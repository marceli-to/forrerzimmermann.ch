<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProjectGridResource extends JsonResource
{
	public function toArray(Request $request): array
	{
		return [
			'id' => $this->id,
			'layout_key' => $this->layout_key,
			'sort_order' => $this->sort_order,
			'items' => ProjectGridItemResource::collection($this->whenLoaded('items')),
		];
	}
}
