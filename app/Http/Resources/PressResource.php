<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PressResource extends JsonResource
{
	public function toArray(Request $request): array
	{
		return [
			'id' => $this->id,
			'project_id' => $this->project_id,
			'title' => $this->title,
			'description' => $this->description,
			'year' => $this->year,
			'url' => $this->url,
			'publish' => $this->publish,
			'sort_order' => $this->sort_order,
			'project' => $this->whenLoaded('project'),
			'media' => MediaResource::collection($this->whenLoaded('media')),
			'created_at' => $this->created_at,
			'updated_at' => $this->updated_at,
		];
	}
}
