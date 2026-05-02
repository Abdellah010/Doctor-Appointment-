<?php

namespace App\Services;

use App\Models\Doctor;
use App\Models\Slot;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class SlotService
{
    /**
     * Generate slots for a doctor for the next N days.
     * Skips days the doctor is unavailable.
     */
    public function generateForDoctor(Doctor $doctor, int $days = 30): void
    {
        $dayMap = [
            'mon' => 1, 'tue' => 2, 'wed' => 3,
            'thu' => 4, 'fri' => 5, 'sat' => 6, 'sun' => 0,
        ];

        $availableDayNumbers = collect($doctor->available_days)
            ->map(fn ($d) => $dayMap[$d] ?? null)
            ->filter()
            ->values();

        $duration  = $doctor->slot_duration ?? 30;
        $workStart = '09:00';
        $workEnd   = '18:00';

        for ($i = 0; $i < $days; $i++) {
            $date = now()->addDays($i)->toDateString();
            $dow  = now()->addDays($i)->dayOfWeek;

            if (!$availableDayNumbers->contains($dow)) {
                continue;
            }

            $current = Carbon::parse("$date $workStart");
            $end     = Carbon::parse("$date $workEnd");

            while ($current->lt($end)) {
                $slotEnd = $current->copy()->addMinutes($duration);

                // Only create if it doesn't already exist
                Slot::firstOrCreate(
                    [
                        'doctor_id'  => $doctor->id,
                        'date'       => $date,
                        'start_time' => $current->format('H:i'),
                    ],
                    [
                        'end_time'   => $slotEnd->format('H:i'),
                        'is_booked'  => false,
                        'is_blocked' => false,
                    ]
                );

                $current->addMinutes($duration);
            }
        }
    }

    /**
     * Get available slots for a doctor on a given date.
     */
    public function getAvailableSlots(Doctor $doctor, string $date): Collection
    {
        $query = Slot::where('doctor_id', $doctor->id)
            ->where('date', $date)
            ->where('is_booked', false)
            ->where('is_blocked', false);

        // If requested date is today, only show future slots
        if ($date === now()->toDateString()) {
            $query->where('start_time', '>', now()->format('H:i'));
        }

        return $query->orderBy('start_time')->get();
    }

    /**
     * Get dates with any available slots (for calendar display).
     */
    public function getDatesWithSlots(Doctor $doctor, string $month): Collection
    {
        return Slot::where('doctor_id', $doctor->id)
            ->where('is_booked', false)
            ->where('is_blocked', false)
            ->whereRaw("DATE_FORMAT(date, '%Y-%m') = ?", [$month])
            ->where(function ($query) {
                // Future dates
                $query->where('date', '>', now()->toDateString())
                    // OR today but future time
                    ->orWhere(function ($q) {
                        $q->where('date', now()->toDateString())
                          ->where('start_time', '>', now()->format('H:i'));
                    });
            })
            ->pluck('date')
            ->unique()
            ->values();
    }
}
