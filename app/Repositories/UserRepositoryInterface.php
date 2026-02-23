<?php

namespace App\Repositories;

use App\DTO\Users\CreateUserDTO;
use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;


interface UserRepositoryInterface
{
    public function findByLogin(string $login): ?User;

    public function create(CreateUserDTO $data): User;

    /**
     * @param int $perPage
     * @return LengthAwarePaginator<User>
     */
    public function getPaginated(int $perPage = 12): LengthAwarePaginator;

    public function exist(int $id): bool;
}
