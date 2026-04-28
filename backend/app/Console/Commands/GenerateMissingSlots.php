<?php

namespace App\Console\Commands;

use App\Models\Doctor;
use App\Services\SlotService;
use Illuminate\Console\Command;

class GenerateMissingSlots extends Command
{
    protected $signature = 'slots:generate-missing';
    protected $description = 'Generate slots for verified doctors who have none';

    public function handle(SlotService $slotService)
    {
        $doctors = Doctor::verified()->get();
        $count = 0;

        foreach ($doctors as $doctor) {
            if ($doctor->slots()->count() === 0) {
                $this->info("Generating slots for {$doctor->user->name}...");
                $slotService->generateForDoctor($doctor, 30);
                $count++;
            }
        }

        $this->info("Done! Generated slots for {$count} doctors.");
    }
}
