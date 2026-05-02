<?php

namespace App\Models;

use App\Enums\AppointmentStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Appointment extends Model
{
    use HasFactory;

    protected $fillable = [
        'patient_id',
        'doctor_id',
        'slot_id',
        'scheduled_at',        // datetime
        'duration_minutes',
        'status',
        'reason',
        'notes',               // doctor notes post-consult
        'insurance_type',
        'patient_rating',
        'patient_review',
        'fee',
        'sms_sent',
        'reminder_sent',
    ];

    protected $casts = [
        'scheduled_at'  => 'datetime',
        'status'        => AppointmentStatus::class,
        'sms_sent'      => 'boolean',
        'reminder_sent' => 'boolean',
        'fee'           => 'decimal:2',
    ];

    /* ── Relations ── */

    public function patient(): BelongsTo
    {
        return $this->belongsTo(User::class, 'patient_id');
    }

    public function doctor(): BelongsTo
    {
        return $this->belongsTo(Doctor::class);
    }

    public function slot(): BelongsTo
    {
        return $this->belongsTo(Slot::class);
    }

    /* ── Scopes ── */

    public function scopeUpcoming($query)
    {
        return $query->where('scheduled_at', '>=', now())
                     ->whereIn('status', [AppointmentStatus::Confirmed, AppointmentStatus::Pending]);
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', AppointmentStatus::Completed);
    }

    /* ── Helpers ── */

    public function canBeCancelled(): bool
    {
        return in_array($this->status, [
            AppointmentStatus::Pending,
            AppointmentStatus::Confirmed,
        ]) && $this->scheduled_at->isFuture();
    }
}
