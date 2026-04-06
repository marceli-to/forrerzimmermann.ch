<?php

namespace App\Http\Requests\Atelier;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAtelierPageRequest extends FormRequest
{
	public function authorize(): bool
	{
		return true;
	}

	public function rules(): array
	{
		$isProfile = $this->route('page')?->slug === 'profile';

		return [
			'title' => ($isProfile ? 'required' : 'nullable') . '|string|max:255',
			'text' => ($isProfile ? 'required' : 'nullable') . '|string',
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
			'media.*.crop' => 'nullable|array',
		];
	}

	public function messages(): array
	{
		return [
			'title.required' => 'Titel ist erforderlich',
			'title.max' => 'Titel darf maximal 255 Zeichen lang sein',
			'text.required' => 'Text ist erforderlich',
		];
	}
}
