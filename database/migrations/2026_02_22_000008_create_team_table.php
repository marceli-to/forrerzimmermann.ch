<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
	public function up(): void
	{
		Schema::create('team', function (Blueprint $table) {
			$table->id();
			$table->string('firstname');
			$table->string('name');
			$table->string('role')->nullable();
			$table->string('position')->nullable();
			$table->string('phone')->nullable();
			$table->string('email');
			$table->text('cv')->nullable();
			$table->boolean('publish')->default(true);
			$table->integer('sort_order')->default(0);
			$table->timestamps();
		});
	}

	public function down(): void
	{
		Schema::dropIfExists('team');
	}
};
