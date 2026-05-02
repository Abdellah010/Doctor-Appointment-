<?php

namespace App\Http\Controllers;

use App\Enums\AppointmentStatus;
use App\Models\Appointment;
use App\Services\SlotService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DoctorDashboardController extends Controller
{
    public function __construct(private readonly SlotService $slotService) {}

    public function index(Request $request)
    {
        $doctor = $request->user()->doctorProfile;

        // This week's appointments for the calendar grid
        $weekStart = now()->startOfWeek();
        $weekEnd   = now()->endOfWeek();

        $appointments = Appointment::with('patient')
            ->where('doctor_id', $doctor->id)
            ->whereBetween('scheduled_at', [$weekStart, $weekEnd])
            ->whereNotIn('status', [AppointmentStatus::Cancelled, AppointmentStatus::Declined])
            ->orderBy('scheduled_at')
            ->get();

        $pending = Appointment::with('patient')
            ->where('doctor_id', $doctor->id)
            ->where('status', AppointmentStatus::Pending)
            ->orderBy('scheduled_at')
            ->get();

        // Weekly activity for the chart (last 7 days)
        $weeklyActivity = collect(range(6, 0))->map(function ($daysAgo) use ($doctor) {
            $date = now()->subDays($daysAgo);
            return [
                'label' => $date->format('D'),
                'count' => Appointment::where('doctor_id', $doctor->id)
                    ->whereDate('created_at', $date)
                    ->count(),
            ];
        })->values()->all();

        return Inertia::render('Doctor/Dashboard', [
            'doctor'         => $doctor->load('user'),
            'appointments'   => $appointments,
            'pending'        => $pending,
            'weeklyActivity' => $weeklyActivity,
            'stats'          => [
                'today_count'  => Appointment::where('doctor_id', $doctor->id)
                    ->whereDate('scheduled_at', today())
                    ->whereNotIn('status', [AppointmentStatus::Cancelled, AppointmentStatus::Declined])
                    ->count(),
                'month_count'  => Appointment::where('doctor_id', $doctor->id)
                    ->whereMonth('scheduled_at', now()->month)
                    ->whereIn('status', [AppointmentStatus::Confirmed, AppointmentStatus::Completed])
                    ->count(),
                'revenue'      => Appointment::where('doctor_id', $doctor->id)
                    ->whereMonth('scheduled_at', now()->month)
                    ->whereIn('status', [AppointmentStatus::Confirmed, AppointmentStatus::Completed])
                    ->sum('fee'),
            ],
        ]);
    }

    public function updateProfile(Request $request)
    {
        $data = $request->validate([
            'bio'   => 'nullable|string|max:500',
            'phone' => 'nullable|string|max:25|unique:users,phone,' . $request->user()->id,
        ], [
            'phone.unique' => 'This phone number is already used by another user.',
        ]);

        $request->user()->update(['phone' => $data['phone'] ?? null]);
        if (isset($data['bio'])) {
            $request->user()->doctorProfile->update(['bio' => $data['bio']]);
        }

        return back()->with('success', 'Profile updated.');
    }

    public function updateAvailability(Request $request)
    {
        $data = $request->validate([
            'consultation_fee' => 'required|numeric|min:0|max:99999',
            'available_days'   => 'required|array|min:1',
            'available_days.*' => 'in:mon,tue,wed,thu,fri,sat,sun',
        ]);

        $doctor = $request->user()->doctorProfile;
        $doctor->update([
            'consultation_fee' => $data['consultation_fee'],
            'available_days'   => $data['available_days'],
        ]);

        // Regenerate slots with new schedule
        app(SlotService::class)->generateForDoctor($doctor, 30);

        return back()->with('success', 'Availability updated. New slots generated.');
    }
}
