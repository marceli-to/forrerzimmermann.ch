<?php

namespace App\Console\Commands;

use App\Models\JobListing;
use Illuminate\Console\Command;

class SeedJobs extends Command
{
    protected $signature = 'app:seed-jobs';
    protected $description = 'Seed job listings';

    public function handle(): void
    {
        JobListing::firstOrCreate(
            ['title' => 'Praktikumstelle'],
            [
                'text' => "Für Mitarbeit an Bauprojekten suchen wir ab sofort eine Praktikantin/einen Praktikanten.\n\nVon Vorteil sind vier Semester Architekturstudium, gute Deutschkenntnisse und Erfahrung mit VectorWorks.\n\nWir bieten eine Vollzeitanstellung für mindestens sechs Monate oder maximal ein Jahr. Gerne können Sie uns bei Interesse Ihr Portfolio per Mail schicken.",
                'publish' => true,
                'sort_order' => 0,
            ]
        );

        $this->info('Job listings seeded.');
    }
}
