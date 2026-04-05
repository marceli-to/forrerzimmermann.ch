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
			'title' => 'nullable|string|max:255',
			'email' => 'nullable|string|email',
			'cv' => 'nullable|string',
			'publish' => 'boolean',
			'former' => 'boolean',
		];
	}
}
