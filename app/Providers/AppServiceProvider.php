<?php

namespace App\Providers;

use App\Mappers\External\GoogleBooksMapper;
use App\Mappers\External\ManIvanovFerberMapper;
use App\Providers\Books\BookSearchStrategyResolver;
use App\Providers\Books\GoogleBooksProvider;
use App\Providers\Books\MannIvanovFerberProvider;
use App\Repositories\AccessTokenRepository;
use App\Repositories\AccessTokenRepositoryInterface;
use App\Repositories\BookRepository;
use App\Repositories\BookRepositoryInterface;
use App\Repositories\ExternalBookRepository;
use App\Repositories\ExternalBookRepositoryInterface;
use App\Repositories\LibraryAccessRepository;
use App\Repositories\LibraryAccessRepositoryInterface;
use App\Repositories\UserRepository;
use App\Repositories\UserRepositoryInterface;
use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(ClientInterface::class, Client::class);

        $this->app->bind(
            UserRepositoryInterface::class,
            UserRepository::class
        );
        $this->app->bind(
            AccessTokenRepositoryInterface::class,
            AccessTokenRepository::class
        );
        $this->app->bind(
            BookRepositoryInterface::class,
            BookRepository::class
        );
        $this->app->bind(
            LibraryAccessRepositoryInterface::class,
            LibraryAccessRepository::class
        );
        $this->app->bind(
            ExternalBookRepositoryInterface::class,
            ExternalBookRepository::class
        );

        $this->app->bind('google_books_provider', function ($app) {
            return new GoogleBooksProvider(
                $app->make(ClientInterface::class),
                $app->make(GoogleBooksMapper::class),
            );
        });

        $this->app->bind('man_ivanov_ferber_provider', function ($app) {
            return new MannIvanovFerberProvider(
                $app->make(ClientInterface::class),
                $app->make(ManIvanovFerberMapper::class),
            );
        });

        $this->app->singleton(BookSearchStrategyResolver::class, function ($app) {
            return new BookSearchStrategyResolver([
                $app->make('man_ivanov_ferber_provider'),
                $app->make('google_books_provider'),
            ]);
        });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
