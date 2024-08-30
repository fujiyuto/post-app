<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class DataOperationException extends Exception
{
    public function report(): void
    {
        Log::debug($this->getMessage());
    }

    public function render(Request $request): JsonResponse
    {
        return response()->json([
            'data' => [
                'error' => 'エラーが発生しました。'
            ]
        ], 500);
    }
}
