<?php

namespace App\Http\Requests\Project;

use Illuminate\Foundation\Http\FormRequest;

class StoreProjectRequest extends FormRequest
{
	public function authorize(): bool
	{
		return true;
	}

	public function rules(): array
	{
		return [
			'title' => 'required|string|max:255',
			'category_id' => 'nullable|exists:categories,id',
			'category_type_id' => 'nullable|exists:category_types,id',
			'name' => 'nullable|string|max:255',
			'location' => 'nullable|string|max:255',
			'year' => 'nullable|integer|min:1900|max:2100',
			'description' => 'nullable|string',
			'info' => 'nullable|string',
			'status' => 'nullable|in:Ausgeführt,In Planung,Studie',
			'competition' => 'nullable|in:1. Preis,2. Preis,3. Preis,Ankauf,Anerkennung,Andere',
			'has_detail' => 'boolean',
			'publish' => 'boolean',
			'media' => 'nullable|array',
			'media.*.uuid' => 'required|string',
			'media.*.file' => 'required|string',
			'media.*.original_name' => 'required|string',
			'media.*.mime_type' => 'required|string',
			'media.*.size' => 'required|integer',
			'media.*.width' => 'nullable|integer',
			'media.*.height' => 'nullable|integer',
			'media.*.alt' => 'nullable|string|max:255',
			'media.*.caption' => 'nullable|string|max:255',
		];
	}
}
