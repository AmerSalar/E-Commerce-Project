<?php

namespace App\Http\Resources\Cart;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CartItemResource extends JsonResource
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
             * @example 'GTA V'
             */
            'name' => $this->name,
            /**
             * @var float
             * @example 14.99
             */
            'price' => $this->price,
            /**
             * @var integer
             * @example 1
             */
            'quantity' => $this->pivot->quantity,
        ];
    }
}
