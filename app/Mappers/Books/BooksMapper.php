<?php

namespace App\Mappers\Books;

use App\DTO\Books\BooksListDTO;
use App\DTO\Books\BooksListItemDTO;
use App\DTO\PaginationDTO;
use App\Models\Book;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class BooksMapper
{
    /**
     * @param LengthAwarePaginator<Book> $list
     * @return BooksListDTO
     */
    public function booksListToDTO(LengthAwarePaginator $list): BooksListDTO
    {
        $pagination = PaginationDTO::fromLengthAwarePaginator($list);
        $booksDTO = array_map(function (Book $book) {
            return new BooksListItemDTO(
                id: $book->id,
                title: $book->title,
            );
        }, $list->items());

        return new BooksListDTO(
            data: $booksDTO,
            meta: $pagination,
        );
    }
}
