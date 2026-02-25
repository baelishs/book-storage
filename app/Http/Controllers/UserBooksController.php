<?php

namespace App\Http\Controllers;

use App\Exceptions\Users\UserNotFoundException;
use App\Http\Resources\Books\BooksListResource;
use App\Http\Resources\Common\PaginationResource;
use App\Services\BookService;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

class UserBooksController extends Controller
{
    public function __construct(
        protected readonly BookService $bookService,
    ) {
    }

    /**
     * @throws UserNotFoundException
     * @throws AuthorizationException
     */
    public function getUserBooks(int $userId, Request $request): JsonResponse
    {
        $result = $this->bookService->getUserBooksForViewer(
            ownerId: $userId,
            viewerId: $request->user()->id,
        );

        return response()->json([
            'data' => BooksListResource::collection($result->items()),
            'meta' => new PaginationResource($result),
        ]);
    }
}
