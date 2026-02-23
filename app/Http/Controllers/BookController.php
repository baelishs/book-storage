<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateBookRequest;
use App\Http\Requests\UpdateBookRequest;
use App\Services\BookService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

class BookController extends Controller
{
    public function __construct(
        private readonly BookService $bookService,
    ) {}

    public function index(Request $request): JsonResponse
    {
        return response()->json(
            $this->bookService->getUserBooks($request->user()->id)
        );
    }

    public function store(CreateBookRequest $request): JsonResponse
    {
        $book = $this->bookService->createBook($request->toDto());

        return response()->json($book, 201);
    }

    public function show(int $id, Request $request): JsonResponse
    {
        return response()->json(
            $this->bookService->getBook($id, $request->user()->id)
        );
    }

    public function update(UpdateBookRequest $request): JsonResponse
    {
        return response()->json(
            $this->bookService->updateBook(
                dto: $request->toDto(),
            )
        );
    }

    public function destroy(int $id, Request $request): JsonResponse
    {
        $this->bookService->deleteBook($id, $request->user()->id);

        return response()->json(['message' => 'Book deleted successfully']);
    }

    public function restore(int $id, Request $request): JsonResponse
    {
        $this->bookService->restoreBook(
            id: $id,
            userId: $request->user()->id,
        );

        return response()->json(['message' => 'Book restored successfully']);
    }
}
