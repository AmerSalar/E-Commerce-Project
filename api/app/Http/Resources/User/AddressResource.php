<?php

namespace App\Http\Resources\User;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AddressResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            /**
             * @example 1
             */
            'id' => $this->id,
            /**
             * @var string
             * @example '07501234567'
             */
            'phone' => $this->phone,
            /**
             * @example "Slemani"
             */
            'city' => $this->city,
            /**
             * @example "Mawlawi st."
             */
            'street' => $this->street,
            /**
             * @var integer
             * @example 18
             */
            'building' => $this->building,
        ];
    }
}
