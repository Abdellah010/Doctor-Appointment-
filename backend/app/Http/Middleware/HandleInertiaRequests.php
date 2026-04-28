<?php

namespace App\Http\Middleware;

use App\Enums\DoctorStatus;
use App\Models\Doctor;
use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    protected $rootView = 'app';

    public function version(Request $request): string|null
    {
        return parent::version($request);
    }

    /**
     * Shared data available on every Inertia page via usePage().props
     */
    public function share(Request $request): array
    {
        $user = $request->user();

        return array_merge(parent::share($request), [
            'auth' => [
                'user' => $user ? [
                    'id'             => $user->id,
                    'name'           => $user->name,
                    'email'          => $user->email,
                    'phone'          => $user->phone,
                    'role'           => $user->role->value,
                    'insurance_type' => $user->insurance_type,
                    'avatar'         => $user->avatar,
                    'created_at'     => $user->created_at,
                ] : null,
            ],

            // Flash messages for toast notifications
            'flash' => [
                'success' => $request->session()->get('success'),
                'error'   => $request->session()->get('error'),
            ],

            // Badge count for admin nav alert
            'pending_verifications_count' => $user?->isAdmin()
                ? Doctor::where('status', DoctorStatus::Pending)->count()
                : 0,
        ]);
    }
}
