<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Forecast;
use App\Models\Rider;
use App\Models\Schedule;
use App\Models\Account;
use Carbon\Carbon;
use Illuminate\View\View;
use Illuminate\Support\Collection;
use Illuminate\Http\Request;

class RiderStatusController extends Controller
{
    public function index(Request $request): View
    {
        Carbon::setLocale(config('app.locale'));

        $availableCities = Forecast::distinct()->pluck('city')->sort();
        $selectedCity = $request->input('city') ?? $availableCities->first();

        try {
            $startOfWeek = $request->input('week') ? Carbon::parse($request->input('week'))->startOfWeek(Carbon::MONDAY) : Carbon::now()->startOfWeek(Carbon::MONDAY);
        } catch (\Exception $e) {
            $startOfWeek = Carbon::now()->startOfWeek(Carbon::MONDAY);
        }
        $endOfWeek = $startOfWeek->copy()->endOfWeek(Carbon::SUNDAY);

        $nav = [
            'prev' => route('admin.rider-status.index', ['city' => $selectedCity, 'week' => $startOfWeek->clone()->subWeek()->format('Y-m-d')]),
            'next' => route('admin.rider-status.index', ['city' => $selectedCity, 'week' => $startOfWeek->clone()->addWeek()->format('Y-m-d')]),
            'current' => $startOfWeek->translatedFormat('j M') . ' - ' . $endOfWeek->translatedFormat('j M, Y'),
        ];

        $riders = Rider::where('city', $selectedCity)
            ->where('status', 'active')
            ->with(['schedules' => function ($query) use ($startOfWeek, $endOfWeek) {
                $query->whereBetween('slot_date', [$startOfWeek, $endOfWeek])->orderBy('slot_date')->orderBy('slot_time');
            }])
            ->get();

        $riders->each(function ($rider) use ($startOfWeek, $endOfWeek) {
            $assignment = $rider->assignments()
                ->where('start_at', '<=', $endOfWeek)
                ->where(function ($query) use ($startOfWeek) {
                    $query->where('end_at', '>=', $startOfWeek)
                          ->orWhereNull('end_at');
                })
                ->with('account')
                ->first();

            $rider->reserved_hours = $rider->schedules->count() * 0.5;
            $rider->formatted_schedule = $this->formatScheduleForCopying(
                $rider->schedules,
                $assignment ? $assignment->account : null
            );
        });

        // La vista siempre recibirá estas variables para evitar errores de variable indefinida.
        return view('content.admin.rider-status.index', compact('riders', 'selectedCity', 'nav', 'availableCities', 'startOfWeek'));
    }

    /**
     * Formatea el horario en el formato específico, AGRUPANDO bloques de horas contiguas.
     */
    private function formatScheduleForCopying(Collection $schedules, ?Account $account): string
    {
        if ($schedules->isEmpty()) {
            return "Sin horas reservadas.";
        }
        if (!$account) {
            return "Sin cuenta asignada para esta semana.";
        }

        $scheduleByDay = $schedules->groupBy(fn($s) => $s->slot_date->format('Y-m-d'));
        $finalTextLines = [];

        foreach ($scheduleByDay->sortKeys() as $date => $slots) {
            $slots = $slots->sortBy(fn($s) => $s->slot_time);
            $currentBlock = null;

            foreach ($slots as $slot) {
                $slotTime = Carbon::parse($slot->slot_time);
                $slotEndTime = $slotTime->copy()->addMinutes(30);

                if ($currentBlock === null || $slotTime->diffInMinutes(Carbon::parse($currentBlock['end_time'])) > 0) {
                    if ($currentBlock !== null) {
                        $formattedDate = Carbon::parse($currentBlock['date'])->format('d/m/Y');
                        $finalTextLines[] = implode("\t", [
                            $account->city,
                            $account->courier_id,
                            $formattedDate,
                            Carbon::parse($currentBlock['start_time'])->format('H:i'),
                            Carbon::parse($currentBlock['end_time'])->format('H:i'),
                            'BOOK'
                        ]);
                    }

                    $currentBlock = [
                        'date' => $slot->slot_date->format('Y-m-d'),
                        'start_time' => $slot->slot_time->format('H:i'),
                        'end_time' => $slotEndTime->format('H:i'),
                    ];
                } else {
                    $currentBlock['end_time'] = $slotEndTime->format('H:i');
                }
            }
            if ($currentBlock !== null) {
                $formattedDate = Carbon::parse($currentBlock['date'])->format('d/m/Y');
                $finalTextLines[] = implode("\t", [
                    $account->city,
                    $account->courier_id,
                    $formattedDate,
                    Carbon::parse($currentBlock['start_time'])->format('H:i'),
                    Carbon::parse($currentBlock['end_time'])->format('H:i'),
                    'BOOK'
                ]);
            }
        }
        return implode("\n", $finalTextLines);
    }
}
