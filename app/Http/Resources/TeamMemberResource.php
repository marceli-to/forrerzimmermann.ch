<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TeamMemberResource extends JsonResource
{
	public function toArray(Request $request): array
	{
		return [
			'id' => $this->id,
			'firstname' => $this->firstname,
			'name' => $this->name,
			'role' => $this->role,
			'position' => $this->position,
			'phone' => $this->phone,
			'email' => $this->email,
			'cv' => $this->cv,
			'publish' => $this->publish,
			'sort_order' => $this->sort_order,
			'media' => MediaResource::collection($this->whenLoaded('media')),
			'created_at' => $this->created_at,
			'updated_at' => $this->updated_at,
		];
	}
}
