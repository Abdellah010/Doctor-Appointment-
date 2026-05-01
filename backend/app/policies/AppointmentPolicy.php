<?php

namespace App\Policies;

use App\Models\Appointment;
use App\Models\User;

class AppointmentPolicy
{
    /**
     * Determine if the user can manage (accept/decline) the appointment.
     */
    public function manage(User $user, Appointment $appointment): bool
    {
        $doctor = $user->doctorProfile;

        if (!$doctor) {
            return false;
        }

        return $user->isDoctor() && (int) $doctor->id === (int) $appointment->doctor_id;
    }
}