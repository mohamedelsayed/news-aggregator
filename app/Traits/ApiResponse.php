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
}
