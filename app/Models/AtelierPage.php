<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class AtelierPage extends Model
{
	use HasFactory, HasUuid;

	protected $fillable = [
		'uuid', 'slug', 'title', 'text',
		'meta_description', 'publish', 'sort_order',
	];

	protected $casts = [
		'publish' => 'boolean',
		'sort_order' => 'integer',
	];

	public function media(): MorphMany
	{
		return $this->morphMany(Media::class, 'mediable')->orderBy('sort_order');
	}
}
