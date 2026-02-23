<?php

namespace App\Http\Controllers;

use App\Http\Resources\Common\PaginationResource;
use App\Http\Resources\User\UserResource;
use App\Services\UserService;
use Symfony\Component\HttpFoundation\JsonResponse;

class UserController extends Controller
{
    public function __construct(
        private readonly UserService $userService,
    ) {}

    public function index(): JsonResponse
    {
        $result = $this->userService->getUsers();

        return response()->json([
            'data' => UserResource::collection($result->items()),
            'meta' => new PaginationResource($result),
        ]);
    }
}
