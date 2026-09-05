<?php

namespace App\Http\Resources\Order;

use App\Http\Resources\User\AddressResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
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
            'order_id' => $this->id,
            /**
             * @var integer
             * @example 1
             */
            'user_id' => $this->user_id,
            /**
             * @var float
             * @example 9.99
             */
            'total_payment' => $this->total,
            /**
             * @example "pending"
             */
            'status' => $this->status,
            /**
             * @var AddressResource
             */
            'address_snapshot' => $this->address_snapshot,
            /**
             * @var OrderItemResource
             */
            'items' => OrderItemResource::collection($this->items),
            'created_at' => $this->created_at->toDateTimeString(),
        ];
    }
}
