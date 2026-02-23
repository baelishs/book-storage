<?php

namespace App\Http\Controllers;

use App\Http\Requests\GrantLibraryAccessRequest;
use App\Services\LibraryAccessService;
use Illuminate\Http\JsonResponse;

class LibraryAccessController extends Controller
{
    public function __construct(
        private readonly LibraryAccessService $libraryAccessService,
    ) {
    }

    public function store(GrantLibraryAccessRequest $request): JsonResponse
    {
        $this->libraryAccessService->grantAccess(
            ownerId: $request->user()->id,
            viewerId: $request->input('viewer_id')
        );

        return response()->json(['message' => 'Access granted'], 201);
    }
}
