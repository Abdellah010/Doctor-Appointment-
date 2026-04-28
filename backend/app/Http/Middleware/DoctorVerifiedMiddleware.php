<?php

namespace App\Http\Middleware;

use App\Enums\DoctorStatus;
use Closure;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DoctorVerifiedMiddleware
{
    public function handle(Request $request, Closure $next): mixed
    {
        $doctor = $request->user()?->doctorProfile;

        if (!$doctor || $doctor->status !== DoctorStatus::Verified) {
            return Inertia::render('Doctor/PendingVerification', [
                'status' => $doctor?->status->value ?? 'pending',
                'rejection_reason' => $doctor?->rejection_reason,
            ])->toResponse($request)->setStatusCode(403);
        }

        return $next($request);
    }
}
