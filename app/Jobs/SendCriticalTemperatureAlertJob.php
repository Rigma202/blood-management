<?php

namespace App\Jobs;

use App\Models\TemperatureAlert;
use App\Notifications\CriticalTemperatureAlertNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendCriticalTemperatureAlertJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public TemperatureAlert $alert)
    {
    }

    public function handle(): void
    {
        $alert = $this->alert->load('refrigerator.bloodBank.users');

        $users = $alert->refrigerator?->bloodBank?->users ?? collect();

        foreach ($users as $user) {
            $user->notify(new CriticalTemperatureAlertNotification($alert));
        }

        $alert->update(['notified' => true]);
    }

    public function failed(\Throwable $exception): void
    {
        Log::error('SendCriticalTemperatureAlertJob failed', [
            'alert_id' => $this->alert->id,
            'error' => $exception->getMessage(),
        ]);
    }
}