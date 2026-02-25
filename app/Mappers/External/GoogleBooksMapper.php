<?php

namespace App\Mappers\External;

use App\DTO\External\ExternalBookDTO;
use App\Providers\Books\GoogleBooksProvider;

class GoogleBooksMapper
{
    public function hydrateToExternalDTO(array $rawResponseList): array
    {
        return array_map(function (array $item) {
            return new ExternalBookDTO(
                externalId: (string) ($item['id'] ?? $item['article'] ?? uniqid('mif_')),
                title: $item['title'] ?? $item['name'] ?? 'Unknown Title',
                source: GoogleBooksProvider::SOURCE,
                description: $item['description'] ?? $item['annotation'] ?? $item['about'] ?? null,
                url: $item['url'] ?? $item['link'] ?? null,
            );
        }, $rawResponseList);
    }
}
