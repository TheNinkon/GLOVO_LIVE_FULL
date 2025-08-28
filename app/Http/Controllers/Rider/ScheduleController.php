<?php

namespace App\Http\Controllers\Rider;

use App\Http\Controllers\Controller;
use App\Models\Forecast;
use App\Models\Rider;
use App\Models\Schedule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;
use Carbon\CarbonPeriod;

class ScheduleController extends Controller
{
    /**
     * El constructor ya no necesita ningún servicio adicional.
     */
    public function __construct() {}

    /**
     * Muestra la vista del horario, cargando todos los datos reales de la semana.
     */
    public function index($week = null): View
    {
        Carbon::setLocale(config('app.locale'));
        $rider = Auth::guard('rider')->user();
        try {
            $startOfWeek = $week ? Carbon::parse($week)->startOfWeek(Carbon::MONDAY) : Carbon::now()->startOfWeek(Carbon::MONDAY);
        } catch (\Exception $e) {
            $startOfWeek = Carbon::now()->startOfWeek(Carbon::MONDAY);
        }

        $forecast = Forecast::where('city', $rider->city)
            ->where('week_start_date', $startOfWeek)
            ->first();

        $navData = $this->buildNavigation($rider->city, $startOfWeek);
        $endOfWeek = $startOfWeek->copy()->endOfWeek(Carbon::SUNDAY);

        // Lógica para el día activo por defecto: lunes, a menos que sea otro día de la semana
        $defaultDay = $startOfWeek->format('Y-m-d');
        if (Carbon::now()->between($startOfWeek, $endOfWeek)) {
            $defaultDay = Carbon::now()->format('Y-m-d');
        }

        if (!$forecast) {
            return view('content.rider.schedule.index', [
                'rider' => $rider,
                'prevWeek' => $navData['prev'],
                'nextWeek' => $navData['next'],
                'startOfWeek' => $startOfWeek,
                'endOfWeek' => $endOfWeek,
                'scheduleData' => null,
                'weekDates' => $this->generateWeekDates($startOfWeek),
                'deadline' => null,
                'defaultDay' => $defaultDay,
                'summary' => [
                     'contractedHours' => $rider->weekly_contract_hours,
                     'reservedHours' => 0,
                     'wildcards' => $rider->edits_remaining,
                ]
            ]);
        }

        $scheduleData = $this->prepareScheduleDataForView($forecast, $rider->id);
        $myHoursCount = $scheduleData['mySchedules']->count() * 0.5;

        return view('content.rider.schedule.index', [
            'rider' => $rider,
            'weekDates' => array_values($scheduleData['days']),
            'scheduleData' => $scheduleData['days'],
            'startOfWeek' => $startOfWeek,
            'endOfWeek' => $endOfWeek,
            'prevWeek' => $navData['prev'],
            'nextWeek' => $navData['next'],
            'deadline' => $forecast->booking_deadline,
            'defaultDay' => $defaultDay,
            'summary' => [
                'contractedHours' => $rider->weekly_contract_hours,
                'reservedHours' => $myHoursCount,
                'wildcards' => $rider->edits_remaining,
            ]
        ]);
    }

    /**
     * Procesa la selección de un único slot.
     */
    public function selectSlot(Request $request): JsonResponse
    {
        return $this->toggleSlot($request, 'select');
    }

    /**
     * Procesa la deselección de un único slot.
     */
    public function deselectSlot(Request $request): JsonResponse
    {
        $rider = Auth::guard('rider')->user();
        if ($rider->edits_remaining <= 0) {
            return response()->json(['message' => 'Has agotado tus comodines para cancelar.'], 403);
        }
        $response = $this->toggleSlot($request, 'deselect');
        if ($response->getStatusCode() === 200) {
            $rider->decrement('edits_remaining');
        }
        $data = $response->getData(true);
        $data['edits_remaining'] = $rider->fresh()->edits_remaining;
        $response->setData($data);
        return $response;
    }

