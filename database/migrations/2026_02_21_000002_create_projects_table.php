<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
	public function up(): void
	{
		Schema::create('projects', function (Blueprint $table) {
			$table->id();
			$table->foreignId('category_id')->nullable()->constrained()->nullOnDelete();
			$table->foreignId('category_type_id')->nullable()->constrained()->nullOnDelete();
			$table->string('title');
			$table->string('slug')->unique();
			$table->string('name')->nullable();
			$table->string('location')->nullable();
			$table->unsignedSmallInteger('year')->nullable();
			$table->text('description')->nullable();
			$table->text('info')->nullable();
			$table->enum('status', ['Ausgeführt', 'In Planung', 'Studie'])->nullable();
			$table->string('competition')->nullable();
			$table->boolean('has_detail')->default(true);
			$table->boolean('publish')->default(false);
			$table->unsignedInteger('sort_order')->default(0);
			$table->timestamps();
		});
	}

	public function down(): void
	{
		Schema::dropIfExists('projects');
	}
};
