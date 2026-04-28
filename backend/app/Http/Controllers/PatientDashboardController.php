<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

class PatientDashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user()->load('appointments.doctor.user');

        $upcoming = $user->appointments()
            ->with(['doctor.user', 'slot'])
            ->upcoming()
            ->orderBy('scheduled_at')
            ->get();

        $past = $user->appointments()
            ->with(['doctor.user'])
            ->completed()
            ->orderByDesc('scheduled_at')
            ->limit(20)
            ->get();

        return Inertia::render('Patient/Dashboard', [
            'user'     => $user,
            'upcoming' => $upcoming,
            'past'     => $past,
            'stats'    => [
                'upcoming_count'   => $upcoming->count(),
                'completed_count'  => $user->appointments()->completed()->count(),
                'doctors_visited'  => $user->appointments()->distinct('doctor_id')->count('doctor_id'),
            ],
        ]);
    }
}
