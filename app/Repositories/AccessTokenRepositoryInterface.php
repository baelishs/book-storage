<?php

namespace App\Repositories;

use App\Models\User;
use Laravel\Sanctum\NewAccessToken;

interface AccessTokenRepositoryInterface
{
    public function delete(int $id): bool;

    public function createUserToken(User $user): NewAccessToken;

    public function deleteUserTokens(User $user): void;
}
