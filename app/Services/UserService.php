<?php

namespace App\Services;

use App\Http\Resources\UserResource;
use App\Repositories\UserRepositoryInterface;

class UserService
{
    private const PAGINATION_PER_PAGE = 12;

    public function __construct(
        protected UserRepositoryInterface $userRepository,
    )
    {
    }

    public function getUsers(): array
    {
        $users = $this->userRepository->getPaginated(self::PAGINATION_PER_PAGE);

        return [
            'data' => UserResource::collection($users->items()),
            'meta' => [
                'current_page' => $users->currentPage(),
                'last_page' => $users->lastPage(),
                'per_page' => $users->perPage(),
                'total' => $users->total(),
            ],
        ];
    }
}
