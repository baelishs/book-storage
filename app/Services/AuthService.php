<?php

namespace App\Services;

use App\DTO\Auth\AuthDTO;
use App\DTO\Auth\AuthResultDTO;
use App\DTO\Users\CreateUserDTO;
use App\Repositories\AccessTokenRepositoryInterface;
use App\Repositories\UserRepositoryInterface;
use Illuminate\Validation\ValidationException;

class AuthService
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
    public function login(AuthDTO $authDTO): AuthResultDTO
    {
        if (!$user = $this->userRepository->findByLogin($authDTO->login)) {
            throw ValidationException::withMessages(['login' => ['Login is not exist']]);
        }

        if (!$this->hasher->check($authDTO->password, $user->password)) {
            throw ValidationException::withMessages(['login' => ['Invalid credentials']]);
        }

        $this->tokenRepository->deleteUserTokens($user);
        $newAccessToken = $this->tokenRepository->createUserToken($user);

        return new AuthResultDTO(
            token: $newAccessToken->plainTextToken,
            token_type: self::AUTHORIZATION_TOKEN_TYPE,
        );
    }

    /**
     * @throws ValidationException
     */
    public function register(AuthDTO $authDTO): AuthResultDTO
    {
        if ($this->userRepository->findByLogin($authDTO->login)) {
            throw ValidationException::withMessages(['login' => ['Login already exists']]);
        }

        $user = $this->userRepository->create(new CreateUserDTO(
            login: $authDTO->login,
            passwordHash: $this->hasher->create($authDTO->password),
        ));

        $newAccessToken = $this->tokenRepository->createUserToken($user);

        return new AuthResultDTO(
            token: $newAccessToken->plainTextToken,
            token_type: self::AUTHORIZATION_TOKEN_TYPE,
        );
    }

    /**
     * @throws ValidationException
     */
    public function logout(int $tokenId): void
    {
        if (!$tokenModel = $this->tokenRepository->findById($tokenId)) {
            throw ValidationException::withMessages(['token' => ['Invalid token']]);
        }

        $this->tokenRepository->delete($tokenModel);
    }
}
