<?php

namespace App\Exceptions\Books;

class BookNotFoundException extends \Exception
{
    public function __construct(int $id)
    {
        parent::__construct("Book with id $id not found");
    }
}
