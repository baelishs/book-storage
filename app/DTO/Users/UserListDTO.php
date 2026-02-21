<?php

namespace App\DTO\Users;

use App\DTO\PaginationDTO;

class UserListDTO
{
    /**
     * @param UserItemDTO[] $data
     * @param PaginationDTO $meta
     */
    public function __construct(
        public array $data,
        public PaginationDTO $meta,
    )
    {
    }
}
