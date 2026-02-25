<?php

namespace App\DTO\Books;

use App\DTO\PaginationDTO;

class BooksListDTO
{
    /**
     * @param BooksListItemDTO[] $data
     * @param PaginationDTO $meta
     */
    public function __construct(
        public array $data,
        public PaginationDTO $meta,
    ) {
    }
}
