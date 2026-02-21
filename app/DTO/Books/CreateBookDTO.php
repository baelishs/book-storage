<?php

namespace App\DTO\Books;

class CreateBookDTO
{
    public function __construct(
        public int $userId,
        public string $title,
        public string $content,
    ) {}

    public function toArray(): array
    {
        return [
            'user_id' => $this->userId,
            'title' => $this->title,
            'content' => $this->content,
        ];
    }
}
