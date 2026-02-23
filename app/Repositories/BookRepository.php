<?php

namespace App\Repositories;

use App\DTO\Books\CreateBookDTO;
use App\DTO\Books\UpdateBookDTO;
use App\Models\Book;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class BookRepository implements BookRepositoryInterface
{
    public function getOwnBooks(int $userId, int $perPage = 12): LengthAwarePaginator
    {
        return Book::query()
            ->where('user_id', $userId)
            ->select('id', 'title', 'user_id')
            ->orderBy('id', 'desc')
            ->paginate($perPage);
    }

    public function getByUserId(int $userId, int $perPage): LengthAwarePaginator
    {
        return Book::query()
            ->where('user_id', $userId)
            ->select('id', 'title', 'user_id')
            ->orderBy('id', 'desc')
            ->paginate($perPage);
    }

    public function create(CreateBookDTO $data): Book
    {
        return Book::create($data->toArray());
    }

    public function findById(int $id): ?Book
    {
        return Book::query()->find($id);
    }

    public function findByIdAndUser(int $id, int $userId): ?Book
    {
        return Book::where('id', $id)
            ->where('user_id', $userId)
            ->first();
    }

    public function findDestroyed(int $bookId): ?Book
    {
        return Book::onlyTrashed()->find($bookId);
    }

    public function update(Book $model, UpdateBookDTO $updateData): Book
    {
        $model->update($updateData->toArray());
        return $model;
    }

    public function delete(Book $model): bool
    {
        return $model->delete();
    }

    public function restore(Book $book): bool
    {
        return $book->restore();
    }
}
