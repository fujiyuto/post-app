<?php

namespace App\Http\Controllers;

use App\Services\GenreService;
use Illuminate\Http\Request;

class GenreController extends Controller
{
    private $genreService;

    public function __construct(GenreService $genreService)
    {
        $this->genreService = $genreService;
    }

    public function index()
    {
        try {

            $data = $this->genreService->getGenres();

            return $this->responseJson($data);

        } catch (\Exception $e) {
            throw $e;
        }
    }
}
