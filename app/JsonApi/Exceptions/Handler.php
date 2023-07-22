<?php

namespace App\JsonApi\Exceptions;

use App\JsonApi\Exceptions;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Validation\ValidationException;
use App\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\HttpException;
use App\JsonApi\Http\Responses\JsonApiValidationErrorResponse;

class Handler extends ExceptionHandler
{
    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->renderable(function (HttpException $e, Request $request) {
            $request->isJsonApi() && throw new Exceptions\HttpException($e);
        })->renderable(function (AuthenticationException $e, Request $request) {
            $request->isJsonApi() && throw new Exceptions\AuthenticationException();
        });

        parent::register();
    }

    protected function invalidJson($request, ValidationException $exception): JsonResponse
    {
        return $request->isJsonApi()
            ? new JsonApiValidationErrorResponse($exception)
            : parent::invalidJson($request, $exception);
    }
}
