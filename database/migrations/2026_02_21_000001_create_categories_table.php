<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
	public function up(): void
	{
		Schema::create('categories', function (Blueprint $table) {
			$table->id();
			$table->string('name');
			$table->boolean('publish')->default(true);
			$table->unsignedInteger('sort_order')->default(0);
			$table->timestamps();
		});

		Schema::create('category_types', function (Blueprint $table) {
			$table->id();
			$table->foreignId('category_id')->constrained()->cascadeOnDelete();
			$table->string('name');
			$table->string('name_singular');
			$table->boolean('publish')->default(true);
			$table->unsignedInteger('sort_order')->default(0);
			$table->timestamps();
		});
	}

	public function down(): void
	{
		Schema::dropIfExists('category_types');
		Schema::dropIfExists('categories');
	}
};
