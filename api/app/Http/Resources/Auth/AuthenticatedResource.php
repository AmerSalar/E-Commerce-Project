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
             * @var string
             * @example "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOi8vYXBpLnRlc3QvYXBpL2xvZ2luIiwiaWF0IjoxNzg3NDA3OTAxLCJleHAiOjE3ODc0MTE1MDEsIm5iZiI6MTc4NzQwNzkwMSwianRpIjoiYWJLM2V2d3RESjI5MWZ5eCIsInN1YiI6IjEiLCJwcnYiOiIyM2JkNWM4OTQ5ZjYwMGFkYjM5ZTcwMWM0MDA4NzJkYjdhNTk3NmY3IiwibmFtZSI6IkFtZWVyIFNhbGFyIiwiZW1haWwiOiJhbWVlckBnbWFpbC5jb20ifQ.lxDe4vwmfCgn7hEXeJ-mN5gmbw42n7D-zQyizWz5lrc"
             */
            'token' => $this->token,
            /**
             * @var UserResource
             */
            'user' => $this->user
        ];
    }
}
