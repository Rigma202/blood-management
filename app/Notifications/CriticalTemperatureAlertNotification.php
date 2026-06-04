<?php

namespace App\Notifications;

use App\Models\TemperatureAlert;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CriticalTemperatureAlertNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public TemperatureAlert $alert)
    {
    }

    public function via($notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Critical Refrigerator Temperature Alert')
            ->greeting("Hello {$notifiable->name},")
            ->line("Refrigerator: {$this->alert->refrigerator->name}")
            ->line("Temperature: {$this->alert->temperature}°C")
            ->line("Recorded at: {$this->alert->recorded_at->format('Y-m-d H:i:s')}")
            ->line('The refrigerator temperature has been above 8°C for 10 continuous minutes.')
            ->line('Please inspect the refrigerator immediately.');
    }

    public function toArray($notifiable): array
    {
        return [
            'alert_id' => $this->alert->id,
            'refrigerator_id' => $this->alert->refrigerator_id,
            'refrigerator_name' => $this->alert->refrigerator->name,
            'temperature' => $this->alert->temperature,
            'recorded_at' => $this->alert->recorded_at->toDateTimeString(),
        ];
    }
}