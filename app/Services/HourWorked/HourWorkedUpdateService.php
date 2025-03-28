<?php

declare(strict_types=1);

namespace App\Services\HourWorked;

use App\Exceptions\HourWorkedNotFoundException;
use App\Models\HourWorked;
use App\Traits\ValidateTimeEntry;
use Illuminate\Support\Facades\DB;

class HourWorkedUpdateService
{
    use CalculateTrait;
    use ValidateTimeEntry;

    /**
     * Summary of execute
     *
     * @throws \App\Exceptions\HourWorkedNotFoundException
     */
    public function execute(string $hourSessionId, ?string $startTime, ?string $endTime, ?int $plannedHours, ?string $workType): void
    {

        $hourWorkeed = HourWorked::where('hour_session_id', $hourSessionId)->first();
        if (! $hourWorkeed) {
            throw new HourWorkedNotFoundException;
        }

        $this->validateTimeEntry($startTime, $endTime);

        $hoursList = $this->calculate($startTime, $endTime, $plannedHours, $workType);

        DB::transaction(function () use ($hourWorkeed, $hoursList) {
            if ($hourWorkeed->normal_hours != null) {
                $hourWorkeed->normal_hours = $hoursList['normalHours'];
            }
            if ($hourWorkeed->overtime_hours != null) {
                $hourWorkeed->overtime_hours = $hoursList['overtimeHours'];
            }
            if ($hourWorkeed->holiday_hours != null) {
                $hourWorkeed->holiday_hours = $hoursList['holidayHours'];
            }
            if ($hourWorkeed->night_hours != null) {
                $hourWorkeed->night_hours = $hoursList['nightHours'];
            }
            $hourWorkeed->save();
        });

    }
}
