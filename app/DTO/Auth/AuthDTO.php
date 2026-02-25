<?php

namespace App\DTO\Auth;

class AuthDTO
{
    public function __construct(
        public string $login,
        public string $password,
    ) {
    }
}
