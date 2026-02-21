<?php

namespace App\DTO;

class CreateUserDTO
{
    public function __construct(
        public string $login,
        public string $passwordHash,
    ) {}

    public function toArray(): array
    {
        return [
            'login' => $this->login,
            'password' => $this->passwordHash,
        ];
    }
}
