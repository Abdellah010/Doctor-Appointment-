<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class DoctorApprovedNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(public \App\Models\Doctor $doctor)
    {
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Congratulations! Your account is verified')
            ->greeting('Hello ' . $this->doctor->user->name . ',')
            ->line('Your doctor profile has been successfully verified by our administration team.')
            ->line('You can now log in to manage your schedule and start receiving appointments.')
            ->action('Access Dashboard', url('/login'))
            ->line('Welcome to DocAppoint!');
    }

    public function toArray(object $notifiable): array
    {
        return [];
    }
}
