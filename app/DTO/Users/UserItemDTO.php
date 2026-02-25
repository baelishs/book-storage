<?php

namespace App\DTO\Users;

class UserItemDTO
{
    public function __construct(
        public int $id,
        public string $login,
    ) {
    }
}
