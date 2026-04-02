<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Project extends Model
{
	use HasFactory;
	protected $fillable = [
		'title',
		'slug',
		'name',
		'location',
		'year',
		'description',
		'info',
		'status',
		'competition',
		'publish',
		'sort_order',
	];

	protected $casts = [
		'publish' => 'boolean',
		'year' => 'integer',
		'sort_order' => 'integer',
	];

	public function media(): MorphMany
	{
		return $this->morphMany(Media::class, 'mediable')->orderBy('sort_order');
	}

	public function teaser(): MorphMany
	{
		return $this->morphMany(Media::class, 'mediable')->where('is_teaser', true);
	}
}
