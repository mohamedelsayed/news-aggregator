<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

trait ApiResponse
{
    public function sendResponse($data = null, ?string $message = null, int $status = Response::HTTP_OK, $errors = null): JsonResponse
    {
        $result['data'] = null;
        if ($data) {
            $result['data'] = $data;
        }
        if ($message) {
            $result['message'] = __($message);
        }
        if ($errors) {
            $result['errors'] = $errors;
        }

        return response()->json($result, $status)
            ->header('X-Powered-By', 'RUBY') // this is to hide my real info of the project from header
            ->header('Server', 'IIS');
    }

    public function sendResponseWithPagination($data, $message = null, int $status = Response::HTTP_OK, $errors = null): JsonResponse
    {
        $result = [
            'data' => $data,
            'pagination' => [
                'total' => $data->total(),
                'per_page' => $data->perPage(),
                'current_page' => $data->currentPage(),
                'last_page' => $data->lastPage(),
                'from' => $data->firstItem(),
                'to' => $data->lastItem(),
            ],
            'links' => [
                'next' => $data->nextPageUrl(),
                'prev' => $data->previousPageUrl(),
            ],
        ];

        if ($message) {
            $result['message'] = __($message);
        }
        if ($errors) {
            $result['errors'] = $errors;
        }

        return response()->json($result, $status)
            ->header('X-Powered-By', 'RUBY') // this is to hide my real info of the project from header
            ->header('Server', 'IIS');
    }
}
