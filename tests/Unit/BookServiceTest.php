<?php

namespace Tests\Unit;

use App\DTO\Books\UpdateBookDTO;
use App\Exceptions\Books\BookNotFoundException;
use App\Exceptions\Users\UserNotFoundException;
use App\Models\Book;
use App\Models\User;
use App\Repositories\BookRepositoryInterface;
use App\Repositories\UserRepositoryInterface;
use App\Services\BookService;
use App\Services\LibraryAccessService;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Pagination\LengthAwarePaginator;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\MockObject\MockObject;
use Tests\TestCase;

class BookServiceTest extends TestCase
{
    private BookRepositoryInterface|MockObject $bookRepositoryMock;
    private UserRepositoryInterface|MockObject $userRepositoryMock;
    private LibraryAccessService|MockObject $libraryAccessServiceMock;

    public function setUp(): void
    {
        $this->bookRepositoryMock = $this->getMockBuilder(BookRepositoryInterface::class)->getMock();
        $this->userRepositoryMock = $this->getMockBuilder(UserRepositoryInterface::class)->getMock();
        $this->libraryAccessServiceMock = $this->getMockBuilder(LibraryAccessService::class)->disableOriginalConstructor()->getMock();

        parent::setUp();
    }

    public static function getUserBooksForViewerFailedProvider(): array
    {
        return [
            'Test 1. User not found, expect UserNotFoundException' => [
                'userRepoBehaviour' => function (MockObject $repository, self $testCase) {
                    $repository->expects($testCase->once())->method('exist')->willReturn(false);
                },
                'libraryAccessBehaviour' => function (MockObject $service, self $testCase) {
                    $service->expects($testCase->never())->method('hasAccess');
                },
                'bookRepoBehaviour' => function (MockObject $repository, self $testCase) {
                    $repository->expects($testCase->never())->method('getByUserId');
                },
                'expectedException' => UserNotFoundException::class,
            ],
            'Test 2. No access to user books, expect AuthorizationException' => [
                'userRepoBehaviour' => function (MockObject $repository, self $testCase) {
                    $repository->expects($testCase->once())->method('exist')->willReturn(true);
                },
                'libraryAccessBehaviour' => function (MockObject $service, self $testCase) {
                    $service->expects($testCase->once())->method('hasAccess')->willReturn(false);
                },
                'bookRepoBehaviour' => function (MockObject $repository, self $testCase) {
                    $repository->expects($testCase->never())->method('getByUserId');
                },
                'expectedException' => AuthorizationException::class,
            ],
        ];
    }

    #[DataProvider('getUserBooksForViewerFailedProvider')]
    public function testGetUserBooksForViewerFailed(
        callable $userRepoBehaviour,
        callable $libraryAccessBehaviour,
        callable $bookRepoBehaviour,
        string $expectedException,
    ) {
        $ownerId = 1;
        $viewerId = 2;

        $userRepoBehaviour($this->userRepositoryMock, $this);
        $libraryAccessBehaviour($this->libraryAccessServiceMock, $this);
        $bookRepoBehaviour($this->bookRepositoryMock, $this);

        $service = new BookService(
            userRepository: $this->userRepositoryMock,
            bookRepository: $this->bookRepositoryMock,
            libraryAccessService: $this->libraryAccessServiceMock,
        );

        $this->expectException($expectedException);

        $service->getUserBooksForViewer($ownerId, $viewerId);
    }

    public function testGetUserBooksForViewerSuccessful()
    {
        $ownerId = 1;
        $viewerId = 2;

        $mockPaginator = $this->getMockBuilder(LengthAwarePaginator::class)->disableOriginalConstructor()->getMock();

        $this->userRepositoryMock->expects($this->once())->method('exist')->with($ownerId)->willReturn(true);
        $this->libraryAccessServiceMock->expects($this->once())->method('hasAccess')->with($ownerId, $viewerId)->willReturn(true);
        $this->bookRepositoryMock->expects($this->once())->method('getByUserId')
            ->with($ownerId, 12)
            ->willReturn($mockPaginator);

        $service = new BookService(
            userRepository: $this->userRepositoryMock,
            bookRepository: $this->bookRepositoryMock,
            libraryAccessService: $this->libraryAccessServiceMock,
        );

        $result = $service->getUserBooksForViewer($ownerId, $viewerId);

        $this->assertSame($mockPaginator, $result);
    }

