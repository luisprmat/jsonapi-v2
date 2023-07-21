<?php

namespace App\Exceptions\JsonApi;

use Exception;

class NotFoundHttpException extends Exception
{
    public function render($request)
    {
        $detail = $this->getMessage();

        if (str($this->getMessage())->startsWith('No query results for model')) {
            $type = $request->filled('data.type')
             ? $request->input('data.type')
             : (string) str($request->path())->after('api/v1/')->before('/');

            $id = $request->filled('data.id')
                ? $request->input('data.id')
                : (string) str($request->path())->after($type)->replace('/', '');

            if ($id && $type) {
                $detail = "No records found with the id '{$id}' in the '{$type}' resource.";
            }
        }

        return response()->json([
            'errors' => [[
                'title' => 'Not Found',
                'detail' => $detail,
                'status' => '404',
            ]],
        ], 404);
    }
}
