<?php

namespace App\Http\Requests\Address;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class AddressSnapshotRequest extends FormRequest
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
             * @example "07501234567"
             */
            'phone' => ['required', 'string', 'max:64'],
            /**
             * @var string
             * @example "Slemani"
             */
            'city' => ['required', 'string', 'max:64'],
            /**
             * @var string
             * @example "Salim st."
             */
            'street' => ['required', 'string', 'max:64'],
            /**
             * @var string
             * @example "37"
             */
            'building' => ['required', 'integer', 'min:1'],
        ];
    }
}
