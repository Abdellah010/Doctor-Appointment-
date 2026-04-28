<?php

namespace App\Http\Controllers;

use App\Enums\UserRole;
use App\Models\Doctor;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Inertia\Inertia;

class AuthController extends Controller
{
    public function showPatientLogin()
    {
        return Inertia::render('Auth/Login', ['role' => 'patient']);
    }

    public function showDoctorLogin()
    {
        return Inertia::render('Auth/Login', ['role' => 'doctor']);
    }

    public function showAdminLogin()
    {
        return Inertia::render('Auth/Login', ['role' => 'admin']);
    }

    public function login(Request $request)
    {
        $request->validate([
            'identity' => 'required|string',
            'password' => 'required|string',
        ]);

        $identity = $request->identity;
        $field = filter_var($identity, FILTER_VALIDATE_EMAIL) ? 'email' : 'phone';

        $user = User::where($field, $identity)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return back()->withErrors(['identity' => 'These credentials do not match our records.']);
        }

        // Enforce role separation
        $selectedRole = $request->input('role');
        if ($selectedRole && $user->role->value !== $selectedRole) {
            return back()->withErrors(['identity' => "This account is not authorized to access the " . ucfirst($selectedRole) . " portal."]);
        }

        Auth::login($user, $request->boolean('remember'));
        $request->session()->regenerate();

        return $this->redirectByRole($user);
    }

    public function showPatientRegister()
    {
        return Inertia::render('Auth/Login', ['role' => 'patient', 'isRegister' => true]);
    }

    public function showDoctorRegister()
    {
        return Inertia::render('Auth/Login', ['role' => 'doctor', 'isRegister' => true]);
    }

    public function register(Request $request)
    {
        $data = $request->validate([
            'name'      => 'required|string|max:120',
            'email'     => 'required|email|unique:users',
            'password'  => ['required', Password::defaults()],
            'role'      => 'required|in:patient,doctor',
            'phone'     => 'nullable|string|max:25',
            // Doctor extra fields
            'specialty' => 'required_if:role,doctor|nullable|string|max:80',
            'city'      => 'required_if:role,doctor|nullable|string|max:80',
        ]);

        $user = User::create([
            'name'     => $data['name'],
            'email'    => $data['email'],
            'password' => Hash::make($data['password']),
            'role'     => UserRole::from($data['role']),
            'phone'    => $data['phone'] ?? null,
        ]);

        // Create doctor profile placeholder awaiting verification
        if ($user->isDoctor()) {
            Doctor::create([
                'user_id'          => $user->id,
                'specialty'        => $data['specialty'],
                'city'             => $data['city'],
                'license_number'   => 'PENDING-' . $user->id,
                'consultation_fee' => 0,
                'status'           => 'pending',
                'available_days'   => ['mon','tue','wed','thu','fri'],
                'slot_duration'    => 30,
            ]);
        }

        Auth::login($user);
        $request->session()->regenerate();

        return $this->redirectByRole($user);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }

    private function redirectByRole(User $user)
    {
        return match ($user->role) {
            UserRole::Admin  => redirect()->route('admin.dashboard'),
            UserRole::Doctor => redirect()->route('doctor.dashboard'),
            default          => redirect()->route('patient.dashboard'),
        };
    }
}
