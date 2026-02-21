<?php

namespace App\DTO\Books;

class UpdateBookDTO
{
    public function __construct(
        public int $id,
        public int $userId,
        public string $title,
        public string $content,
    ) {}

    public function toArray(): array
    {
        return [
            'title' => $this->title,
            'content' => $this->content,
        ];
    }
}
