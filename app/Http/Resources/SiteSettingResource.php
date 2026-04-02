<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SiteSettingResource extends JsonResource
{
	public function toArray(Request $request): array
	{
		return [
			'uuid' => $this->uuid,
			'site_title' => $this->site_title,
			'meta_description' => $this->meta_description,
			'landing_meta_description' => $this->landing_meta_description,
			'projects_meta_description' => $this->projects_meta_description,
			'media' => MediaResource::collection($this->whenLoaded('media')),
		];
	}
}
