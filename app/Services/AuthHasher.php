<?php

namespace App\Services;

use Illuminate\Support\Facades\Hash;

class AuthHasher
{
    public function check(string $passwordToAuth, string $userPassword): bool
    {
        return Hash::check($passwordToAuth, $userPassword);
    }

    public function create(string $passwordToHash): string
    {
        return Hash::make($passwordToHash);
    }
}
