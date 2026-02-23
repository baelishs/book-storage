<?php

namespace Tests\Unit;

use App\DTO\Auth\AuthDTO;
use App\Models\User;
use App\Repositories\AccessTokenRepositoryInterface;
use App\Repositories\UserRepositoryInterface;
use App\Services\AuthHasher;
use App\Services\AuthService;
use Illuminate\Validation\ValidationException;
use Laravel\Sanctum\NewAccessToken;
use Laravel\Sanctum\PersonalAccessToken;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\MockObject\MockObject;
use Tests\TestCase;

class AuthServiceTest extends TestCase
{
    private UserRepositoryInterface|MockObject $userRepositoryMock;
    protected AccessTokenRepositoryInterface|MockObject $tokenRepository;
    protected AuthHasher|MockObject $hasher;

    public function setUp(): void
    {
        $this->userRepositoryMock = $this->getMockBuilder(UserRepositoryInterface::class)->getMock();
        $this->tokenRepository = $this->getMockBuilder(AccessTokenRepositoryInterface::class)->getMock();
        $this->hasher = $this->getMockBuilder(AuthHasher::class)->getMock();

        parent::setUp();
    }

    public static function failedAuthProvider(): array
    {
        return [
            'Test 1. Login not found, expect ValidationException' => [
                'userRepoBehaviour' => function (MockObject $repository, self $testCase) {
                    $repository->expects($testCase->once())->method('findByLogin')->willReturn(null);
                },
                'hasherBehaviour' => function (MockObject $hasher, self $testCase) {
                    $hasher->expects($testCase->never())->method('check');
                },
            ],
            'Test 2. Hasher check failed, expects ValidationException' => [
                'userRepoBehaviour' => function (MockObject $repository, self $testCase) {
                    $mockUser = new User();
                    $mockUser->password = '111';
                    $repository->expects($testCase->once())->method('findByLogin')->willReturn($mockUser);
                },
                'hasherBehaviour' => function (MockObject $hasher, self $testCase) {
                    $hasher->expects($testCase->once())->method('check')->willReturn(false);
                },
            ],
        ];
    }

    #[DataProvider('failedAuthProvider')]
    public function testFailedAuth(
        callable $userRepoBehaviour,
        callable $hasherBehaviour,
    )
    {
        $authDTO = new AuthDTO(
            login: '111',
            password: '111',
        );

        $this->tokenRepository->expects($this->never())->method('deleteUserTokens');
        $this->tokenRepository->expects($this->never())->method('createUserToken');

        $userRepoBehaviour($this->userRepositoryMock, $this);
        $hasherBehaviour($this->hasher, $this);

        $service = new AuthService(
            userRepository: $this->userRepositoryMock,
            tokenRepository: $this->tokenRepository,
            hasher: $this->hasher,
        );

        $this->expectException(ValidationException::class);
        $service->login($authDTO);
    }

    public function testSuccessfulAuth()
    {
        $authDTO = new AuthDTO(
            login: '123',
            password: '123',
        );

        $mockAccessToken = $this->getMockBuilder(NewAccessToken::class)->disableOriginalConstructor()->getMock();
        $mockAccessToken->plainTextToken = '11';

        $this->tokenRepository->expects($this->once())->method('deleteUserTokens');
        $this->tokenRepository->expects($this->once())->method('createUserToken')->willReturn($mockAccessToken);

        $mockUser = new User();
        $mockUser->password = '111';
        $this->userRepositoryMock->expects($this->once())->method('findByLogin')->willReturn($mockUser);

        $this->hasher->expects($this->once())->method('check')->willReturn(true);

        $service = new AuthService(
            userRepository: $this->userRepositoryMock,
            tokenRepository: $this->tokenRepository,
            hasher: $this->hasher,
        );

        $result = $service->login($authDTO);

        $this->assertSame($result->token_type, 'Bearer');
        $this->assertSame($result->token, '11');
    }

    public static function registerFailedProvider(): array
    {
        return [
            'Test 1. Login already exists, expect ValidationException' => [
                'userRepoBehaviour' => function (MockObject $repository, self $testCase) {
                    $mockUser = new User();
                    $repository->expects($testCase->once())->method('findByLogin')->willReturn($mockUser);
                },
            ],
        ];
    }

    #[DataProvider('registerFailedProvider')]
    public function testRegisterFailed(
        callable $userRepoBehaviour,
    )
    {
        $authDTO = new AuthDTO(
            login: 'test_user',
            password: 'password123',
        );

        $this->tokenRepository->expects($this->never())->method('deleteUserTokens');
        $this->tokenRepository->expects($this->never())->method('createUserToken');
        $this->hasher->expects($this->never())->method('create');

        $userRepoBehaviour($this->userRepositoryMock, $this);

        $service = new AuthService(
            userRepository: $this->userRepositoryMock,
            tokenRepository: $this->tokenRepository,
            hasher: $this->hasher,
        );

        $this->expectException(ValidationException::class);
        $service->register($authDTO);
    }

    public function testRegisterSuccessful()
    {
        $authDTO = new AuthDTO(
            login: 'new_user',
            password: 'password123',
        );

        $mockAccessToken = $this->getMockBuilder(NewAccessToken::class)->disableOriginalConstructor()->getMock();
        $mockAccessToken->plainTextToken = 'new_token_123';

        $this->userRepositoryMock->expects($this->once())->method('findByLogin')->willReturn(null);
        $this->hasher->expects($this->once())->method('create')->with('password123')->willReturn('hashed_password');
        $this->tokenRepository->expects($this->once())->method('createUserToken')->willReturn($mockAccessToken);

        $mockUser = new User();
        $mockUser->login = 'new_user';
        $this->userRepositoryMock->expects($this->once())->method('create')->willReturn($mockUser);

        $service = new AuthService(
            userRepository: $this->userRepositoryMock,
            tokenRepository: $this->tokenRepository,
            hasher: $this->hasher,
        );

        $result = $service->register($authDTO);

        $this->assertSame($result->token_type, 'Bearer');
        $this->assertSame($result->token, 'new_token_123');
    }

    public function testLogoutSuccessful()
    {
        $tokenId = 123;

        $mockToken = $this->getMockBuilder(PersonalAccessToken::class)->disableOriginalConstructor()->getMock();
        $mockToken->id = $tokenId;

        $this->tokenRepository->expects($this->once())->method('findById')->with($tokenId)->willReturn($mockToken);
        $this->tokenRepository->expects($this->once())->method('delete')->with($mockToken);

        $service = new AuthService(
            userRepository: $this->userRepositoryMock,
            tokenRepository: $this->tokenRepository,
            hasher: $this->hasher,
        );

        $service->logout($tokenId);
    }

    public function testLogoutFailed()
    {
        $tokenId = 999;

        $this->tokenRepository->expects($this->once())->method('findById')->with($tokenId)->willReturn(null);
        $this->tokenRepository->expects($this->never())->method('delete');

        $service = new AuthService(
            userRepository: $this->userRepositoryMock,
            tokenRepository: $this->tokenRepository,
            hasher: $this->hasher,
        );

        $this->expectException(ValidationException::class);
        $service->logout($tokenId);
    }
}
