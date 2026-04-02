<?php

namespace App\Http\Requests\CategoryType;

use Illuminate\Foundation\Http\FormRequest;

class ReorderCategoryTypeRequest extends FormRequest
{
	public function authorize(): bool
	{
		return true;
	}

	public function rules(): array
	{
		return [
			'items' => 'required|array',
			'items.*.id' => 'required|integer|exists:category_types,id',
			'items.*.sort_order' => 'required|integer',
		];
	}
}
