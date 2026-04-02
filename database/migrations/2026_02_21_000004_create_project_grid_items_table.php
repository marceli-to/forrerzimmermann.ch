<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
	public function up(): void
	{
		Schema::create('project_grid_items', function (Blueprint $table) {
			$table->id();
			$table->foreignId('project_grid_id')->constrained()->cascadeOnDelete();
			$table->foreignId('media_id')->nullable()->constrained()->nullOnDelete();
			$table->unsignedInteger('position')->default(0);
			$table->timestamps();
		});
	}

	public function down(): void
	{
		Schema::dropIfExists('project_grid_items');
	}
};
