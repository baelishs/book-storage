<?php

namespace App\DTO;

class LogoutDTO
{
    public function __construct(
        public int $userId,
        public ?string $tokenId = null
    ) {}
}
