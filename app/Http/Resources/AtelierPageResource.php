<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AtelierPageResource extends JsonResource
{
	public function toArray(Request $request): array
	{
		return [
			'uuid' => $this->uuid,
			'slug' => $this->slug,
			'title' => $this->title,
			'text' => $this->text,
			'meta_description' => $this->meta_description,
			'publish' => $this->publish,
			'sort_order' => $this->sort_order,
			'media' => MediaResource::collection($this->whenLoaded('media')),
		];
	}
}
