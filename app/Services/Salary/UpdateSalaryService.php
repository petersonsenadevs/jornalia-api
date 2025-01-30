<?php

declare(strict_types=1);

namespace App\Services\Salary;

use App\Models\Salary;
use Illuminate\Support\Facades\DB;

class UpdateSalaryService
{
    public function execute(Salary $salary, array $dataSalary): void
    {
        DB::transaction(function () use ($salary, $dataSalary) {
            $this->updateSalaryFields($salary, $dataSalary);
        });
    }

    private function updateSalaryFields(Salary $salary, array $dataSalary): void
    {
        $salary->total_normal_hours = $dataSalary['total_normal_hours'];
        $salary->total_overtime_hours = $dataSalary['total_overtime_hours'];
        $salary->total_holiday_hours = $dataSalary['total_holiday_hours'];
        $salary->total_gross_salary = $dataSalary['gross_salary'];
        $salary->total_net_salary = $dataSalary['net_salary'];

        $salary->save();
    }
}
