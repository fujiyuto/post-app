<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AuthenticateException extends Exception
{
    public function report(): void
    {
        Log::debug($this->getMessage());
    }

    public function render(Request $request): JsonResponse
    {
        return response()->json([
            'data' => [
                'error' => 'ログインに失敗しました'
            ]
        ], 401);
    }
}
