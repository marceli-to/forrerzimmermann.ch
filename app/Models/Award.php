<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Award extends Model
{
	use HasFactory;

	protected $fillable = [
		'title',
		'description',
		'year',
		'url',
		'publish',
	];

	protected $casts = [
		'publish' => 'boolean',
	];

	public function media(): MorphMany
	{
		return $this->morphMany(Media::class, 'mediable')->orderBy('sort_order');
	}

	public function scopePublished($query)
	{
		return $query->where('publish', true);
	}
}
