<?php

namespace App\Exceptions\Users;

class UserNotFoundException extends \Exception
{
    public function __construct(int $id)
    {
        parent::__construct("User with id $id not found");
    }
}
