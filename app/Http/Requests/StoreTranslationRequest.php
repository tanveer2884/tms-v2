<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTranslationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'locale' => 'required|string',
            'key' => 'required|string|unique:translations,key',
            'content' => 'required|string',
            'tags' => 'nullable|array',
            'tags.*' => 'string|distinct'
        ];
    }

    public function messages(): array
    {
        return [
            'key.unique' => 'The translation key must be unique.',
            'tags.*.string' => 'Each tag must be a string.',
            'tags.*.distinct' => 'Duplicate tags are not allowed.',
        ];
    }
}
