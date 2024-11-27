<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class FormRequestException extends Exception
{
    private $errors;

    public function __construct(array $errors)
    {
        $this->errors = $errors;
    }

    public function report(): void
    {
        Log::debug($this->errors);
    }

    public function render(Request $request): JsonResponse
    {
        return response()->json([
            'error' => 'データが正しくありません'
        ], 400);
    }
}
