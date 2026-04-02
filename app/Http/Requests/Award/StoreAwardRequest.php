<?php

namespace App\Http\Requests\Award;

use Illuminate\Foundation\Http\FormRequest;

class StoreAwardRequest extends FormRequest
{
	public function authorize(): bool
	{
		return true;
	}

	public function rules(): array
	{
		return [
			'title' => 'required|string|max:255',
			'description' => 'nullable|string',
			'year' => 'required|string|max:10',
			'url' => 'nullable|string|max:255',
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
