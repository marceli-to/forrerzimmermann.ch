<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Media extends Model
{
	use HasFactory, HasUuid;

	protected $fillable = [
		'uuid',
		'mediable_type',
		'mediable_id',
		'file',
		'original_name',
		'mime_type',
		'size',
		'alt',
		'caption',
		'width',
		'height',
		'is_teaser',
		'sort_order',
	];

	protected $casts = [
		'is_teaser' => 'boolean',
		'size' => 'integer',
		'width' => 'integer',
		'height' => 'integer',
	];

	public function mediable(): MorphTo
	{
		return $this->morphTo();
	}

	public function isImage(): bool
	{
		return str_starts_with($this->mime_type, 'image/');
	}

	public function isVideo(): bool
	{
		return str_starts_with($this->mime_type, 'video/');
	}

	public function getOrientationAttribute(): string
	{
		if (!$this->width || !$this->height) {
			return 'unknown';
		}
		if ($this->width > $this->height) {
			return 'landscape';
		}
		if ($this->height > $this->width) {
			return 'portrait';
		}
		return 'square';
	}
}
