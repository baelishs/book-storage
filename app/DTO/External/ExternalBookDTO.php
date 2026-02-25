<?php

namespace App\DTO\External;

class ExternalBookDTO
{
    public function __construct(
        public string $externalId,
        public string $title,
        public string $source = '',
        public ?string $description = null,
        public ?string $url = null,
    ) {
    }
}
