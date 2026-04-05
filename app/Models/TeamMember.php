<?php

namespace App\Models;

use App\Traits\HasPublish;
use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class TeamMember extends Model
{
	use HasFactory, HasPublish, HasUuid;

	protected $fillable = [
		'uuid', 'firstname', 'name', 'title', 'email', 'cv',
		'publish', 'former', 'sort_order',
	];

	protected $casts = [
		'publish' => 'boolean',
		'former' => 'boolean',
		'sort_order' => 'integer',
	];

	public function media(): MorphMany
	{
		return $this->morphMany(Media::class, 'mediable')->orderBy('sort_order');
	}
}
