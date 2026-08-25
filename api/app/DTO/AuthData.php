<?php

namespace App\DTO;


readonly class AuthData
{
    public function __construct(
        // this is an example of IoC and service container.
        // dependencies will automatically be injected
        public mixed $user,
        public string $token,
        public string $message = "Success"
    ) {}
}
