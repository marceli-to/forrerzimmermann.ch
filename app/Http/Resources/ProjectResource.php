<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProjectResource extends JsonResource
{
	public function toArray(Request $request): array
	{
		return [
			'id' => $this->id,
			'title' => $this->title,
			'slug' => $this->slug,
			'name' => $this->name,
			'location' => $this->location,
			'year' => $this->year,
			'description' => $this->description,
			'info' => $this->info,
			'status' => $this->status,
			'competition' => $this->competition,
			'has_detail' => $this->has_detail,
			'publish' => $this->publish,
			'sort_order' => $this->sort_order,
			'category_id' => $this->category_id,
			'category_type_id' => $this->category_type_id,
			'category' => $this->whenLoaded('category'),
			'category_type' => $this->whenLoaded('categoryType'),
			'media' => MediaResource::collection($this->whenLoaded('media')),
			'grids' => ProjectGridResource::collection($this->whenLoaded('grids')),
			'created_at' => $this->created_at,
			'updated_at' => $this->updated_at,
		];
	}
}
