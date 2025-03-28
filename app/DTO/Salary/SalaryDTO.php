<?php

namespace App\DTO\Salary;

use App\DTO\DTOInterface;
use Illuminate\Database\Eloquent\Model;

final readonly class SalaryDTO implements DTOInterface
{
    public function __construct(
        public string $startDate,
        public string $endDate,
        public float $total_normal_hours,
        public float $total_overtime_hours,
        public float $total_holiday_hours,
        public float $total_night_hours,
        public float $total_gross_salary,
        public float $total_net_salary) {}

    public static function fromModel(Model $salary)
    {
        return new self(
            $salary->start_date,
            $salary->end_date,
            $salary->total_normal_hours,
            $salary->total_overtime_hours,
            $salary->total_holiday_hours,
            $salary->total_night_hours,
            
            $salary->total_gross_salary,
            $salary->total_net_salary
        );
    }

    public static function toArray(array $data)
    {
        return [
            'total_normal_hours' => $data['total_normal_hours'],
            'total_overtime_hours' => $data['total_overtime_hours'],
            'total_night_hours' => $data['total_night_hours'],
            'total_holiday_hours' => $data['total_holiday_hours'],
            'total_gross_salary' => $data['total_gross_salary'],
            'total_net_salary' => $data['total_net_salary'],
        ];
    }
}
