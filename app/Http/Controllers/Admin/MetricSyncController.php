<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Metric;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;
use Illuminate\Support\Str;

class MetricSyncController extends Controller
{
    // Asegúrate de que la hoja sea pública (o cualquiera con enlace - lector)
    private $csvUrl = 'https://docs.google.com/spreadsheets/d/1JBMROzwrJCPmpZK99XtY3iD619jxbZh-EzF9RgbbK-I/export?format=csv&gid=2008014102';

    public function sync()
    {
        Log::info("🚀 Iniciando sincronización de métricas...");
        $result = ['processed' => 0, 'imported' => 0, 'errors' => []];

        try {
            $response = Http::withHeaders([
                'Accept' => 'text/csv,*/*;q=0.8'
            ])->get($this->csvUrl);

            if (!$response->ok()) {
                return response()->json(['error' => '❌ No se pudo obtener el CSV (HTTP '.$response->status().').'], 500);
            }

            // Guardar a archivo temporal para usar fgetcsv correctamente
            $tmp = tmpfile();
            $meta = stream_get_meta_data($tmp);
            fwrite($tmp, $response->body());
            rewind($tmp);

            $file = new \SplFileObject($meta['uri'], 'r');
            $file->setFlags(\SplFileObject::READ_CSV | \SplFileObject::SKIP_EMPTY);
            $file->setCsvControl(','); // Google exporta con coma

            // Leer encabezados y normalizarlos
            $rawHeaders = $file->fgetcsv();
            if ($rawHeaders === null || $rawHeaders === false) {
                return response()->json(['error' => '❌ CSV sin encabezados.'], 500);
            }

            $normalize = function ($h) {
                $h = is_string($h) ? $h : '';
                // Quitar BOM
                $h = preg_replace('/^\xEF\xBB\xBF/', '', $h);
                $h = trim(mb_strtolower($h));
                // Reemplazar caracteres raros
                $h = str_replace(['%', 'º', '°'], ['pct', '', ''], $h);
                // Normalizar espacios / tildes
                $h = Str::of($h)
                    ->replace(['/', '\\'], ' ')
                    ->replaceMatches('/\s+/', ' ')
                    ->replace(['á','é','í','ó','ú','ñ'], ['a','e','i','o','u','n'])
                    ->trim()
                    ->toString();
                return $h;
            };

            $headers = array_map($normalize, $rawHeaders);

            // Mapeos de seguridad por si los nombres vienen “creativos”
            $alias = [
                'courier id'                 => 'courier_id',
                'transport'                  => 'transport',
                'start date'                 => 'start_date',
                'city code'                  => 'city_code',
                'delivered orders'           => 'delivered_orders',
                'pct canceled orders'        => 'pct_canceled_orders',
                'pct reassignments'          => 'pct_reassignments',
                'pctno show'                 => 'pct_no_show',   // por si venía “%no show”
                'pct no show'                => 'pct_no_show',
                'h ras'                      => 'hours',         // por si venía “h/ras”
                'horas'                      => 'hours',
                'ratio de entrga'            => 'ratio_entrega', // por si venía con typo
                'ratio de entrega'           => 'ratio_entrega',
                'cdt (min)=< 20min'          => 'cdt_le_20',
                'cdt (min)<= 20min'          => 'cdt_le_20',
            ];

            $keyedHeaders = [];
            foreach ($headers as $h) {
                $keyedHeaders[] = $alias[$h] ?? $h;
            }

            // Para upsert
            $rowsForUpsert = [];

            // Última fecha ya importada (optimización)
            $lastDate = Metric::max('fecha'); // Y-m-d

            while (!$file->eof()) {
                $row = $file->fgetcsv();
                if ($row === null || $row === false) continue;
                // Evitar filas vacías
                if (count(array_filter($row, fn($v) => $v !== null && $v !== '')) === 0) continue;

                // Alinear largo
                if (count($row) < count($keyedHeaders)) {
                    // Rellenar columnas faltantes
                    $row = array_pad($row, count($keyedHeaders), null);
                }

                $data = [];
                foreach ($keyedHeaders as $i => $key) {
                    $data[$key] = isset($row[$i]) ? trim((string)$row[$i]) : null;
                }

                if (empty($data['courier_id'] ?? null)) {
                    continue; // sin id no sirve
                }

                // Parse fecha
                try {
                    $date = null;
                    if (!empty($data['start_date'])) {
                        // suele venir d/m/Y
                        $date = Carbon::createFromFormat('d/m/Y', $data['start_date']);
                    }
                    if (!$date) continue;
                } catch (\Throwable $e) {
                    continue;
                }

                if ($lastDate && $date->lte(Carbon::parse($lastDate))) {
                    // ya lo tenemos o es más antiguo
                    continue;
                }

                $toFloat = function ($v) {
                    if ($v === null || $v === '') return 0.0;
                    // quitar %, guiones bajos, espacios
                    $v = str_replace(['%', '_', ' '], '', $v);
                    // cambiar coma decimal a punto
                    $v = str_replace(',', '.', $v);
                    // algunas celdas pueden llevar “-” para 0
                    if ($v === '-' ) $v = '0';
                    return is_numeric($v) ? (float)$v : 0.0;
                };

                $rowsForUpsert[] = [
                    'courier_id'        => $data['courier_id'],
                    'transport'         => $data['transport'] ?? null,
                    'fecha'             => $date->format('Y-m-d'),
                    'ciudad'            => $data['city_code'] ?? null,
                    'pedidos_entregados'=> $toFloat($data['delivered_orders'] ?? 0),
                    'cancelados'        => $toFloat($data['pct_canceled_orders'] ?? 0),
                    'reasignaciones'    => $toFloat($data['pct_reassignments'] ?? 0),
                    'no_show'           => $toFloat($data['pct_no_show'] ?? 0),
                    'horas'             => $toFloat($data['hours'] ?? 0),
                    'ratio_entrega'     => $toFloat($data['ratio_entrega'] ?? 0),
                    'tiempo_promedio'   => $toFloat($data['cdt_le_20'] ?? 0),
                    'created_at'        => now(),
                    'updated_at'        => now(),
                ];
                $result['processed']++;
            }

            // Cerrar tmp
            fclose($tmp);

            if (!empty($rowsForUpsert)) {
                // Asegúrate de tener un índice único en (courier_id, fecha)
                // Schema::table('metrics', function (Blueprint $t){ $t->unique(['courier_id','fecha']); });
                Metric::upsert(
                    $rowsForUpsert,
                    ['courier_id', 'fecha'], // keys
                    [
                        'transport','ciudad','pedidos_entregados','cancelados','reasignaciones',
                        'no_show','horas','ratio_entrega','tiempo_promedio','updated_at'
                    ]
                );
                $result['imported'] = count($rowsForUpsert);
            }

            return response()->json([
                'success' => '✅ Sincronización completada.',
                'procesados' => $result['processed'],
                'nuevos_o_actualizados' => $result['imported']
            ]);
        } catch (\Throwable $e) {
            Log::error("Error en sincronización: " . $e->getMessage());
            return response()->json(['error' => 'Error en sincronización: ' . $e->getMessage()], 500);
        }
    }
}
