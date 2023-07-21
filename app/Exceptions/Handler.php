<?php

namespace App\Exceptions;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Validation\ValidationException;
use App\Http\Responses\JsonApiValidationErrorResponse;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class Handler extends ExceptionHandler
{
    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
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
        $this->renderable(function (NotFoundHttpException $e, Request $request) {
            $request->isJsonApi() && throw new JsonApi\NotFoundHttpException($e->getMessage());
        });

        $this->renderable(function (BadRequestHttpException $e, Request $request) {
            $request->isJsonApi() && throw new JsonApi\BadRequestHttpException($e->getMessage());
        });

        $this->renderable(function (AuthenticationException $e, Request $request) {
            $request->isJsonApi() && throw new JsonApi\AuthenticationException();
        });
    }

    protected function invalidJson($request, ValidationException $exception): JsonResponse
    {
        return $request->isJsonApi()
            ? new JsonApiValidationErrorResponse($exception)
            : parent::invalidJson($request, $exception);
    }
}
