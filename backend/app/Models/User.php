<?php

namespace App\Models;

use App\Enums\UserRole;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'role',
        'insurance_type',
        'avatar',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password'          => 'hashed',
        'role'              => UserRole::class,
    ];

    /* ── Relations ── */

    public function doctorProfile()
    {
        return $this->hasOne(Doctor::class);
    }

    public function appointments()
    {
        return $this->hasMany(Appointment::class, 'patient_id');
    }

    /* ── Role helpers ── */

    public function isPatient(): bool
    {
        return $this->role === UserRole::Patient;
    }

    public function isDoctor(): bool
    {
        return $this->role === UserRole::Doctor;
    }

    public function isAdmin(): bool
    {
        return $this->role === UserRole::Admin;
    }
}
