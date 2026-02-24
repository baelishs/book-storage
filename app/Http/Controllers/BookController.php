<?php

namespace App\Http\Controllers;

use App\Exceptions\Books\BookNotFoundException;
use App\Http\Requests\CreateBookRequest;
use App\Http\Requests\UpdateBookRequest;
use App\Http\Resources\Books\BooksListResource;
use App\Http\Resources\Books\BooksResource;
use App\Http\Resources\Common\PaginationResource;
use App\Services\BookService;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

class BookController extends Controller
{
    public function __construct(
        private readonly BookService $bookService,
    ) {}

    public function index(Request $request): JsonResponse
    {
        $result = $this->bookService->getUserBooks(
            userId: $request->user()->id
        );

        return response()->json([
            'data' => BooksListResource::collection($result->items()),
            'meta' => new PaginationResource($result),
        ]);
    }

    public function store(CreateBookRequest $request): JsonResponse
    {
        $book = $this->bookService->createBook($request->toDto());

        return (new BooksResource($book))->response()->setStatusCode(201);
    }

    /**
     * @throws AuthorizationException
     * @throws BookNotFoundException
     */
    public function show(int $id, Request $request): BooksResource
    {
        $book = $this->bookService->getBook(
            id: $id,
            userId: $request->user()->id
        );

        return new BooksResource($book);
    }

    /**
     * @throws BookNotFoundException
     */
    public function update(UpdateBookRequest $request): BooksResource
    {
        $book = $this->bookService->updateBook(
            dto: $request->toDto(),
        );

        return new BooksResource($book);
    }

    /**
     * @throws AuthorizationException
     * @throws BookNotFoundException
     */
    public function destroy(int $id, Request $request): JsonResponse
    {
        $this->bookService->deleteBook($id, $request->user()->id);

        return response()->json(['message' => 'Book deleted successfully']);
    }

    /**
     * @throws AuthorizationException
     * @throws BookNotFoundException
     */
    public function restore(int $id, Request $request): JsonResponse
    {
        $this->bookService->restoreBook(
            id: $id,
            userId: $request->user()->id,
        );

        return response()->json(['message' => 'Book restored successfully']);
    }
}
