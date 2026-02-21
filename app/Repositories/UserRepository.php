<?php

namespace App\Repositories;

use App\DTO\Users\CreateUserDTO;
use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;


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

    public function getPaginated(int $perPage = 12): LengthAwarePaginator
    {
        return User::query()
            ->select('id', 'login')
            ->orderBy('id')
            ->paginate($perPage);
    }
}
