<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MediaResource extends JsonResource
{
	public function toArray(Request $request): array
	{
		return [
			'id' => $this->id,
			'uuid' => $this->uuid,
			'file' => $this->file,
			'original_name' => $this->original_name,
			'mime_type' => $this->mime_type,
			'size' => $this->size,
			'alt' => $this->alt,
			'caption' => $this->caption,
			'width' => $this->width,
			'height' => $this->height,
			'orientation' => $this->orientation,
			'is_teaser' => $this->is_teaser,
			'sort_order' => $this->sort_order,
			'in_use' => $this->mediable_id !== null,
			'type' => str_starts_with($this->mime_type, 'video/') ? 'video' : 'image',
			'original_url' => '/uploads/' . $this->file,
			'thumbnail_url' => '/img/uploads/' . $this->file . '?w=200&h=200&fit=crop',
			'preview_url' => '/img/uploads/' . $this->file . '?w=800&fit=max',
		];
	}
}
