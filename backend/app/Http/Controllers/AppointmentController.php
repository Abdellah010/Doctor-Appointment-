<?php

namespace App\Http\Controllers;

use App\Http\Requests\BookAppointmentRequest;
use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\Slot;
use App\Services\BookingService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AppointmentController extends Controller
{
    public function __construct(private readonly BookingService $bookingService) {}

    /**
     * POST /appointments — book a new appointment
     */
    public function store(BookAppointmentRequest $request)
    {
        $doctor = Doctor::findOrFail($request->doctor_id);
        $slot   = Slot::findOrFail($request->slot_id);

        $appointment = $this->bookingService->book(
            patient: $request->user(),
            doctor:  $doctor,
            slot:    $slot,
            data:    $request->validated()
        );

        return redirect()->route('patient.dashboard')
            ->with('success', 'Appointment confirmed! SMS sent to your phone.');
    }

    /**
     * PATCH /appointments/{appointment}/cancel
     */
    public function cancel(Request $request, Appointment $appointment)
    {
        $this->bookingService->cancel($appointment, $request->user());

        return back()->with('success', 'Appointment cancelled.');
    }

    /**
     * PATCH /appointments/{appointment}/accept — doctor accepts
     */
    public function accept(Request $request, Appointment $appointment)
    {
        $this->authorize('manage', $appointment);
        $this->bookingService->accept($appointment);

        return back()->with('success', 'Appointment accepted.');
    }

    /**
     * PATCH /appointments/{appointment}/decline — doctor declines
     */
    public function decline(Request $request, Appointment $appointment)
    {
        $this->authorize('manage', $appointment);
        $this->bookingService->decline($appointment);

        return back()->with('success', 'Appointment declined.');
    }

    /**
     * POST /appointments/{appointment}/complete — mark complete + rate
     */
    public function complete(Request $request, Appointment $appointment)
    {
        $request->validate([
            'rating' => 'nullable|integer|min:1|max:5',
            'review' => 'nullable|string|max:500',
        ]);

        $this->bookingService->complete(
            $appointment,
            $request->integer('rating'),
            $request->string('review')
        );

        return back()->with('success', 'Appointment marked complete. Thank you for your rating!');
    }
}
