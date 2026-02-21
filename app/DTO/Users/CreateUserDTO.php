<?php

namespace App\DTO\Users;

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
