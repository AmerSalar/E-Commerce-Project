<?php

namespace App\Http\Resources\Order;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderItemResource extends JsonResource
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
            'product_id' => $this->id,
            /**
             * @example "GTA V"
             */
            'item_name' => $this->name,
            /**
             * @var float
             * @example 9.99
             */
            'item_price' => $this->price,
            /**
             * @var integer
             * @example 1
             */
            'quantity' => $this->pivot->quantity,
        ];
    }
}
