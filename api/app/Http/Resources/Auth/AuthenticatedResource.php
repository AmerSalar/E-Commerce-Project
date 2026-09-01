<?php

namespace App\Http\Resources\Auth;

use App\Http\Resources\User\UserResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\User;
use Override;

/**
 * @mixin User
 */
class AuthenticatedResource extends JsonResource
{
    // public static $wrap = null;
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {

        return [
            /**
             * @var string
             * @example "Logged-in successfully."
             */
            'message' => $this->message,
            /**
             * @var UserResource
             */
            'user' => $this->user
        ];
    }
}
