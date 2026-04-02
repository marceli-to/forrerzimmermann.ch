<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
	use HasFactory;
	protected $fillable = ['name', 'publish', 'sort_order'];

	protected $casts = [
		'publish' => 'boolean',
	];

	public function types(): HasMany
	{
		return $this->hasMany(CategoryType::class)->orderBy('sort_order');
	}

	public function projects(): HasMany
	{
		return $this->hasMany(Project::class);
	}
}
