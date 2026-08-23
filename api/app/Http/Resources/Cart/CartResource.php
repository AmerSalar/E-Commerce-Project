<?php

namespace App\Http\Resources\Cart;

use App\Http\Resources\Product\ProductResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CartResource extends JsonResource
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
             * @var float
             * @example 9.99
             */
            'total' => $this->total,
            /**
             * @var CartItemResource
             */
            'items' => CartItemResource::collection($this->items),
        ];
    }
}
