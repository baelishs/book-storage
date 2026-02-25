<?php

namespace App\Services;

use App\Models\User;
use App\Repositories\UserRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;

class UserService
{
    private const PAGINATION_PER_PAGE = 12;

    public function __construct(
        protected UserRepositoryInterface $userRepository,
    ) {
    }

    /**
     * @return LengthAwarePaginator<User>
     */
    public function getUsers(): LengthAwarePaginator
    {
        return $this->userRepository->getPaginated(
            perPage: self::PAGINATION_PER_PAGE,
        );
    }
}
