<?php

namespace App\Http\Requests\Product;

use Illuminate\Http\UploadedFile;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use phpDocumentor\Reflection\Types\Integer;

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
            /**
             * @var string
             * @example "GTA V"
             */
            'name' => [
                'required',
                'string',
                'max:128',
                Rule::unique('products', 'name')->ignore($this->route('product'))
            ],
            /**
             * @var string
             * @example "Grand Theft Auto 5"
             */
            'description' => ['nullable', 'string', 'max:1024'],
            /**
             * @var float
             * @example 4.99
             */
            'price' => ['required', 'numeric', 'min:0', 'decimal:0,2'],
            /**
             * @var integer
             * @example 1
             */
            'quantity' => ['required', 'integer', 'between:0,50'],
            /**
             * @var UploadedFile
             * @example "file with extensions: jpeg, jpg, png, webp"
             */
            'picture' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:2048',
                'dimensions:min_width=256,min_height=256,max_width=4096,max_height=4096'
            ],
            /**
             * @var array<integer>
             * @example [1,2,3]
             */
            'category_ids' => ['required', 'array', 'min:1'], // check array
            'category_ids.*' => ['integer', 'distinct', 'exists:categories,id'] // check elements of array
        ];
    }
}
