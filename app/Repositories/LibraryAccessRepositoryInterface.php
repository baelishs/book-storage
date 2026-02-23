<?php

namespace App\Repositories;

interface LibraryAccessRepositoryInterface
{
    public function exists(int $ownerId, int $viewerId): bool;

    public function create(int $ownerId, int $viewerId): void;

    public function getAccessibleOwnerIds(int $viewerId): array;
}
