<?php

namespace App\Http\Resources\Category;

use App\Http\Resources\Product\ProductResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CategoryResource extends JsonResource
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
             * @example "console"
             */
            'name' => $this->name,

            /**
             * @var ProductResource
             */
            'products' => ProductResource::collection($this->whenLoaded('products')),
        ];
    }
}
