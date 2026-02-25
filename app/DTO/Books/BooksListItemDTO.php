<?php

namespace App\DTO\Books;

class BooksListItemDTO
{
    public function __construct(
        public int $id,
        public string $title,
    ) {
    }
}
