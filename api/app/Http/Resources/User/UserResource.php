<?php

namespace App\Http\Resources\User;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
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
             * @example "Ameer Salar"
             */
            'name' => $this->name,
            /**
             * @example "ameersalar@gmail.com"
             */
            'email' => $this->email,
            /**
             * @var array<integer>
             * @example [1,2,3]
             */
            'roles' => RoleResource::collection($this->whenLoaded('roles')),
            /**
             * @var AddressResource
             */
            'addresses' => AddressResource::collection($this->whenLoaded('addresses')),
        ];
    }
}
