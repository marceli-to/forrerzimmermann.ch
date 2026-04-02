<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
	public function up(): void
	{
		Schema::create('site_settings', function (Blueprint $table) {
			$table->id();
			$table->uuid('uuid')->unique();
			$table->string('site_title');
			$table->string('meta_description')->nullable();
			$table->string('landing_meta_description')->nullable();
			$table->string('projects_meta_description')->nullable();
			$table->timestamps();
		});
	}

	public function down(): void
	{
		Schema::dropIfExists('site_settings');
	}
};
