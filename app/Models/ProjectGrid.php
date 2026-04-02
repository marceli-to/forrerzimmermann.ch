<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProjectGrid extends Model
{
	protected $fillable = [
		'project_id',
		'layout_key',
		'sort_order',
	];

	protected $casts = [
		'sort_order' => 'integer',
	];

	public function project(): BelongsTo
	{
		return $this->belongsTo(Project::class);
	}

	public function items(): HasMany
	{
		return $this->hasMany(ProjectGridItem::class)->orderBy('position');
	}
}
