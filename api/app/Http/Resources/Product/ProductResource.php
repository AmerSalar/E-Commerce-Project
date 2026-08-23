<?php

namespace App\Http\Resources\Product;

use App\Http\Resources\Category\CategoryResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
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
             * @example 'GTA V'
             */
            'name' => $this->name,
            /**
             * @var string
             * @example "Grand Theft Auto 5"
             */
            'description' => $this->description,
            /**
             * @var float
             * @example 9.99
             */
            'price' => $this->price,
            /**
             * @example 1
             */
            'quantity' => $this->quantity,
            /**
             * @var string
             * @example "products/034cf7dd-15a8-4fee-90cc-c251700efd5c.webp"
             */
            'picture_url' => $this->picture_url,

            /**
             * @var CategoryResource
             */
            'categories' => CategoryResource::collection(
                $this->whenLoaded('categories')
            ),
        ];
    }
}
