<?php

namespace App\Services;

use App\DTO\Books\CreateBookDTO;
use App\DTO\Books\UpdateBookDTO;
use App\Exceptions\Books\BookNotFoundException;
use App\Exceptions\Users\UserNotFoundException;
use App\Models\Book;
use App\Repositories\BookRepositoryInterface;
use App\Repositories\UserRepositoryInterface;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Pagination\LengthAwarePaginator;

class BookService
{
    private const PAGINATION_PER_PAGE = 12;

    public function __construct(
        protected UserRepositoryInterface $userRepository,
        protected BookRepositoryInterface $bookRepository,
        protected LibraryAccessService $libraryAccessService,
    ) {
    }

    /**
     * @param int $userId
     * @return LengthAwarePaginator<Book>
     */
    public function getUserBooks(int $userId): LengthAwarePaginator
    {
        return $this->bookRepository->getOwnBooks(
            userId: $userId,
            perPage: self::PAGINATION_PER_PAGE
        );
    }

    /**
     * @param int $ownerId
     * @param int $viewerId
     * @return LengthAwarePaginator<Book>
     * @throws AuthorizationException
     * @throws UserNotFoundException
     */
    public function getUserBooksForViewer(int $ownerId, int $viewerId): LengthAwarePaginator
    {
        if (!$this->userRepository->exist($ownerId)) {
            throw new UserNotFoundException($ownerId);
        }

        if (!$this->libraryAccessService->hasAccess($ownerId, $viewerId)) {
            throw new AuthorizationException("You do not have access to this user's books");
        }

        return $this->bookRepository->getByUserId(
            userId: $ownerId,
            perPage: self::PAGINATION_PER_PAGE
        );
    }

    public function createBook(CreateBookDTO $dto): Book
    {
        return $this->bookRepository->create($dto);
    }

    /**
     * @throws BookNotFoundException
     * @throws AuthorizationException
     */
    public function getBook(int $id, int $userId): Book
    {
        if (!$book = $this->bookRepository->findById($id)) {
            throw new BookNotFoundException($id);
        }

        if (!$this->libraryAccessService->hasAccess($book->user_id, $userId)) {
            throw new AuthorizationException('You do not have access to this book');
        }

        return $book;
    }

    /**
     * @throws BookNotFoundException
     */
    public function updateBook(UpdateBookDTO $dto): Book
    {
        if (!$book = $this->bookRepository->findByIdAndUser($dto->id, $dto->userId)) {
            throw new BookNotFoundException($dto->id);
        }

        return $this->bookRepository->update($book, $dto);
    }

    /**
     * @throws BookNotFoundException|AuthorizationException
     */
    public function deleteBook(int $id, int $userId): bool
    {
        if (!$book = $this->bookRepository->findByIdAndUser($id, $userId)) {
            throw new BookNotFoundException($id);
        }

        if ($book->user_id !== $userId) {
            throw new AuthorizationException("You do not have access to delete this book");
        }

        return $this->bookRepository->delete($book);
    }

    /**
     * @throws BookNotFoundException
     * @throws AuthorizationException
     */
    public function restoreBook(int $id, int $userId): bool
    {
        if (!$book = $this->bookRepository->findDestroyed($id)) {
            throw new BookNotFoundException($id);
        }

        if ($book->user_id !== $userId) {
            throw new AuthorizationException("You do not have access to restore this book");
        }

        return $this->bookRepository->restore($book);
    }
}
