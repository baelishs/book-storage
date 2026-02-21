<?php

namespace App\Services;

use App\DTO\Books\BooksListDTO;
use App\DTO\Books\CreateBookDTO;
use App\DTO\Books\UpdateBookDTO;
use App\Exceptions\Books\BookNotFoundException;
use App\Mappers\Books\BooksMapper;
use App\Models\Book;
use App\Repositories\BookRepositoryInterface;

class BookService
{
    private const PAGINATION_PER_PAGE = 12;

    public function __construct(
        protected BookRepositoryInterface $bookRepository,
        protected BooksMapper $booksMapper,
    ) {
    }

    public function getUserBooks(int $userId): BooksListDTO
    {
        return $this->booksMapper->booksListToDTO(
            list: $this->bookRepository->getUserBooks($userId, self::PAGINATION_PER_PAGE),
        );
    }

    public function createBook(CreateBookDTO $dto): Book
    {
        return $this->bookRepository->create($dto);
    }

    /**
     * @throws BookNotFoundException
     */
    public function getBook(int $id, int $userId): Book
    {
        if (!$book = $this->bookRepository->findByIdAndUser($id, $userId)) {
            throw new BookNotFoundException($id);
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
     * @throws BookNotFoundException
     */
    public function deleteBook(int $id, int $userId): bool
    {
        if (!$book = $this->bookRepository->findByIdAndUser($id, $userId)) {
            throw new BookNotFoundException($id);
        }

        return $this->bookRepository->delete($book);
    }

    /**
     * @throws BookNotFoundException
     */
    public function restoreBook(int $id): bool
    {
        if (!$book = $this->bookRepository->findDestroyed($id)) {
            throw new BookNotFoundException($id);
        }

        return $this->bookRepository->restore($book);
    }
}
