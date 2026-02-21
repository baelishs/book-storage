<?php

namespace App\Repositories;

use App\Models\User;
use Laravel\Sanctum\NewAccessToken;
use Laravel\Sanctum\PersonalAccessToken;

class AccessTokenRepository implements AccessTokenRepositoryInterface
{
    public function delete(PersonalAccessToken $model): bool
    {
        return $model->delete();
    }

    public function createUserToken(User $user): NewAccessToken
    {
        return $user->createToken('api-token');
    }

    public function deleteUserTokens(User $user): void
    {
        $user->tokens()->delete();
    }

    public function findById(int $id): ?PersonalAccessToken
    {
        return PersonalAccessToken::query()->where('id', '=', $id)->first();
    }
}
