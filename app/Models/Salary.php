<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Salary extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = ['employee_id', 'start_date', 'end_date', 'total_normal_hours', 'total_overtime_hours','total_night_hours', 'total_holiday_hours', 'total_gross_salary', 'total_net_salary'];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }
}
