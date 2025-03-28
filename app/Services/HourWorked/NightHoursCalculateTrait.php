<?php

declare(strict_types=1);

namespace App\Services\HourWorked;

use App\Enums\NightShiftTypeEnum;
use Carbon\Carbon;

trait NightHoursCalculateTrait
{
    /**
     * Calcula las horas nocturnas (22:00-06:00)
     */
    private function calculateNightHours(string $startTime, string $endTime): float
    {
        $start = Carbon::parse($startTime);
        $end = Carbon::parse($endTime);

        // Si el tiempo de fin es menor al de inicio, asumimos que es del día siguiente
        if ($end < $start) {
            $end->addDay();
        }

        $nightHours = 0;

        // Caso 1: Si el inicio es entre 00:00 y 06:00
        if ($start->hour >= 0 && $start->hour < 6) {
            $morningEnd = $start->copy()->setTime(6, 0);
            if ($end <= $morningEnd) {
                // Todo el turno está en la madrugada
                $nightHours = $start->floatDiffInHours($end);
            } else {
                // El turno continúa después de las 06:00
                $nightHours = $start->floatDiffInHours($morningEnd);
            }
        }
        // Caso 2: Si el inicio es antes o en las 22:00
        elseif ($start->hour < 22) {
            $nightStart = $start->copy()->setTime(22, 0);
            $nextMorning = $start->copy()->addDay()->setTime(6, 0);

            if ($end > $nightStart) {
                if ($end <= $nextMorning) {
                    // Turno termina antes de las 06:00 del día siguiente
                    $nightHours = $nightStart->floatDiffInHours($end);
                } else {
                    // Turno continúa después de las 06:00 del día siguiente
                    $nightHours = $nightStart->floatDiffInHours($nextMorning);
                }
            }
        }
        // Caso 3: Si el inicio es después de las 22:00
        else {
            $nextMorning = $start->copy()->addDay()->setTime(6, 0);
            if ($end <= $nextMorning) {
                // Turno termina antes de las 06:00 del día siguiente
                $nightHours = $start->floatDiffInHours($end);
            } else {
                // Turno continúa después de las 06:00 del día siguiente
                $nightHours = $start->floatDiffInHours($nextMorning);
            }
        }

        return round($nightHours, 2);
    }
} 