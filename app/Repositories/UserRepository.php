<?php

namespace App\Repositories;

use App\DTO\CreateUserDTO;
use App\Models\User;

class UserRepository implements UserRepositoryInterface
{
    public function findByLogin(string $login): ?User
    {
        return User::where('login', $login)->first();
    }

    public function create(CreateUserDTO $data): User
    {
        return User::create($data->toArray());
    }
}