    public static function getBookFailedProvider(): array
    {
        return [
            'Book not found, expect BookNotFoundException' => [
                'bookRepoBehaviour' => function (MockObject $repository, self $testCase) {
                    $repository->expects($testCase->once())->method('findById')->willReturn(null);
                },
                'libraryAccessBehaviour' => function (MockObject $service, self $testCase) {
                    $service->expects($testCase->never())->method('hasAccess');
                },
                'expectedException' => BookNotFoundException::class,
            ],
            'No access to book, expect AuthorizationException' => [
                'bookRepoBehaviour' => function (MockObject $repository, self $testCase) {
                    $mockBook = new Book();
                    $mockBook->id = 1;
                    $mockBook->user_id = 3;
                    $repository->expects($testCase->once())->method('findById')->willReturn($mockBook);
                },
                'libraryAccessBehaviour' => function (MockObject $service, self $testCase) {
                    $service->expects($testCase->once())->method('hasAccess')->willReturn(false);
                },
                'expectedException' => AuthorizationException::class,
            ],
        ];
    }

    #[DataProvider('getBookFailedProvider')]
    public function testGetBookFailed(
        callable $bookRepoBehaviour,
        callable $libraryAccessBehaviour,
        string $expectedException,
    ) {
        $bookId = 1;
        $userId = 1;
        $bookRepoBehaviour($this->bookRepositoryMock, $this);
        $libraryAccessBehaviour($this->libraryAccessServiceMock, $this);

        $service = new BookService(
            userRepository: $this->userRepositoryMock,
            bookRepository: $this->bookRepositoryMock,
            libraryAccessService: $this->libraryAccessServiceMock,
        );

        $this->expectException($expectedException);

        $service->getBook($bookId, $userId);
    }

    public function testGetBookSuccessful()
    {
        $bookId = 1;
        $userId = 2;

        $mockBook = new Book();
        $mockBook->id = $bookId;
        $mockBook->user_id = $userId;

        $this->bookRepositoryMock->expects($this->once())->method('findById')->with($bookId)->willReturn($mockBook);
        $this->libraryAccessServiceMock->expects($this->once())->method('hasAccess')->with($userId, $userId)->willReturn(true);

        $service = new BookService(
            userRepository: $this->userRepositoryMock,
            bookRepository: $this->bookRepositoryMock,
            libraryAccessService: $this->libraryAccessServiceMock,
        );

        $result = $service->getBook($bookId, $userId);

        $this->assertSame($mockBook, $result);
    }

    public static function updateBookFailedProvider(): array
    {
        return [
            'Book not found, expect BookNotFoundException' => [
                'bookRepoBehaviour' => function (MockObject $repository, self $testCase) {
                    $repository->expects($testCase->once())->method('findByIdAndUser')->willReturn(null);
                },
                'expectedException' => BookNotFoundException::class,
            ],
        ];
    }

    #[DataProvider('updateBookFailedProvider')]
    public function testUpdateBookFailed(
        callable $bookRepoBehaviour,
        string $expectedException,
    ) {
        $bookId = 1;
        $userId = 2;

        $updateBookDTO = new UpdateBookDTO(
            id: $bookId,
            userId: $userId,
            title: 'Updated Title',
            content: 'Updated Content',
        );

        $bookRepoBehaviour($this->bookRepositoryMock, $this);

        $service = new BookService(
            userRepository: $this->userRepositoryMock,
            bookRepository: $this->bookRepositoryMock,
            libraryAccessService: $this->libraryAccessServiceMock,
        );

        $this->expectException($expectedException);

        $service->updateBook($updateBookDTO);
    }

