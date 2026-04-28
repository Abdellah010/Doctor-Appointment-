<?php

namespace App\Jobs;

use App\Models\Appointment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Twilio\Rest\Client as TwilioClient;

class SendAppointmentSms implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $backoff = 60;

    public function __construct(public readonly Appointment $appointment) {}

    public function handle(): void
    {
        $appt    = $this->appointment->load(['patient', 'doctor.user']);
        $patient = $appt->patient;

        if (empty($patient->phone)) {
            return;
        }

        $doctorName   = $appt->doctor->user->name;
        $date         = $appt->scheduled_at->format('M d, Y');
        $time         = $appt->scheduled_at->format('g:i A');

        $message = match ($appt->status->value) {
            'confirmed' => "DocAppoint: Your appointment with {$doctorName} is confirmed for {$date} at {$time}. Reply CANCEL to cancel.",
            'cancelled' => "DocAppoint: Your appointment with {$doctorName} on {$date} at {$time} has been cancelled.",
            'declined'  => "DocAppoint: Your appointment request with {$doctorName} was declined. Please book a different slot.",
            default     => "DocAppoint: Appointment update — {$date} at {$time} with {$doctorName}.",
        };

        $sid   = config('services.twilio.sid');
        $token = config('services.twilio.token');
        $from  = config('services.twilio.from');

        if (!$sid || !$token || !$from) {
            \Log::warning("Twilio credentials not configured. SMS not sent for appointment #{$appt->id}");
            return;
        }

        try {
            $twilio = new TwilioClient($sid, $token);

            $twilio->messages->create($patient->phone, [
                'from' => $from,
                'body' => $message,
            ]);

            $appt->update(['sms_sent' => true]);

        } catch (\Exception $e) {
            report($e);
            $this->fail($e);
        }
    }
}
