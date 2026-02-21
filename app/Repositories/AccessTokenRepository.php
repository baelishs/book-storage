<?php

namespace App\Repositories;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Laravel\Sanctum\NewAccessToken;

class AccessTokenRepository implements AccessTokenRepositoryInterface
{
    public function delete(int $id): bool
    {
        DB::table('personal_access_tokens')
            ->where('id', '=', $id)
            ->delete();

        return true;
    }

    public function createUserToken(User $user): NewAccessToken
    {
        return $user->createToken('api-token');
    }

    public function deleteUserTokens(User $user): void
    {
        $user->tokens()->delete();
    }
}
