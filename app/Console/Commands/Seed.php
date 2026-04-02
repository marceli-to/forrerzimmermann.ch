<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class Seed extends Command
{
	protected $signature = 'app:seed';

	protected $description = 'Nuke all tables, run migrations and seed default data';

	public function handle(): void
	{
		if (!$this->confirm('This will delete all data. Continue?')) {
			return;
		}

		$this->info('Running fresh migrations...');
		$this->call('migrate:fresh');

		$this->info('Creating default user...');
		User::create([
			'name' => 'Marcel Stadelmann',
			'email' => 'm@marceli.to',
			'password' => Hash::make('7aq31rr23'),
		]);

		$this->info('Seeding categories...');
		$this->call('app:categories');

		$this->info('Seeding projects...');
		$this->call('app:projects');

		$this->info('Done!');
	}
}
