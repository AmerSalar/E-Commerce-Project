<?php

namespace App\Http\Requests\Product;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SaveProductRequest extends FormRequest
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
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:128'],
            'description' => ['nullable', 'string', 'max:1024'],
            'price' => ['required', 'numeric', 'min:0', 'decimal:0,2'],
            'quantity' => ['required', 'integer', 'between:0,50'],
            'category_ids' => ['required', 'array', 'min:1'], // check array
            'category_ids.*' => ['integer', 'distinct', 'exists:categories,id'] // check elements of array
        ];
    }
}
