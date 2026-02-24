<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\ExternalBookController;
use App\Http\Controllers\LibraryAccessController;
use App\Http\Controllers\UserBooksController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::controller(AuthController::class)->group(function () {
    Route::post('/login', 'login');
    Route::post('/register', 'register');
});


Route::middleware('auth:sanctum')->group(function () {

    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', fn (Request $request) => $request->user());

    Route::get('/users', [UserController::class, 'index']);

    Route::get('users/{user}/books', [UserBooksController::class, 'getUserBooks']);

    Route::apiResource('books', BookController::class);
    Route::post('books/{book}/restore', [BookController::class, 'restore']);

    Route::post('library-access', [LibraryAccessController::class, 'store']);

    Route::prefix('external')->controller(ExternalBookController::class)->group(function () {
        Route::get('/search', 'search');
        Route::post('/import', 'import');
    });
});
