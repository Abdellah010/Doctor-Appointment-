<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class DoctorRejectedNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        public \App\Models\Doctor $doctor,
        public string $reason,
        public ?string $notes
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $message = (new MailMessage)
            ->subject('Update regarding your DocAppoint application')
            ->greeting('Hello ' . $this->doctor->user->name . ',')
            ->line('We have reviewed your application to join DocAppoint.')
            ->line('Unfortunately, we are unable to verify your profile at this time due to the following reason:')
            ->line('**' . ucfirst(str_replace('_', ' ', $this->reason)) . '**');

        if ($this->notes) {
            $message->line('Additional notes from our team:')
                    ->line($this->notes);
        }

        return $message
            ->line('Please correct the issues and contact support to re-apply.')
            ->action('Contact Support', url('/support'));
    }

    public function toArray(object $notifiable): array
    {
        return [];
    }
}
