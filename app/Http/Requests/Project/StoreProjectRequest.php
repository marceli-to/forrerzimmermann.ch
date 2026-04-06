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
			'topic_id' => 'nullable|string|exists:topics,uuid',
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
			'year.required' => 'Jahr ist erforderlich',
			'year.integer' => 'Jahr muss eine Zahl sein',
			'location.max' => 'Ort darf maximal 255 Zeichen lang sein',
			'subtitle.max' => 'Untertitel darf maximal 255 Zeichen lang sein',
			'meta_description.max' => 'Meta Description darf maximal 255 Zeichen lang sein',
			'topic_id.exists' => 'Das gewählte Thema ist ungültig',
		];
	}
}
