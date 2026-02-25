<?php

namespace App\DTO\Books;

class ImportBookDTO
{
    public function __construct(
        public int $userId,
        public string $externalId,
        public string $source,
        public string $title,
        public ?string $description = null,
        public ?string $url = null,
    ) {
    }

    public function getContent(): string
    {
        return $this->description ?? $this->url ?? '';
    }

    public function toArray(): array
    {
        return [
            'user_id' => $this->userId,
            'external_id' => $this->externalId,
            'title' => $this->title,
            'content' => $this->getContent(),
        ];
    }
}
