<?php

namespace App\Console\Commands;

use App\Models\AtelierPage;
use Illuminate\Console\Command;

class SeedAtelier extends Command
{
    protected $signature = 'app:seed-atelier';
    protected $description = 'Seed atelier pages';

    public function handle(): void
    {
        AtelierPage::firstOrCreate(['slug' => 'profil'], ['publish' => true]);
        AtelierPage::firstOrCreate(['slug' => 'team'], ['publish' => true]);
        AtelierPage::firstOrCreate(['slug' => 'jobs'], ['publish' => true]);

        $this->info('Atelier pages seeded.');
    }
}
