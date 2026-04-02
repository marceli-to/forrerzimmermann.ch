<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class News extends Model
{
	use HasFactory;

	protected $fillable = [
		'date',
		'title',
		'subtitle',
		'text',
		'link',
		'link_text',
		'sort_order',
	];

	protected $casts = [
		'sort_order' => 'integer',
	];

	public function media(): MorphMany
	{
		return $this->morphMany(Media::class, 'mediable')->orderBy('sort_order');
	}
}
