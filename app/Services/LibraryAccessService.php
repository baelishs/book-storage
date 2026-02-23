<?php

namespace App\Services;

use App\Repositories\LibraryAccessRepositoryInterface;
use Illuminate\Auth\Access\AuthorizationException;

class LibraryAccessService
{
    public function __construct(
        private readonly LibraryAccessRepositoryInterface $libraryAccessRepository,
    ) {
    }

    /**
     * @throws AuthorizationException
     */
    public function grantAccess(int $ownerId, int $viewerId): void
    {
        if ($ownerId === $viewerId) {
            throw new AuthorizationException('Cannot grant access to yourself');
        }

        if ($this->libraryAccessRepository->exists($ownerId, $viewerId)) {
            return;
        }

        $this->libraryAccessRepository->create($ownerId, $viewerId);
    }

    public function getAccessibleOwnerIds(int $viewerId): array
    {
        return $this->libraryAccessRepository->getAccessibleOwnerIds($viewerId);
    }

    public function hasAccess(int $bookOwnerId, int $viewerId): bool
    {
        if ($bookOwnerId === $viewerId) {
            return true;
        }

        $accessibleOwnerIds = $this->getAccessibleOwnerIds($viewerId);

        return in_array($bookOwnerId, $accessibleOwnerIds);
    }
}
