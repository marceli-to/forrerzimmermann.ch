<?php

namespace App\Http\Requests\Team;

use Illuminate\Foundation\Http\FormRequest;

class StoreTeamRequest extends FormRequest
{
	public function authorize(): bool
	{
		return true;
	}

	public function rules(): array
	{
		return [
			'firstname' => 'required|string|max:255',
			'name' => 'required|string|max:255',
			'role' => 'nullable|string|max:255',
			'position' => 'nullable|string|max:255',
			'phone' => 'nullable|string|max:50',
			'email' => 'required|string|max:255',
			'cv' => 'nullable|string',
			'publish' => 'boolean',
			'sort_order' => 'integer|min:0',
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
