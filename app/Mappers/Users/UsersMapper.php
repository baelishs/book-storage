<?php

namespace App\Mappers\Users;

use App\DTO\PaginationDTO;
use App\DTO\Users\UserItemDTO;
use App\DTO\Users\UserListDTO;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class UsersMapper
{
    /**
     * @param LengthAwarePaginator<User> $list
     * @return UserListDTO
     */
    public function userListToDTO(LengthAwarePaginator $list): UserListDTO
    {
        $pagination = PaginationDTO::fromLengthAwarePaginator($list);
        $usersDTO = array_map(function (User $user) {
            return new UserItemDTO(
                id: $user->id,
                login: $user->login,
            );
        }, $list->items());

        return new UserListDTO(
            data: $usersDTO,
            meta: $pagination,
        );
    }
}
