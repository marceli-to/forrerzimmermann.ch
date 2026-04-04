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
                'address' => "Badenerstrasse 370\n\nCH-8004 Zürich",
                'email' => 'mail@forrerzimmermann.ch',
                'phone' => '+41 44 548 90 01',
                'maps_url' => null,
                'imprint' => null,
            ]
        );

        $this->info('Contact data seeded.');
    }
}
