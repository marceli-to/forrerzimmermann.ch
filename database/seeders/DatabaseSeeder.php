<?php

namespace Database\Seeders;

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
                'name' => 'Marcel Stadelmann',
                'password' => Hash::make('7aq31rr23'),
            ]
        );
    }
}
