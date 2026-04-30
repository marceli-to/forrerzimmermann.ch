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
            'cv' => '<p>2005–2008<br>Bachelorstudium in Architektur, ­ Hochschule für Technik, Stuttgart</p><p>2009–2011<br>Masterstudium in Architektur, ETH Zürich</p><p>2012–2016<br>Adrian Streich Architekten, Zürich</p><p>2016–2017<br>FHNW Muttenz, Wissenschaftliche­ Assistentin Architektur</p><p>2017–2018<br>Gion Signorell, Architekt BSA, Chur</p><p>2018–2019<br>Neon Deiss Architekten GmbH, Zürich</p>',
            'sort_order' => 0,
        ],
        [
            'firstname' => 'Stefan',
            'name' => 'Forrer',
            'title' => 'Architekt MSc SIA',
            'email' => 'sfo@forrerzimmermann.ch',
            'cv' => '<p>2005–2008<br>Bachelorstudium in Architektur, ­ Hochschule für Technik, Stuttgart</p><p>2009–2011<br>Masterstudium in Architektur, ETH Zürich</p><p>2012–2016<br>Adrian Streich Architekten, Zürich</p><p>2016–2017<br>FHNW Muttenz, Wissenschaftliche­ Assistentin Architektur</p><p>2017–2018<br>Gion Signorell, Architekt BSA, Chur</p><p>2018–2019<br>Neon Deiss Architekten GmbH, Zürich</p>',
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
                    'cv' => $data['cv'] ?? null,
                    'publish' => true,
                    'sort_order' => $data['sort_order'],
                ]
            );
        }

        $this->info('Team members seeded.');
    }
}
