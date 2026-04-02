<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Press extends Model
{
	use HasFactory;

	protected $table = 'press';

	protected $fillable = [
		'project_id',
		'title',
		'description',
		'year',
		'url',
		'publish',
		'sort_order',
	];

	protected $casts = [
		'publish' => 'boolean',
		'sort_order' => 'integer',
	];

	public function project(): BelongsTo
	{
		return $this->belongsTo(Project::class);
	}

	public function media(): MorphMany
	{
		return $this->morphMany(Media::class, 'mediable')->orderBy('sort_order');
	}

	public function scopePublished($query)
	{
		return $query->where('publish', true);
	}
}
