<?php

namespace App\Http\Controllers;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Resources\Auth\AuthResource;
use App\Services\AuthService;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\JsonResponse;

class AuthController extends Controller
{
    public function __construct(
        private readonly AuthService $authService,
    ) {}

    /**
     * @throws ValidationException
     */
    public function login(LoginRequest $request): AuthResource
    {
        $result = $this->authService->login(
            authDTO: $request->toDto(),
        );

        return new AuthResource($result);
    }

    /**
     * @throws ValidationException
     */
    public function register(RegisterRequest $request): AuthResource
    {
        $result = $this->authService->register(
            authDTO: $request->toDto()
        );

        return new AuthResource($result);
    }

    /**
     * @throws ValidationException
     */
    public function logout(Request $request): JsonResponse
    {
        $this->authService->logout(
            tokenId: $request->user()->currentAccessToken()->id,
        );

        return response()->json(['message' => 'Logged out successfully']);
    }
}
