<?php

declare(strict_types=1);

namespace App\Services\HourWorked;

use App\Exceptions\TimeEntryException;
use Carbon\Carbon;

trait CalculateTrait
{
    use HourCalculateTrait;
    use NightHoursCalculateTrait;

    /**
     * Calcula la distribución de horas trabajadas
     */
    private function calculate($startTime, $endTime, $plannedHours, ?string $workType): array
    {
        $start = Carbon::parse($startTime);
        $end = Carbon::parse($endTime);

        // Si el tiempo de fin es menor al de inicio, asumimos que es del día siguiente
        if ($end < $start) {
            $end->addDay();
        }

        // Calculamos el total de horas trabajadas
        $hoursWorkedCalculated = $this->diffInHours($start, $end);
        
        // Calculamos las horas nocturnas
        $nightHours = $this->calculateNightHours($startTime, $endTime);
        
        // Las horas diurnas son el total menos las nocturnas
        $dayHours = $hoursWorkedCalculated - $nightHours;

        // Verificar si es festivo y calcular las horas festivas
        $holidayHours = $this->calculateHolidayHours($dayHours, $workType);
      
        // Calcular horas extras
        $regularOvertimeHours = $this->calculateRegularOvertimeHours($dayHours, $plannedHours, $workType);

        // Calcular dia complementario
        $extraShiftHours = $this->calculateExtraShiftOvertime($dayHours, $workType);

        // Calcular las horas normales
        $normalHours = $this->calculateNormalHours(
            $dayHours,
            $regularOvertimeHours,
            $workType
        );

        return [
            'normalHours' => round($normalHours, 2),
            'overtimeHours' => round($regularOvertimeHours + $extraShiftHours, 2),
            'holidayHours' => round($holidayHours, 2),
            'nightHours' => $nightHours
        ];
    }

    /**
     * Calcula la diferencia de horas entre dos tiempos
     */
    private function diffInHours($start, $end): float
    {
        $start = Carbon::parse($start);
        $end = Carbon::parse($end);
        
        // Si la hora de fin es menor que la hora de inicio, asumimos que es del día siguiente
        if ($end < $start) {
            $end->addDay();
        }
        
        return round($start->floatDiffInHours($end), 2);
    }
}
