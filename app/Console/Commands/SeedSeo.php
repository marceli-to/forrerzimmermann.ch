<?php

namespace App\Console\Commands;

use App\Models\SeoSetting;
use Illuminate\Console\Command;

class SeedSeo extends Command
{
    protected $signature = 'app:seed-seo';
    protected $description = 'Seed SEO settings';

    public function handle(): void
    {
        SeoSetting::firstOrCreate(['id' => 1]);

        $this->info('SEO settings seeded.');
    }
}
