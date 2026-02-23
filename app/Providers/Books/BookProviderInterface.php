<?php

namespace App\Providers\Books;

use App\DTO\External\ExternalBookDTO;
use Exception;

interface BookProviderInterface
{
    /**
     * @param string $query
     * @return ExternalBookDTO[]
     */
    public function search(string $query): array;
}
