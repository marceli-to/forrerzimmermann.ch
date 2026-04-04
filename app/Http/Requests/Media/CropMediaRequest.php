<?php

namespace App\Http\Requests\Media;

use Illuminate\Foundation\Http\FormRequest;

class CropMediaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'x' => 'nullable|integer|min:0',
            'y' => 'nullable|integer|min:0',
            'w' => 'nullable|integer|min:1',
            'h' => 'nullable|integer|min:1',
        ];
    }
}
