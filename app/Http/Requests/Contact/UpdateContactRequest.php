<?php

namespace App\Http\Requests\Contact;

use Illuminate\Foundation\Http\FormRequest;

class UpdateContactRequest extends FormRequest
{
	public function authorize(): bool
	{
		return true;
	}

	public function rules(): array
	{
		return [
			'name' => 'required|string|max:255',
			'address' => 'required|string|max:255',
			'email' => 'required|string|email',
			'phone' => 'required|string|max:255',
			'maps_url' => 'nullable|string|url',
			'imprint' => 'nullable|string',
			'meta_description' => 'nullable|string|max:255',
			'publish' => 'boolean',
		];
	}
}
