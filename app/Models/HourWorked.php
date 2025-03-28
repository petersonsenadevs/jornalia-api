<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HourWorked extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'hour_session_id',
        'normal_hours',
        'overtime_hours',
        'holiday_hours',
        'night_hours',
    ];

    protected $table = 'hours_worked';

    public function hourSession(): BelongsTo
    {
        return $this->belongsTo(HourSession::class);
    }
}
