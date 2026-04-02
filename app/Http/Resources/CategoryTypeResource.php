<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CategoryTypeResource extends JsonResource
{
	public function toArray(Request $request): array
	{
		return [
			'id' => $this->id,
			'category_id' => $this->category_id,
			'name' => $this->name,
			'name_singular' => $this->name_singular,
			'publish' => $this->publish,
			'sort_order' => $this->sort_order,
		];
	}
}
