<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class Seed extends Command
{
    protected $signature = 'app:seed';
    protected $description = 'Seed the entire database';

    public function handle(): void
    {
        $this->call('app:seed-user');
        $this->call('app:seed-atelier');
        $this->call('app:seed-contact');
        $this->call('app:seed-seo');
        $this->call('app:seed-topics');
        $this->call('app:seed-projects');
        $this->call('app:seed-team');
        $this->call('app:seed-jobs');

        $this->info('Database seeded.');
    }
}
