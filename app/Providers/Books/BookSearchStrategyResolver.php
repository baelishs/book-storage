<?php

namespace App\Providers\Books;

class BookSearchStrategyResolver
{
    /**
     * @param BookProviderInterface[] $providers
     */
    public function __construct(
        private readonly array $providers,
    ) {
    }

    /**
     * @return BookProviderInterface[]
     */
    public function getProviders(): iterable
    {
        return $this->providers;
    }
}
