<?php

namespace Database\Seeders;

use App\Models\AtelierPage;
use App\Models\Contact;
use App\Models\SeoSetting;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => 'm@marceli.to'],
            [
                'firstname' => 'Marcel',
                'name' => 'Stadelmann',
                'password' => Hash::make('7aq31rr23'),
                'role' => 'admin',
            ]
        );

        AtelierPage::firstOrCreate(['slug' => 'profil'], ['sort_order' => 0]);
        AtelierPage::firstOrCreate(['slug' => 'team'], ['sort_order' => 1]);
        AtelierPage::firstOrCreate(['slug' => 'jobs'], ['sort_order' => 2]);

        Contact::firstOrCreate(
            ['id' => 1],
            [
                'name' => 'Forrer Zimmermann Architekten GmbH',
                'address' => 'Badenerstrasse 370, CH-8004 Zürich',
                'email' => 'mail@forrerzimmermann.ch',
                'phone' => '+41 44 548 90 01',
            ]
        );

        SeoSetting::firstOrCreate(['id' => 1]);
    }
}
