<?php

namespace App\Http\Controllers;

use App\Services\BookService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

class UserBooksController extends Controller
{
    public function __construct(
        protected readonly BookService $bookService,
    )
    {}

    public function getUserBooks(int $userId, Request $request): JsonResponse
    {
        $currentUserId = $request->user()->id;

        return response()->json(
            $this->bookService->getUserBooksForViewer(
                ownerId: $userId,
                viewerId: $currentUserId
            )
        );
    }
}
