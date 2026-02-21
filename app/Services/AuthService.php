<?php

namespace App\Services;

use App\DTO\CreateUserDTO;
use App\Repositories\AccessTokenRepositoryInterface;
use App\Repositories\UserRepositoryInterface;
use App\DTO\AuthDTO;
use Illuminate\Validation\ValidationException;

readonly class AuthService
{
    private const AUTHORIZATION_TOKEN_TYPE = 'Bearer';

    public function __construct(
        protected UserRepositoryInterface $userRepository,
        protected AccessTokenRepositoryInterface $tokenRepository,
        protected AuthHasher $hasher,
    ) {}

    /**
     * @throws ValidationException
     */
    public function login(AuthDTO $authDTO): array
    {
        if (!$user = $this->userRepository->findByLogin($authDTO->login)) {
            throw ValidationException::withMessages([
                'login' => ['Login is not exist'],
            ]);
        }

        if (!$this->hasher->check($authDTO->password, $user->password)) {
            throw ValidationException::withMessages([
                'login' => ['Invalid credentials'],
            ]);
        }

        $this->tokenRepository->deleteUserTokens($user);
        $newAccessToken = $this->tokenRepository->createUserToken($user);

        return [
            'token' => $newAccessToken->plainTextToken,
            'token_type' => 'Bearer',
        ];
    }

    /**
     * @throws ValidationException
     */
    public function register(AuthDTO $data): array
    {
        if ($this->userRepository->findByLogin($data->login)) {
            throw ValidationException::withMessages(['login' => ['Login already exists']]);
        }

        $user = $this->userRepository->create(new CreateUserDTO(
            login: $data->login,
            passwordHash: $this->hasher->create($data->password),
        ));

        $newAccessToken = $this->tokenRepository->createUserToken($user);

        return [
            'token' => $newAccessToken->plainTextToken,
            'token_type' => self::AUTHORIZATION_TOKEN_TYPE,
        ];
    }

    public function logout(int $tokenId): void
    {
        $this->tokenRepository->delete($tokenId);
    }
}
