<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class SiteSetting extends Model
{
	use HasFactory, HasUuid;

	protected $fillable = [
		'uuid', 'site_title', 'meta_description',
		'landing_meta_description', 'projects_meta_description',
	];

	public function media(): MorphMany
	{
		return $this->morphMany(Media::class, 'mediable')->orderBy('sort_order');
	}
}
