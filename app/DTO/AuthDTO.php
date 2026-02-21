<?php

namespace App\DTO;

class AuthDTO
{
    public function __construct(
        public string $login,
        public string $password,
    ) {}
}
