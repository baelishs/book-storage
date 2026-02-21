<?php

namespace App\Services;

use App\DTO\Users\UserListDTO;
use App\Mappers\Users\UsersMapper;
use App\Repositories\UserRepositoryInterface;

class UserService
{
    private const PAGINATION_PER_PAGE = 12;

    public function __construct(
        protected UserRepositoryInterface $userRepository,
        protected UsersMapper $mapper,
    )
    {
    }

    public function getUsers(): UserListDTO
    {
        return $this->mapper->userListToDTO(
            list: $this->userRepository->getPaginated(self::PAGINATION_PER_PAGE),
        );
    }
}
