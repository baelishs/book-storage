<?php

namespace Tests\Unit;

use App\Repositories\LibraryAccessRepositoryInterface;
use App\Services\LibraryAccessService;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Validation\ValidationException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\MockObject\MockObject;
use Tests\TestCase;

class LibraryAccessServiceTest extends TestCase
{
    private LibraryAccessRepositoryInterface|MockObject $libraryAccessRepositoryMock;

    public function setUp(): void
    {
        $this->libraryAccessRepositoryMock = $this->getMockBuilder(LibraryAccessRepositoryInterface::class)->getMock();

        parent::setUp();
    }

    public static function grantAccessFailedProvider(): array
    {
        return [
            'Grant access to yourself throws AuthorizationException' => [
                'ownerId' => 1,
                'viewerId' => 1,
                'repositoryBehaviour' => function (MockObject $repository, self $testCase) {
                    $repository->expects($testCase->never())->method('exists');
                    $repository->expects($testCase->never())->method('create');
                },
                'expectedException' => ValidationException::class,
            ],
        ];
    }

    #[DataProvider('grantAccessFailedProvider')]
    public function testGrantAccessFailed(
        int $ownerId,
        int $viewerId,
        callable $repositoryBehaviour,
        string $expectedException,
    ) {
        $repositoryBehaviour($this->libraryAccessRepositoryMock, $this);

        $service = new LibraryAccessService(
            libraryAccessRepository: $this->libraryAccessRepositoryMock,
        );

        $this->expectException($expectedException);

        $service->grantAccess($ownerId, $viewerId);
    }

    public function testGrantAccessAlreadyExists()
    {
        $ownerId = 1;
        $viewerId = 2;

        $this->libraryAccessRepositoryMock->expects($this->once())->method('exists')
            ->with($ownerId, $viewerId)
            ->willReturn(true);
        $this->libraryAccessRepositoryMock->expects($this->never())->method('create');

        $service = new LibraryAccessService(
            libraryAccessRepository: $this->libraryAccessRepositoryMock,
        );

        $service->grantAccess($ownerId, $viewerId);

        $this->assertTrue(true);
    }

    public function testGrantAccessSuccessful()
    {
        $ownerId = 1;
        $viewerId = 2;

        $this->libraryAccessRepositoryMock->expects($this->once())->method('exists')
            ->with($ownerId, $viewerId)
            ->willReturn(false);
        $this->libraryAccessRepositoryMock->expects($this->once())->method('create')
            ->with($ownerId, $viewerId);

        $service = new LibraryAccessService(
            libraryAccessRepository: $this->libraryAccessRepositoryMock,
        );

        $service->grantAccess($ownerId, $viewerId);

        $this->assertTrue(true);
    }

    public function testHasAccessWhenOwnerEqualsViewer()
    {
        $bookOwnerId = 1;
        $viewerId = 1;

        $this->libraryAccessRepositoryMock->expects($this->never())->method('getAccessibleOwnerIds');

        $service = new LibraryAccessService(
            libraryAccessRepository: $this->libraryAccessRepositoryMock,
        );

        $result = $service->hasAccess($bookOwnerId, $viewerId);

        $this->assertTrue($result);
    }

    public function testHasAccessWhenViewerHasAccess()
    {
        $bookOwnerId = 1;
        $viewerId = 2;
        $accessibleOwnerIds = [1, 3, 5];

        $this->libraryAccessRepositoryMock->expects($this->once())->method('getAccessibleOwnerIds')
            ->with($viewerId)
            ->willReturn($accessibleOwnerIds);

        $service = new LibraryAccessService(
            libraryAccessRepository: $this->libraryAccessRepositoryMock,
        );

        $result = $service->hasAccess($bookOwnerId, $viewerId);

        $this->assertTrue($result);
    }

    public function testHasAccessWhenViewerDoesNotHaveAccess()
    {
        $bookOwnerId = 1;
        $viewerId = 2;
        $accessibleOwnerIds = [3, 5, 7];

        $this->libraryAccessRepositoryMock->expects($this->once())->method('getAccessibleOwnerIds')
            ->with($viewerId)
            ->willReturn($accessibleOwnerIds);

        $service = new LibraryAccessService(
            libraryAccessRepository: $this->libraryAccessRepositoryMock,
        );

        $result = $service->hasAccess($bookOwnerId, $viewerId);

        $this->assertFalse($result);
    }
}
