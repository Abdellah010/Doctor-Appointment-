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
            ->filter(fn ($d) => $d !== null)
            ->values();

        // 1. Cleanup: Remove unbooked slots on days the doctor is now unavailable
        Slot::where('doctor_id', $doctor->id)
            ->where('is_booked', false)
            ->where('date', '>=', now()->toDateString())
            ->get()
            ->filter(function ($slot) use ($availableDayNumbers) {
                return !$availableDayNumbers->contains(Carbon::parse($slot->date)->dayOfWeek);
            })
            ->each->delete();

        $duration  = $doctor->slot_duration ?? 30;
        $workStart = '09:00';
        $workEnd   = '18:00';

        // 2. Generate/Update slots for available days
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
        return Slot::where('doctor_id', $doctor->id)
            ->where('date', $date)
            ->where('is_booked', false)
            ->where('is_blocked', false)
            ->orderBy('start_time')
            ->get();
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
            ->pluck('date')
            ->unique()
            ->values();
    }
}
