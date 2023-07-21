<?php

namespace App\Exceptions\JsonApi;

use Exception;

class NotFoundHttpException extends Exception
{
    public function render($request)
    {
        return response()->json([
            'errors' => [[
                'title' => 'Not Found',
                'detail' => $this->getDetail($request),
                'status' => '404',
            ]],
        ], 404);
    }

    protected function getDetail($request): string
    {
        if (str($this->getMessage())->startsWith('No query results for model')) {
            return "No records found with the id '{$request->getResourceId()}'".
                " in the '{$request->getResourceType()}' resource.";
        }

        return $this->getMessage();
    }
}
