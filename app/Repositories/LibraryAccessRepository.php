<?php

namespace App\Repositories;

use App\Models\LibraryAccess;

class LibraryAccessRepository implements LibraryAccessRepositoryInterface
{
    public function exists(int $ownerId, int $viewerId): bool
    {
        return LibraryAccess::query()
            ->where('owner_id', $ownerId)
            ->where('viewer_id', $viewerId)
            ->exists();
    }

    public function create(int $ownerId, int $viewerId): void
    {
        LibraryAccess::create([
            'owner_id' => $ownerId,
            'viewer_id' => $viewerId,
        ]);
    }

    public function getAccessibleOwnerIds(int $viewerId): array
    {
        return LibraryAccess::query()
            ->where('viewer_id', $viewerId)
            ->pluck('owner_id')
            ->toArray();
    }
}
