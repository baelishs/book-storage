<?php

namespace App\Repositories;

use App\DTO\CreateUserDTO;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface UserRepositoryInterface
{
    public function findByLogin(string $login): ?User;

    public function create(CreateUserDTO $data): User;

    public function getPaginated(int $perPage = 12): LengthAwarePaginator;
}
