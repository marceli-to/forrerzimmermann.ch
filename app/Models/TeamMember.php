<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class TeamMember extends Model
{
	use HasFactory;

	protected $table = 'team';

	protected $fillable = [
		'firstname',
		'name',
		'role',
		'position',
		'phone',
		'email',
		'cv',
		'publish',
		'sort_order',
	];

	protected $casts = [
		'publish' => 'boolean',
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
