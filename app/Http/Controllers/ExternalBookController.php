<?php

namespace App\Http\Controllers;

use App\Exceptions\Books\ExternalBookServiceException;
use App\Http\Requests\ImportBookRequest;
use App\Http\Requests\SearchBookRequest;
use App\Http\Resources\Books\BooksResource;
use App\Http\Resources\External\SearchExternalResource;
use App\Services\BookSearchService;
use Exception;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Symfony\Component\HttpFoundation\JsonResponse;

class ExternalBookController extends Controller
{
    public function __construct(
        private readonly BookSearchService $bookSearchService,
    ) {
    }

    /**
     * @throws Exception
     */
    public function search(SearchBookRequest $request): ResourceCollection
    {
        $result = $this->bookSearchService->searchAll(
            query: $request->getSearchQuery()
        );

        return SearchExternalResource::collection($result);
    }

    /**
     * @throws ExternalBookServiceException
     */
    public function import(ImportBookRequest $request): JsonResponse
    {
        $book = $this->bookSearchService->importBook(
            dto: $request->toDto()
        );

        return (new BooksResource($book))->response()->setStatusCode(201);
    }
}
