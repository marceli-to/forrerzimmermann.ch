<?php

namespace App\Console\Commands;

use App\Models\Topic;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class SeedTopics extends Command
{
    protected $signature = 'app:seed-topics';
    protected $description = 'Seed topics';

    public function handle(): void
    {
        $topics = ['Wettbewerb', 'Sanierung', 'Neubau', 'Renovation'];

        foreach ($topics as $index => $title) {
            Topic::firstOrCreate(
                ['slug' => Str::slug($title)],
                [
                    'title' => $title,
                    'publish' => true,
                    'sort_order' => $index,
                ]
            );
        }

        $this->info('Topics seeded.');
    }
}
