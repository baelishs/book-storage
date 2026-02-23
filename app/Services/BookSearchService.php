<?php

namespace App\Services;

use App\DTO\Books\ImportBookDTO;
use App\DTO\External\ExternalBookDTO;
use App\Exceptions\Books\ExternalBookServiceException;
use App\Models\Book;
use App\Providers\Books\BookSearchStrategyResolver;
use App\Repositories\ExternalBookRepositoryInterface;

class BookSearchService
{
    public function __construct(
        private readonly BookSearchStrategyResolver $strategyResolver,
        private readonly ExternalBookRepositoryInterface $externalBookRepository,
    ) {}

    /**
     * @param string $query
     * @return ExternalBookDTO[]
     */
    public function searchAll(string $query): array
    {
        $results = [];

        foreach ($this->strategyResolver->getProviders() as $provider) {
            try {
                $providerResults = $provider->search($query);
                $results = array_merge($results, $providerResults);
            } catch (ExternalBookServiceException $e) {
                continue;
            }
        }

        return $results;
    }

    /**
     * @throws ExternalBookServiceException
     */
    public function importBook(ImportBookDTO $dto): Book
    {
        if ($this->externalBookRepository->existsByExternalId(externalId: $dto->externalId)) {
            throw new ExternalBookServiceException(
                message: 'Book with this external_id already exists for this user',
                code: 409,
            );
        }

        return $this->externalBookRepository->createFromImport($dto);
    }
}
