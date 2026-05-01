<?php

namespace App\Services;

use App\Enums\AppointmentStatus;
use App\Jobs\SendAppointmentSms;
use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\Slot;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class BookingService
{
    public function __construct(
        private readonly SlotService $slotService
    ) {}

    /**
     * Create a confirmed appointment.
     * Wraps the slot lock + appointment creation in a transaction.
     */
    public function book(User $patient, Doctor $doctor, Slot $slot, array $data): Appointment
    {
        if (!$doctor->isVerified()) {
            throw ValidationException::withMessages([
                'doctor' => 'This doctor is not yet verified.',
            ]);
        }

        if (!$slot->isAvailable()) {
            throw ValidationException::withMessages([
                'slot' => 'This time slot is no longer available.',
            ]);
        }

        return DB::transaction(function () use ($patient, $doctor, $slot, $data) {
            // Lock the slot row to prevent race conditions
            $slot = Slot::lockForUpdate()->find($slot->id);

            if (!$slot->isAvailable()) {
                throw ValidationException::withMessages([
                    'slot' => 'Someone just booked this slot. Please choose another.',
                ]);
            }

            $appointment = Appointment::create([
                'patient_id'       => $patient->id,
                'doctor_id'        => $doctor->id,
                'slot_id'          => $slot->id,
                'scheduled_at'     => $slot->date->format('Y-m-d') . ' ' . $slot->start_time,
                'duration_minutes' => $doctor->slot_duration ?? 30,
                'status'           => AppointmentStatus::Pending,
                'reason'           => $data['reason'] ?? null,
                'insurance_type'   => $data['insurance_type'] ?? null,
                'fee'              => $doctor->consultation_fee,
            ]);

            $slot->update(['is_booked' => true]);

            // Dispatch SMS notification in background
           

            return $appointment;
        });
    }

    /**
     * Cancel an appointment and free the slot.
     */
    public function cancel(Appointment $appointment, User $requestedBy): Appointment
    {
        if (!$appointment->canBeCancelled()) {
            throw ValidationException::withMessages([
                'appointment' => 'This appointment cannot be cancelled.',
            ]);
        }

        // Patients can cancel their own; admins can cancel any
        if ($requestedBy->isPatient() && $appointment->patient_id !== $requestedBy->id) {
            abort(403);
        }

        DB::transaction(function () use ($appointment) {
            $appointment->update(['status' => AppointmentStatus::Cancelled]);
            $appointment->slot()->update(['is_booked' => false]);
        });

        return $appointment->fresh();
    }

    /**
     * Doctor accepts a pending appointment request.
     */
    public function accept(Appointment $appointment): Appointment
    {
        $appointment->update(['status' => AppointmentStatus::Confirmed]);
        SendAppointmentSms::dispatch($appointment->fresh());
        return $appointment->fresh();
    }

    /**
     * Doctor declines a pending appointment request.
     */
    public function decline(Appointment $appointment): Appointment
    {
        DB::transaction(function () use ($appointment) {
            $appointment->update(['status' => AppointmentStatus::Declined]);
            $appointment->slot()->update(['is_booked' => false]);
        });

        return $appointment->fresh();
    }

    /**
     * Mark appointment complete and record rating.
     */
    public function complete(Appointment $appointment, ?int $rating = null, ?string $review = null): Appointment
    {
        $appointment->update([
            'status'         => AppointmentStatus::Completed,
            'patient_rating' => $rating,
            'patient_review' => $review,
        ]);

        if ($rating !== null) {
            $appointment->doctor->updateRating($rating);
        }

        return $appointment->fresh();
    }
}
