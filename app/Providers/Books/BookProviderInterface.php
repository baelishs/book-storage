<?php

namespace App\Providers\Books;

use App\DTO\External\ExternalBookDTO;
use App\Exceptions\Books\ExternalBookServiceException;
use Exception;

interface BookProviderInterface
{
    /**
     * @param string $query
     * @return ExternalBookDTO[]
     * @throws ExternalBookServiceException
     */
    public function search(string $query): array;
}