    public function testUpdateBookSuccessful()
    {
        $bookId = 1;
        $userId = 2;

        $mockBook = new Book();
        $mockBook->id = $bookId;
        $mockBook->user_id = $userId;
        $mockBook->title = 'Old Title';
        $mockBook->content = 'Old Content';

        $updatedBook = new Book();
        $updatedBook->id = $bookId;
        $updatedBook->user_id = $userId;
        $updatedBook->title = 'Updated Title';
        $updatedBook->content = 'Updated Content';

        $updateBookDTO = new UpdateBookDTO(
            id: $bookId,
            userId: $userId,
            title: 'Updated Title',
            content: 'Updated Content',
        );

        $this->bookRepositoryMock->expects($this->once())->method('findByIdAndUser')->with($bookId, $userId)->willReturn($mockBook);
        $this->bookRepositoryMock->expects($this->once())->method('update')->with($mockBook, $updateBookDTO)->willReturn($updatedBook);

        $service = new BookService(
            userRepository: $this->userRepositoryMock,
            bookRepository: $this->bookRepositoryMock,
            libraryAccessService: $this->libraryAccessServiceMock,
        );

        $result = $service->updateBook($updateBookDTO);

        $this->assertSame($updatedBook, $result);
        $this->assertSame('Updated Title', $result->title);
        $this->assertSame('Updated Content', $result->content);
    }

    public static function deleteBookFailedProvider(): array
    {
        return [
            'Book not found, expect BookNotFoundException' => [
                'bookRepoBehaviour' => function (MockObject $repository, self $testCase) {
                    $repository->expects($testCase->once())->method('findByIdAndUser')->willReturn(null);
                },
                'libraryAccessBehaviour' => function (MockObject $service, self $testCase) {
                    $service->expects($testCase->never())->method('hasAccess');
                },
                'expectedException' => BookNotFoundException::class,
            ],
            'No access to delete book, expect AuthorizationException' => [
                'bookRepoBehaviour' => function (MockObject $repository, self $testCase) {
                    $mockBook = new Book();
                    $mockBook->id = 1;
                    $mockBook->user_id = 3;
                    $repository->expects($testCase->once())->method('findByIdAndUser')->willReturn($mockBook);
                },
                'libraryAccessBehaviour' => function (MockObject $service, self $testCase) {
                    $service->expects($testCase->never())->method('hasAccess');
                },
                'expectedException' => AuthorizationException::class,
            ],
        ];
    }

    #[DataProvider('deleteBookFailedProvider')]
    public function testDeleteBookFailed(
        callable $bookRepoBehaviour,
        callable $libraryAccessBehaviour,
        string $expectedException,
    ) {
        $bookId = 1;
        $userId = 2;

        $bookRepoBehaviour($this->bookRepositoryMock, $this);
        $libraryAccessBehaviour($this->libraryAccessServiceMock, $this);

        $service = new BookService(
            userRepository: $this->userRepositoryMock,
            bookRepository: $this->bookRepositoryMock,
            libraryAccessService: $this->libraryAccessServiceMock,
        );

        $this->expectException($expectedException);

        $service->deleteBook($bookId, $userId);
    }

    public function testDeleteBookSuccessful()
    {
        $bookId = 1;
        $userId = 2;

        $mockBook = new Book();
        $mockBook->id = $bookId;
        $mockBook->user_id = $userId;

        $this->bookRepositoryMock->expects($this->once())->method('findByIdAndUser')->with($bookId, $userId)->willReturn($mockBook);
        $this->bookRepositoryMock->expects($this->once())->method('delete')->with($mockBook)->willReturn(true);

        $service = new BookService(
            userRepository: $this->userRepositoryMock,
            bookRepository: $this->bookRepositoryMock,
            libraryAccessService: $this->libraryAccessServiceMock,
        );

        $result = $service->deleteBook($bookId, $userId);

        $this->assertTrue($result);
    }
}
