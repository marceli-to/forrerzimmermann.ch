<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ContentResource extends JsonResource
{
	public function toArray(Request $request): array
	{
		return [
			'id' => $this->id,
			'key' => $this->key,
			'title' => $this->title,
			'text' => $this->text,
			'publish' => $this->publish,
			'has_media' => $this->has_media,
			'sort_order' => $this->sort_order,
			'media' => MediaResource::collection($this->whenLoaded('media')),
			'created_at' => $this->created_at,
			'updated_at' => $this->updated_at,
		];
	}
}
