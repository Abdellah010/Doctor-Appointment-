<?php

namespace App\Http\Controllers;

use App\Http\Requests\RejectDoctorRequest;
use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\User;
use App\Services\VerificationService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AdminController extends Controller
{
    public function __construct(private readonly VerificationService $verificationService) {}

    /**
     * Admin overview dashboard
     */
    public function dashboard()
    {
        return Inertia::render('Admin/Dashboard', [
            'stats' => [
                'total_patients'   => User::where('role', 'patient')->count(),
                'active_doctors'   => Doctor::where('status', 'verified')->count(),
                'total_appointments' => Appointment::count(),
                'pending_verifications' => Doctor::where('status', 'pending')->count(),
            ],
            'recent_appointments' => Appointment::with(['patient', 'doctor.user'])
                ->orderByDesc('created_at')
                ->limit(10)
                ->get(),
            'weekly_activity' => $this->getWeeklyActivity(),
        ]);
    }

    /**
     * Verification panel — all doctors by status
     */
    public function verifications(Request $request)
    {
        $status = $request->input('status', 'all');

        $doctors = Doctor::with('user')
            ->when($status !== 'all', fn ($q) => $q->where('status', $status))
            ->orderBy('created_at', 'asc')
            ->paginate(20);

        return Inertia::render('Admin/Verifications', [
            'doctors' => $doctors,
            'status'  => $status,
            'counts'  => [
                'all'      => Doctor::count(),
                'pending'  => Doctor::where('status', 'pending')->count(),
                'verified' => Doctor::where('status', 'verified')->count(),
                'rejected' => Doctor::where('status', 'rejected')->count(),
            ],
        ]);
    }

    /**
     * PATCH /admin/doctors/{doctor}/approve
     */
    public function approve(Doctor $doctor)
    {
        $this->verificationService->approve($doctor);

        return back()->with('success', "Dr. {$doctor->user->name} has been approved.");
    }

    /**
     * PATCH /admin/doctors/{doctor}/reject
     */
    public function reject(RejectDoctorRequest $request, Doctor $doctor)
    {
        $this->verificationService->reject(
            $doctor,
            $request->validated('reason'),
            $request->validated('notes')
        );

        return back()->with('success', "Dr. {$doctor->user->name} has been rejected.");
    }

    private function getWeeklyActivity(): array
    {
        $days = collect(range(6, 0))->map(function ($daysAgo) {
            $date = now()->subDays($daysAgo);
            return [
                'label' => $date->format('D'),
                'count' => Appointment::whereDate('created_at', $date)->count(),
            ];
        });

        return $days->values()->all();
    }
}
