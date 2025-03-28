<?php

declare(strict_types=1);

namespace App\Services\HourWorked;

use App\Models\HourWorked;
use App\Traits\ValidateTimeEntry;
use Illuminate\Support\Facades\DB;

class HourWorkedEntryService
{
    use CalculateTrait;
    use ValidateTimeEntry;

    /**
     * Summary of execute
     */
    public function execute(string $hourSessionId, string $startTime, string $endTime, int $plannedHours, string $workType): void
    {

        // Validar la entrada de tiempo
        $this->validateTimeEntry($startTime, $endTime);
        
        $hoursList = $this->calculate($startTime, $endTime, $plannedHours, $workType);
        $night_hours = $hoursList['nightHours'];

        DB::transaction(function () use ($hourSessionId, $hoursList, $workType, $night_hours) {
            HourWorked::create([
                'hour_session_id' => $hourSessionId,
                'normal_hours' => $hoursList['normalHours'],
                'overtime_hours' => $hoursList['overtimeHours'],
                'holiday_hours' => $hoursList['holidayHours'],
                'night_hours' => $night_hours,
            ]);
        });

    }
}
