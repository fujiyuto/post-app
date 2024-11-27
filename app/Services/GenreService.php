<?php

namespace App\Services;

use App\Models\Genre;
use App\Models\GenreGroup;
use App\Exceptions\DataNotFoundException;

class GenreService
{
    public function getGenres(): array
    {
        $genre_data = Genre::selectRaw('genre_groups.unique_name as group_unique_name, genre_groups.group_name, genres.unique_name as genre_unique_name, genre_name')
                            ->join('genre_groups', 'genre_groups.id', '=', 'genres.genre_group_id')
                            ->orderBy('genre_groups.unique_name', 'asc')
                            ->orderBy('genres.unique_name', 'asc')
                            ->get();

        if (!$genre_data) {
            throw new DataNotFoundException('ERROR: Exception occur in '.__LINE__.' lines of '.basename(__CLASS__));
        }

        $output_data = [];
        foreach ($genre_data as $data) {
            if (!array_key_exists($data->group_unique_name, $output_data)) {
                $output_data[$data->group_unique_name] = [
                    'group_name' => $data->group_name,
                    'genres' => []
                ];
            }
            $output_data[$data->group_unique_name]['genres'][] = [
                'unique_name' => $data->genre_unique_name,
                'genre_name'  => $data->genre_name
            ];
        }
        return [
            'genre_groups' => $output_data
        ];
    }
}
