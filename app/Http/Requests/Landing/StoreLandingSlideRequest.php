<?php

namespace App\Http\Requests\Landing;

use App\Http\Requests\Traits\HasMediaRules;
use Illuminate\Foundation\Http\FormRequest;

class StoreLandingSlideRequest extends FormRequest
{
	use HasMediaRules;
	public function authorize(): bool
	{
		return true;
	}

	public function rules(): array
	{
		return [
			'type' => 'required|in:image,image_text',
			'text' => 'nullable|required_if:type,image_text|string',
			'publish' => 'boolean',
			...$this->mediaRules(),
		];
	}

	public function messages(): array
	{
		return [
			'type.required' => 'Typ ist erforderlich',
			'type.in' => 'Ungültiger Typ',
			'text.required_if' => 'Text ist erforderlich für diesen Typ',
		];
	}
}
