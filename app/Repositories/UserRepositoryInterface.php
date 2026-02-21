<?php

namespace App\Repositories;

use App\DTO\CreateUserDTO;
use App\Models\User;

interface UserRepositoryInterface
{
    public function findByLogin(string $login): ?User;

    public function create(CreateUserDTO $data): User;
}
