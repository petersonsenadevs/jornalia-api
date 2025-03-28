<?php

declare(strict_types=1);

namespace App\Services\Salary;

use App\DTO\Salary\SalaryDTO;
use App\Exceptions\SalaryNotFoundException;
use App\Models\Salary;
use Carbon\Carbon;

class FindSalaryByMonthService
{
    /**
     * Summary of execute
     *
     * @param  mixed  $month
     * @param  mixed  $year
     *
     * @throws \App\Exceptions\SalaryNotFoundException
     */
    public function execute(string $employeeId, string $month, string $year): array
    {

        $str = "{$year}-{$month}-01";
        $date = new Carbon($str);
        $salary = Salary::where('employee_id', $employeeId)->whereMonth('start_date', $month)->whereYear('start_date', $year)->select('start_date', 'end_date', 'total_normal_hours', 'total_overtime_hours','total_night_hours', 'total_holiday_hours', 'total_gross_salary', 'total_net_salary')->first();
        if (! $salary) {
            throw new SalaryNotFoundException;
        }

        return SalaryDTO::toArray($salary->toArray());
    }
}
