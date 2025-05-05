<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'name',
        'normal_hourly_rate',
        'overtime_hourly_rate',
        'night_hourly_rate',
        'holiday_hourly_rate',
        'company_name',
        'user_id',
        'irpf',
    ];

    protected $casts = [
        'irpf' => 'float',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function hourSessions()
    {
        return $this->hasMany(HourSession::class);
    }

    public function Salaries()
    {
        return $this->hasMany(Salary::class);
    }
}
