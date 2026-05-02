<?php

namespace App\Services;

use App\Enums\DoctorStatus;
use App\Models\Doctor;
use App\Notifications\DoctorApprovedNotification;
use App\Notifications\DoctorRejectedNotification;
use App\Services\SlotService;

class VerificationService
{
    public function __construct(
        private readonly SlotService $slotService
    ) {}

    /**
     * Approve a doctor and generate their initial slots.
     */
    public function approve(Doctor $doctor): Doctor
    {
        $doctor->update([
            'status'           => DoctorStatus::Verified,
            'rejection_reason' => null,
        ]);

        // Generate 30 days of slots on approval
        $this->slotService->generateForDoctor($doctor, 30);

        // Notify the doctor
        $doctor->user->notify(new DoctorApprovedNotification($doctor));

        return $doctor->fresh();
    }

    /**
     * Reject a doctor with a specific reason.
     */
    public function reject(Doctor $doctor, string $reason, ?string $notes = null): Doctor
    {
        $doctor->update([
            'status'           => DoctorStatus::Rejected,
            'rejection_reason' => $reason,
        ]);

        $doctor->user->notify(new DoctorRejectedNotification($doctor, $reason, $notes));

        return $doctor->fresh();
    }

    /**
     * Get pending verification queue.
     */
    public function getPendingDoctors()
    {
        return Doctor::with('user')
            ->where('status', DoctorStatus::Pending)
            ->orderBy('created_at', 'asc')
            ->get();
    }
}
