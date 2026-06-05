<?php

namespace App\Console\Commands;

use App\Models\Refrigerator;
use App\Models\TemperatureLog;
use App\Services\TemperatureSimulatorService;
use App\Services\TemperatureService;
use Illuminate\Console\Command;

class SimulateTemperatureLogs extends Command
{
    protected $signature = 'simulate:temperature {--bulk= : Generate N minutes of back-filled logs}';
    protected $description = 'Simulate realistic temperature readings for all active refrigerators';

    public function __construct(
        private TemperatureSimulatorService $simulator,
        private TemperatureService          $temperatureService,
    ) {
        parent::__construct();
    }

    public function handle(): void
    {
        $refrigerators = Refrigerator::where('is_active', true)->get();

        if ($refrigerators->isEmpty()) {
            $this->info('No active refrigerators found. Nothing to simulate.');
            return;
        }

        $bulkMinutes = (int) $this->option('bulk');

        if ($bulkMinutes > 0) {
            $this->bulkGenerate($refrigerators, $bulkMinutes);
            return;
        }

        foreach ($refrigerators as $fridge) {
            $temp = $this->simulator->generateTemperature($fridge->id);

            $this->temperatureService->log($fridge->id, $temp);
            $this->info("[Fridge {$fridge->id}] {$fridge->name} → {$temp}°C");
        }
    }

    private function bulkGenerate($refrigerators, int $minutes): void
    {
        $this->info("Bulk generating {$minutes} minutes of data...");

        foreach ($refrigerators as $fridge) {
            $this->info("\n Fridge: {$fridge->name}");

            for ($i = $minutes; $i >= 1; $i--) {
                $temp       = $this->simulator->generateTemperature($fridge->id);
                $recordedAt = now()->subMinutes($i);

                $this->temperatureService->log($fridge->id, $temp, $recordedAt);
            }
        }

        $this->info("\n Done! {$minutes} readings generated per refrigerator.");
    }
}
