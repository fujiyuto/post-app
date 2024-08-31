<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;

abstract class Controller
{
    protected function responseJson(array $data, $status = 200): JsonResponse
    {
        return response()->json($data, $status);
    }
}
