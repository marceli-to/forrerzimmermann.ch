<?php

namespace App\Http\Requests\Atelier;

use App\Http\Requests\Traits\HasMediaRules;
use Illuminate\Foundation\Http\FormRequest;

class UpdateAtelierPageRequest extends FormRequest
{
	use HasMediaRules;
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
			...$this->mediaRules(),
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
