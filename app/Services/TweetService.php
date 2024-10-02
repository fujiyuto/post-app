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
    public function getTweets(Restaurant $restaurant, string|null $keyword, int $per_page)
    {

        if ( $keyword ) {
            $tweets = Tweet::search($keyword)
                            ->where('restaurant_id', $restaurant->id)
                            ->paginate($per_page);
        } else {
            $tweets = Tweet::selectRaw('tweets.id as id, users.user_name, tweets.message')
                        ->join('users', 'users.id', '=', 'tweets.user_id')
                        ->where('restaurant_id', $restaurant->id)
                        ->orderBy('tweets.id', 'desc')
                        ->paginate($per_page);
        }

        $response_data = [
            'current_page' => $tweets->currentPage(),
            'per_page'     => $tweets->perPage(),
            'last_page'    => $tweets->lastPage(),
            'tweets'       => $tweets->items()
        ];

        return [
            'data' => $response_data
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
