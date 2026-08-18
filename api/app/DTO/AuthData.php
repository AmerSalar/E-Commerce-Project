<?php

namespace App\DTO;

use App\Models\User;
use PHPOpenSourceSaver\JWTAuth\Authenticatable;

readonly class AuthData
{
    public function __construct(
        public mixed $user,
        public string $token,
        public string $message = "Success"
    ) {}
}
