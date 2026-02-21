<?php

namespace App\Repositories;

use App\Models\User;
use Laravel\Sanctum\NewAccessToken;
use Laravel\Sanctum\PersonalAccessToken;

interface AccessTokenRepositoryInterface
{
    public function delete(PersonalAccessToken $model): bool;

    public function findById(int $id): ?PersonalAccessToken;

    public function createUserToken(User $user): NewAccessToken;

    public function deleteUserTokens(User $user): void;
}
