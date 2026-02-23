<?php

namespace App\Mappers\External;


use App\DTO\External\ExternalBookDTO;
use App\Providers\Books\MannIvanovFerberProvider;

class ManIvanovFerberMapper
{
    /**
     * @param array $rawResponseList
     * @return ExternalBookDTO[]
     */
    public function hydrateToExternalDTO(array $rawResponseList): array
    {
        return array_map(function(array $item) {
            return new ExternalBookDTO(
                externalId: (string) ($item['id'] ?? $item['article'] ?? uniqid('mif_')),
                title: $item['title'] ?? $item['name'] ?? 'Unknown Title',
                source: MannIvanovFerberProvider::SOURCE,
                description: $item['description'] ?? $item['annotation'] ?? $item['about'] ?? null,
                url: $item['url'] ?? $item['link'] ?? null,
            );
        }, $rawResponseList);
    }
}
