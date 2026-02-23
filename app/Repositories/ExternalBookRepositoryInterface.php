<?php

namespace App\Repositories;

use App\DTO\Books\ImportBookDTO;
use App\Models\Book;

interface ExternalBookRepositoryInterface
{
    public function existsByExternalId(string $externalId): bool;

    public function createFromImport(ImportBookDTO $dto): Book;
}
