<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
	public function up(): void
	{
		Schema::create('domain_jobs', function (Blueprint $table) {
			$table->id();
			$table->string('title');
			$table->text('lead')->nullable();
			$table->text('info')->nullable();
			$table->boolean('publish')->default(true);
			$table->integer('sort_order')->default(0);
			$table->timestamps();
		});
	}

	public function down(): void
	{
		Schema::dropIfExists('domain_jobs');
	}
};
