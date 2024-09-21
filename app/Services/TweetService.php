<?php

namespace App\Services;

use App\Models\Restaurant;
use App\Models\Tweet;
use App\Exceptions\DataOperationException;
use Illuminate\Support\Facades\Gate;
use App\Policies\TweetPolicy;
use App\Exceptions\UnauthorizationException;

class TweetService
{
    public function getTweets(Restaurant $restaurant, string $keyword, int $per_page)
    {
        $tweets_query = Tweet::selectRaw('tweets.id as id, users.user_name, tweets.message')
                        ->join('users', 'users.id', '=', 'tweets.user_id')
                        ->where('restaurant_id', $restaurant->id);
        if ( $keyword ) {
            $tweets_query->where('tweets.message', 'LIKE', "%{$keyword}%")
                         ->orWhere('users.user_name', 'LIKE', "%{$keyword}%");
        }
        $tweets = $tweets_query->orderBy('tweets.id', 'desc')
                               ->simplePaginate($per_page);
        return [
            'data' => [
                'tweets' => $tweets
            ]
        ];
    }

    public function createTweet(int $restaurant_id, int $user_id, string $message)
    {
        $insert_data = [
            'restaurant_id' => $restaurant_id,
            'user_id'       => $user_id,
            'message'       => $message
        ];

        if (!Tweet::create($insert_data)) {
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
        // ユーザーチェック
        $check = Gate::inspect('update', $tweet);
        if ( $check->denied() ) {
            throw new UnauthorizationException('ERROR: Exception occur in '.__LINE__.' lines of '.basename(__CLASS__));
        }
        $tweet->message = $message;
        if (!$tweet->save()) {
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
        // ユーザーチェック
        $check = Gate::inspect('delete', $tweet);
        if ( $check->denied() ) {
            throw new UnauthorizationException('ERROR: Exception occur in '.__LINE__.' lines of '.basename(__CLASS__));
        }

        // ツイート削除
        if ( !$tweet->delete() ) {
            throw new DataOperationException('ERROR: Exception occur in '.__LINE__.' lines of '.basename(__CLASS__));
        }

        return [
            'data' => [
                'ok' => true
            ]
        ];
    }

}
