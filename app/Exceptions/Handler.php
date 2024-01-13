<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
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
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @throws \Throwable
     */
    public function render($request, Throwable $e)
    {
        if ($request->expectsJson()) {
            if ($e instanceof ModelNotFoundException) {
                return json_message('Resource not found', 404);
            }

            if ($e instanceof NotFoundHttpException) {
                return json_message($e->getMessage(), 404);
            }

            if ($e instanceof AuthorizationException) {
                return json_message('Unauthorized', 403);
            }

            if ($e instanceof ValidationException) {
                return json('The given data was invalid', $e->errors(), 422);
            }

            // unrecognized exception
            $message = app()->isProduction() ? "Internal server error" : $e->getMessage();
            $data = app()->isProduction() ? [] : [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTrace(),
            ];

            return json($message, $data, 400);
        }

        return parent::render($request, $e);
    }
}
