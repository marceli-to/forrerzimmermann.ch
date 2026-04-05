<?php

namespace App\Models;

use App\Traits\HasPublish;
use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphOne;

class AtelierPage extends Model
{
	use HasFactory, HasPublish, HasUuid;

	protected $fillable = [
		'uuid', 
    'slug', 
    'title', 
    'text', 
    'publish',
	];

	protected $casts = [
		'publish' => 'boolean',
	];

	public function media(): MorphOne
	{
		return $this->morphOne(Media::class, 'mediable');
	}
}
