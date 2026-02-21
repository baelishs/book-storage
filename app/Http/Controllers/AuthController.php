<?php

namespace App\Http\Controllers;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
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
    public function login(LoginRequest $request): JsonResponse
    {
        return response()->json(
            $this->authService->login($request->toDto())
        );
    }

    /**
     * @throws ValidationException
     */
    public function register(RegisterRequest $request): JsonResponse
    {
        return response()->json(
            $this->authService->register($request->toDto())
        );
    }

    public function logout(Request $request): JsonResponse
    {
        $tokenId = $request->user()->currentAccessToken()->id;
        $this->authService->logout($tokenId);

        return response()->json(['message' => 'Logged out successfully']);
    }
}
