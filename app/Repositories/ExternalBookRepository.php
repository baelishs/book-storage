<?php

namespace App\Repositories;

use App\DTO\Books\ImportBookDTO;
use App\Models\Book;

class ExternalBookRepository implements ExternalBookRepositoryInterface
{
    public function existsByExternalId(string $externalId): bool
    {
        return Book::query()->where('external_id', $externalId)->exists();
    }

    public function createFromImport(ImportBookDTO $dto): Book
    {
        return Book::create($dto->toArray());
    }
}
