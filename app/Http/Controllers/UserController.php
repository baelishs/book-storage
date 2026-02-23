<?php

namespace App\Http\Controllers;

use App\Services\UserService;
use Symfony\Component\HttpFoundation\JsonResponse;

class UserController extends Controller
{
    public function __construct(
        private readonly UserService $userService,
    ) {}

    public function index(): JsonResponse
    {
        return response()->json(
            $this->userService->getUsers(),
        );
    }
}
