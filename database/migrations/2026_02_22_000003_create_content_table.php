<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
	public function up(): void
	{
		Schema::create('content', function (Blueprint $table) {
			$table->id();
			$table->string('key')->unique();
			$table->string('title');
			$table->text('text')->nullable();
			$table->boolean('publish')->default(true);
			$table->boolean('has_media')->default(false);
			$table->integer('sort_order')->default(0);
			$table->timestamps();
		});
	}

	public function down(): void
	{
		Schema::dropIfExists('content');
	}
};
