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
			'location' => 'nullable|string|max:255',
			'subtitle' => 'nullable|string|max:255',
			'year' => 'required|integer',
			'description' => 'nullable|string',
			'info' => 'nullable|string',
			'meta_description' => 'nullable|string|max:255',
			'publish' => 'boolean',
			'feature' => 'boolean',
			'topic_id' => 'nullable|string',
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
