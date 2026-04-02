<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Project extends Model
{
	use HasFactory, HasUuid;

	protected $fillable = [
		'uuid', 'title', 'location', 'slug', 'subtitle', 'year',
		'description', 'info', 'meta_description',
		'publish', 'feature', 'sort_order', 'topic_id',
	];

	protected $casts = [
		'publish' => 'boolean',
		'feature' => 'boolean',
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

	public function topic(): BelongsTo
	{
		return $this->belongsTo(Topic::class);
	}
}
