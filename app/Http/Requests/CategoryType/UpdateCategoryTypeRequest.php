<?php

namespace App\Http\Requests\CategoryType;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCategoryTypeRequest extends FormRequest
{
	public function authorize(): bool
	{
		return true;
	}

	public function rules(): array
	{
		return [
			'name' => 'required|string|max:255',
			'name_singular' => 'nullable|string|max:255',
			'publish' => 'boolean',
		];
	}
}
