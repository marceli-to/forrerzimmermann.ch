<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Content extends Model
{
	use HasFactory;

	protected $table = 'content';

	protected $fillable = [
		'key',
		'title',
		'text',
		'publish',
		'has_media',
		'sort_order',
	];

	protected $casts = [
		'publish' => 'boolean',
		'has_media' => 'boolean',
		'sort_order' => 'integer',
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
