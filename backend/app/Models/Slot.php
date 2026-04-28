<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Slot extends Model
{
    protected $fillable = [
        'doctor_id',
        'date',          // YYYY-MM-DD
        'start_time',    // HH:MM
        'end_time',
        'is_booked',
        'is_blocked',    // doctor manually blocked
    ];

    protected $casts = [
        'date'       => 'date',
        'is_booked'  => 'boolean',
        'is_blocked' => 'boolean',
    ];

    public function doctor(): BelongsTo
    {
        return $this->belongsTo(Doctor::class);
    }

    public function appointment()
    {
        return $this->hasOne(Appointment::class);
    }

    public function isAvailable(): bool
    {
        return !$this->is_booked && !$this->is_blocked;
    }
}
