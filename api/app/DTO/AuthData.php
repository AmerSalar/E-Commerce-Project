<?php

namespace App\DTO;


readonly class AuthData
{
    public function __construct(
        public mixed $user,
        public string $token,
        public string $message = "Success"
    ) {}
}
