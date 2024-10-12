<?php

namespace App\Services;

use App\Models\Genre;
use App\Exceptions\DataNotFoundException;

class GenreService {

    public function getGenres(): array
    {
        $genres = Genre::selectRaw('unique_name, genre_name')->orderBy('genre_name', 'asc')->get();

        if ( !$genres ) {
            throw new DataNotFoundException('ERROR: Exception occur in '.__LINE__.' lines of '.basename(__CLASS__));
        }

        return $genres->toArray();
    }
}
