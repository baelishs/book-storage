<?php

namespace App\Exceptions;

use App\Exceptions\Books\BookNotFoundException;
use App\Exceptions\Books\ExternalBookServiceException;
use App\Exceptions\Users\UserNotFoundException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register()
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    public function render($request, Throwable $e): JsonResponse|Response
    {
        return match (true) {

            $e instanceof BookNotFoundException || $e instanceof UserNotFoundException =>
            $this->errorResponse($e->getMessage(), 404),

            $e instanceof AuthenticationException =>
            $this->errorResponse('Unauthenticated', 401),

            $e instanceof AuthorizationException =>
            $this->errorResponse($e->getMessage(), 403),

            $e instanceof ExternalBookServiceException =>
            $this->errorResponse($e->getMessage(), 503),

            $e instanceof ValidationException =>
            $this->validationErrorResponse($e),

            default =>
            parent::render($request, $e)
        };
    }

    private function errorResponse(string $message, int $status): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $message], $status);
    }

    private function validationErrorResponse(ValidationException $e): JsonResponse
    {
        return response()->json([
            'success' => false,
            'errors' => $e->errors()], 422);
    }
}
