<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class UnauthorizationException extends Exception
{
    public function report(): void
    {
        Log::debug($this->getMessage());
    }

    public function render(Request $request): JsonResponse
    {
        return response()->json([
            'data' => [
                'error' => '許可されていない操作です'
            ]
        ], 403);
    }
}
