<?php

namespace App\Services;

use App\Repositories\LibraryAccessRepositoryInterface;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Validation\ValidationException;

class LibraryAccessService
{
    public function __construct(
        private readonly LibraryAccessRepositoryInterface $libraryAccessRepository,
    ) {
    }

    /**
     * @throws ValidationException
     */
    public function grantAccess(int $ownerId, int $viewerId): void
    {
        if ($ownerId === $viewerId) {
            throw ValidationException::withMessages([
                'viewer_id' => 'You cannot grant access to yourself.']);
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
