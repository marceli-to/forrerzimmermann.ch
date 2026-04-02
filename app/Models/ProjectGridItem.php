<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProjectGridItem extends Model
{
	protected $fillable = [
		'project_grid_id',
		'media_id',
		'position',
	];

	protected $casts = [
		'position' => 'integer',
	];

	public function grid(): BelongsTo
	{
		return $this->belongsTo(ProjectGrid::class, 'project_grid_id');
	}

	public function media(): BelongsTo
	{
		return $this->belongsTo(Media::class);
	}
}
