<?php

declare(strict_types=1);

namespace App\Services\Salary;

use App\Models\Employee;
use App\Models\Salary;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class SalaryService implements SalaryServiceInterface
{
    use CalculateSalaryTrait;

    public function execute(string $employeeId, string $date): void
    {

        $employee = Employee::findOrFail($employeeId);

        $prepareDate = $this->prepareDate($date);

        $hourWorkedCollection = $this->prepareHourWorkedCollection($employee, $prepareDate['startOfMonth'], $prepareDate['endOfMonth']);

        $salary = $this->prepareSalary($employeeId, $prepareDate['startOfMonth'], $prepareDate['endOfMonth']);

        $dataSalary = $this->calculateSalary($hourWorkedCollection, $employee);
        var_dump($dataSalary);

        DB::transaction(function () use ($salary, $dataSalary, $employeeId, $prepareDate) {
            $salary ? $this->updateSalary($salary, $dataSalary) : $this->createNewSalary($employeeId, $prepareDate, $dataSalary);
        });

    }

    private function prepareDate($date): array
    {
        $date = new Carbon($date);
        // Primer día del mes
        $startOfMonth = new Carbon($date->copy()->startOfMonth()->toDateString());

        // Último día del mes
        $endOfMonth = new Carbon($date->copy()->endOfMonth()->toDateString()); // Último día del mes

        return ['startOfMonth' => $startOfMonth,
            'endOfMonth' => $endOfMonth];
    }

    private function prepareHourWorkedCollection(Employee $employee, Carbon $startOfMonth, Carbon $endOfMonth): Collection
    {
        $hourSessions = $employee->hourSessions()
            ->whereBetween('date', [$startOfMonth, $endOfMonth])
            ->with('hourWorked')
            ->get();

        return $hourSessions->pluck('hourWorked');

    }

    private function prepareSalary($employeeId, $startOfMonth, $endOfMonth): ?Salary
    {
        return Salary::where('employee_id', $employeeId)
            ->where(function ($query) use ($startOfMonth, $endOfMonth) {
                $query->whereBetween('start_date', [$startOfMonth, $endOfMonth])
                    ->orWhereBetween('end_date', [$startOfMonth, $endOfMonth]);
            })
            ->first();
    }

    private function updateSalary($salary, $dataSalary): void
    {

        $salary->total_normal_hours = $dataSalary['total_normal_hours'];

        $salary->total_overtime_hours = $dataSalary['total_overtime_hours'];

        $salary->total_night_hours = $dataSalary['total_night_hours'];

        $salary->total_holiday_hours = $dataSalary['total_holiday_hours'];

        $salary->total_gross_salary = $dataSalary['gross_salary'];

        $salary->total_net_salary = $dataSalary['net_salary'];

        $salary->save();

    }

    private function createNewSalary(string $employeeId, array $prepareDate, array $dataSalary): void
    {
        Salary::create(
            ['employee_id' => $employeeId,
                'start_date' => $prepareDate['startOfMonth'],
                'end_date' => $prepareDate['endOfMonth'],
                'total_normal_hours' => $dataSalary['total_normal_hours'],
                'total_overtime_hours' => $dataSalary['total_overtime_hours'],
                'total_night_hours' => $dataSalary['total_night_hours'],
                'total_holiday_hours' => $dataSalary['total_holiday_hours'],
                'total_gross_salary' => $dataSalary['gross_salary'],
                'total_net_salary' => $dataSalary['net_salary'],

            ]);
    }
}
