<?php

namespace App\Repositories;

use App\DTO\Books\CreateBookDTO;
use App\DTO\Books\UpdateBookDTO;
use App\Models\Book;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface BookRepositoryInterface
{
    /**
     * @param int $userId
     * @param int $perPage
     * @return LengthAwarePaginator<Book>
     */
    public function getUserBooks(int $userId, int $perPage = 12): LengthAwarePaginator;

    public function create(CreateBookDTO $data): Book;

    public function findById(int $id): ?Book;

    public function findByIdAndUser(int $id, int $userId): ?Book;
    public function findDestroyed(int $bookId): ?Book;

    public function update(Book $model, UpdateBookDTO $updateData): Book;

    public function delete(Book $model): bool;

    public function restore(Book $book): bool;
}
