<?php

namespace App\DTO\Auth;

class AuthResultDTO
{
    public function __construct(
        public string $token,
        public string $token_type,
    ) {
    }
}