    /**
     * Lógica de negocio central para validar y ejecutar la selección/deselección de un solo slot.
     */
    private function toggleSlot(Request $request, string $action): JsonResponse
    {
        $rider = Auth::guard('rider')->user();
        $slotIdentifier = $request->input('slot');
        if (!$slotIdentifier) return response()->json(['message' => 'Slot no proporcionado.'], 400);

        [$date, $time] = explode('_', $slotIdentifier);
        $startOfWeek = Carbon::parse($date)->startOfWeek(Carbon::MONDAY);
        $forecast = Forecast::where('city', $rider->city)->where('week_start_date', $startOfWeek)->firstOrFail();

        if (!empty($forecast->booking_deadline) && Carbon::now()->gt($forecast->booking_deadline)) {
            return response()->json(['message' => 'El periodo para modificar este horario ha finalizado.'], 403);
        }

        return DB::transaction(function () use ($rider, $forecast, $date, $time, $action) {
            // Reglas de negocio para un solo slot
            if ($action === 'select') {
                $currentWeeklyHours = Schedule::where('rider_id', $rider->id)->where('forecast_id', $forecast->id)->count() * 0.5;
                if ($rider->weekly_contract_hours > 0 && ($currentWeeklyHours + 0.5) > $rider->weekly_contract_hours) {
                    return response()->json(['message' => 'Superas tu límite de horas de contrato semanal.'], 422);
                }
                $currentDailyHours = Schedule::where('rider_id', $rider->id)->where('forecast_id', $forecast->id)->where('slot_date', $date)->count() * 0.5;
                if (($currentDailyHours + 0.5) > 8) {
                    return response()->json(['message' => 'No puedes trabajar más de 8 horas al día.'], 422);
                }
            }

            $dayKey  = strtolower(Carbon::parse($date)->format('D'));
            $timeKey = Carbon::parse($time)->format('H:i');
            $demand  = $forecast->forecast_data[$dayKey][$timeKey] ?? 0;
            $bookedCount = Schedule::where('forecast_id', $forecast->id)->where('slot_date', $date)->where('slot_time', $time)->count();

            if ($action === 'select') {
                if ($bookedCount >= $demand) return response()->json(['message' => 'Este turno ya está completo.'], 409);
                Schedule::create(['rider_id' => $rider->id, 'forecast_id' => $forecast->id, 'slot_date' => $date, 'slot_time' => $time, 'city' => $rider->city]);
            } else {
                $deleted = Schedule::where('rider_id', $rider->id)->where('forecast_id', $forecast->id)->where('slot_date', $date)->where('slot_time', $time)->delete();
                if ($deleted === 0) return response()->json(['message' => 'No tenías reservado este turno.'], 404);
            }

            $totalHours = Schedule::where('rider_id', $rider->id)->where('forecast_id', $forecast->id)->count() * 0.5;
            // Retornar información necesaria para actualizar la UI sin recargar
            return response()->json([
                'message' => 'Horario actualizado.',
                'total_hours' => $totalHours,
                'edits_remaining' => $rider->fresh()->edits_remaining,
            ]);
        });
    }

    /**
     * Prepara los datos de todos los slots de una semana para la vista.
     */
    private function prepareScheduleDataForView(Forecast $forecast, int $riderId): array
    {
        $startOfWeek = $forecast->week_start_date;
        $mySchedules = Schedule::where('forecast_id', $forecast->id)->where('rider_id', $riderId)->get();
        $bookedSchedules = Schedule::where('forecast_id', $forecast->id)
            ->select('slot_date', 'slot_time', DB::raw('count(*) as total'))
            ->groupBy('slot_date', 'slot_time')
            ->get()->keyBy(fn ($item) => $item->slot_date->format('Y-m-d') . '_' . $item->slot_time->format('H:i:s'));

        $days = [];
        for ($i = 0; $i < 7; $i++) {
            $currentDate = $startOfWeek->clone()->addDays($i);
            $dayKey = strtolower($currentDate->format('D'));
            $slots = [];
            for ($j = 0; $j < 48; $j++) {
                $currentTime = Carbon::createFromTimeString('00:00')->addMinutes($j * 30);
                $timeKey = $currentTime->format('H:i');
                $slotIdentifier = $currentDate->format('Y-m-d') . '_' . $currentTime->format('H:i:s');
                $demand = $forecast->forecast_data[$dayKey][$timeKey] ?? 0;
                $booked = $bookedSchedules[$slotIdentifier]['total'] ?? 0;
                $isMine = $mySchedules->contains(fn ($s) => $s->slot_date->format('Y-m-d') === $currentDate->format('Y-m-d') && $s->slot_time->format('H:i:s') === $currentTime->format('H:i:s'));
                $isFull = $booked >= $demand;
                $status = 'unavailable';
                if ($isMine) $status = 'mine';
                elseif ($demand > 0 && !$isFull) $status = 'available';
                $slots[] = ['time' => $currentTime->format('H:i'), 'identifier' => $slotIdentifier, 'status' => $status];
            }
            $days[$currentDate->format('Y-m-d')] = [
                'full' => $currentDate->format('Y-m-d'),
                'dayName' => strtoupper($currentDate->translatedFormat('D')), 'dayNum' => $currentDate->format('j'),
                'isToday' => $currentDate->isToday(), 'slots' => $slots,
            ];
        }
        return ['days' => $days, 'mySchedules' => $mySchedules];
    }

    /**
     * Construye la navegación (semanas anterior/siguiente).
     */
    private function buildNavigation(string $city, Carbon $startOfWeek): array
    {
        $prevWeek = $startOfWeek->clone()->subWeek();
        $nextWeek = $startOfWeek->clone()->addWeek();
        return [
            'prev' => Forecast::where('city', $city)->where('week_start_date', $prevWeek)->exists()
                ? route('rider.schedule.index', ['week' => $prevWeek->format('Y-m-d')])
                : null,
            'next' => Forecast::where('city', $city)->where('week_start_date', $nextWeek)->exists()
                ? route('rider.schedule.index', ['week' => $nextWeek->format('Y-m-d')])
                : null,
        ];
    }

    /**
     * Genera un array simple de fechas para el selector de días.
     */
    private function generateWeekDates(Carbon $startOfWeek): array
    {
        return collect(CarbonPeriod::create($startOfWeek, $startOfWeek->copy()->endOfWeek(Carbon::SUNDAY)))->map(fn ($date) => [
            'full' => $date->format('Y-m-d'),
            'dayName' => strtoupper($date->translatedFormat('D')),
            'dayNum' => $date->format('j'),
            'isToday' => $date->isToday(),
        ])->toArray();
    }
}
