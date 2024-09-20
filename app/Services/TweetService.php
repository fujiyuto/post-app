<?php

namespace App\Services;

use App\Models\Restaurant;
use App\Models\Tweet;
use App\Exceptions\DataOperationException;
use Illuminate\Support\Facades\Gate;

class TweetService {

    public function createTweet(int $restaurant_id, int $user_id, string $message)
    {
        $insert_data = [
            'restaurant_id' => $restaurant_id,
            'user_id'       => $user_id,
            'message'       => $message
        ];

        if ( !Tweet::create($insert_data) ) {
            throw new DataOperationException('ERROR: Exception occur in '.__LINE__.' lines of '.basename(__CLASS__));
        }

        return [
            'data' => [
                'ok' => true
            ]
        ];
    }

    public function updateTweet(Tweet $tweet, string $message)
    {
        // TODO
        // ユーザーチェック

        $tweet->message = $message;
        if ( !$tweet->save() ) {
            throw new DataOperationException('ERROR: Exception occur in '.__LINE__.' lines of '.basename(__CLASS__));
        }

        return [
            'data' => [
                'ok' => true
            ]
        ];
    }

    public function deleteTweet(Tweet $tweet)
    {
        
    }
}
