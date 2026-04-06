<?php

namespace App\Console\Commands;

use App\Models\Contact;
use Illuminate\Console\Command;

class SeedContact extends Command
{
    protected $signature = 'app:seed-contact';
    protected $description = 'Seed contact data';

    public function handle(): void
    {
        Contact::firstOrCreate(
            ['id' => 1],
            [
                'name' => 'Forrer Zimmermann Architekten GmbH',
                'address' => "Badenerstrasse 370\nCH-8004 Zürich",
                'email' => 'mail@forrerzimmermann.ch',
                'phone' => '+41 44 548 90 01',
                'maps_url' => 'https://maps.app.goo.gl/75Hp2fPpY43qCL9s7',
                'imprint' => '<p><strong>Redaktion</strong><br>Forrer Zimmermann Architekten GmbH, Zürich</p><p><strong>Konzept und Gestaltung</strong><br>Bivgrafik AG, <a target="_blank" rel="noopener noreferrer nofollow" href="https://bivgrafik.ch">bivgrafik.ch</a>, Zürich</p><p><strong>Text</strong><br>Forrer Zimmermann Architekten GmbH, Zürich</p><p><strong>Fotografie</strong><br>Hans Muster, Zürich</p><p><strong>Programmierung</strong><br>Marcel Stadelmann, <a target="_blank" rel="noopener noreferrer nofollow" href="https://marceli.to">marceli.to</a>, Zürich</p>',
            ]
        );

        $this->info('Contact data seeded.');
    }
}
