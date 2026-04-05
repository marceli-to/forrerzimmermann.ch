<?php

namespace App\Console\Commands;

use App\Models\TeamMember;
use Illuminate\Console\Command;

class SeedTeam extends Command
{
    protected $signature = 'app:seed-team';
    protected $description = 'Seed team members';

    protected array $members = [
        [
            'firstname' => 'Katrin',
            'name' => 'Zimmermann',
            'title' => 'Architektin MSc ETH',
            'email' => 'kzi@forrerzimmermann.ch',
            'sort_order' => 0,
        ],
        [
            'firstname' => 'Stefan',
            'name' => 'Forrer',
            'title' => 'Architekt MSc SIA',
            'email' => 'sfo@forrerzimmermann.ch',
            'sort_order' => 1,
        ],
        [
            'firstname' => 'Maya',
            'name' => 'Muster',
            'title' => 'Architektin MSc ETH',
            'email' => 'mmu@forrerzimmermann.ch',
            'sort_order' => 2,
        ],
    ];

    public function handle(): void
    {
        foreach ($this->members as $data) {
            TeamMember::firstOrCreate(
                ['firstname' => $data['firstname'], 'name' => $data['name']],
                [
                    'title' => $data['title'],
                    'email' => $data['email'],
                    'publish' => true,
                    'sort_order' => $data['sort_order'],
                ]
            );
        }

        $this->info('Team members seeded.');
    }
}
