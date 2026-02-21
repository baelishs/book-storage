<?php

namespace App\DTO;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class PaginationDTO
{
    public function __construct(
        public int $current_page,
        public int $last_page,
        public int $per_page,
        public int $total,
    )
    {}

    public static function fromLengthAwarePaginator(LengthAwarePaginator $paginator): self
    {
        return new self(
            current_page: $paginator->currentPage(),
            last_page: $paginator->lastPage(),
            per_page: $paginator->perPage(),
            total: $paginator->total(),
        );
    }
}
