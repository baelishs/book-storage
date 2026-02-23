<?php

namespace App\Exceptions\Books;

use Exception;
use Throwable;

class ExternalBookServiceException extends Exception
{
    public function __construct(
        string $message = 'External book service unavailable',
        int $code = 503,
        ?Throwable $previous = null,
    ) {
        parent::__construct($message, $code, $previous);
    }
}
