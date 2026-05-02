<?php

namespace App\Models;

use App\Enums\DoctorStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Doctor extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'specialty',
        'city',
        'license_number',
        'consultation_fee',
        'status',
        'rejection_reason',
        'bio',
        'available_days',   // JSON: ["mon","tue","thu","fri"]
        'slot_duration',    // minutes: 30
        'rating',
        'rating_count',
        'accepts_cnss',
        'accepts_ramed',
        'accepts_private',
    ];

    protected $casts = [
        'status'         => DoctorStatus::class,
        'available_days' => 'array',
        'accepts_cnss'   => 'boolean',
        'accepts_ramed'  => 'boolean',
        'accepts_private'=> 'boolean',
        'rating'         => 'float',
    ];

    /* ── Relations ── */

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class);
    }

    public function slots(): HasMany
    {
        return $this->hasMany(Slot::class);
    }

    /* ── Scopes ── */

    public function scopeVerified($query)
    {
        return $query->where('status', DoctorStatus::Verified);
    }

    public function scopeAvailableToday($query)
    {
        $day = strtolower(now()->format('D'));  // "mon", "tue" …
        return $query->whereJsonContains('available_days', $day);
    }

    /* ── Helpers ── */

    public function isVerified(): bool
    {
        return $this->status === DoctorStatus::Verified;
    }

    public function updateRating(float $newRating): void
    {
        $total = ($this->rating * $this->rating_count) + $newRating;
        $this->rating_count++;
        $this->rating = round($total / $this->rating_count, 2);
        $this->save();
    }
}
