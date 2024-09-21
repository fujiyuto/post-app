<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\TweetService;
use App\Models\Restaurant;
use App\Models\Tweet;
use App\Http\Requests\TweetCreateRequest;
use App\Http\Requests\TweetEditRequest;
use App\Http\Requests\TweetDeleteRequest;
use Illuminate\Support\Facades\Auth;

class TweetController extends Controller
{
    private $tweetService;

    public function __construct(TweetService $tweetService)
    {
        $this->tweetService = $tweetService;
    }

    public function index(Request $request, Restaurant $restaurant)
    {
        try {

            $data = $this->tweetService->getTweets($restaurant, $request->input('keyword', ''), $request->input('perPage', 2));

            return $this->responseJson($data);

        } catch (\Exception $e) {
            throw $e;
        }
    }

    public function create(TweetCreateRequest $request)
    {
        try {

            $data = $this->tweetService->createTweet($request->restaurant_id, Auth::id(), $request->message);

            return $this->responseJson($data);

        } catch (\Exception $e) {
            throw $e;
        }
    }

    public function edit(TweetEditRequest $request, Tweet $tweet)
    {
        try {

            $data = $this->tweetService->updateTweet($tweet, $request->message);

            return $this->responseJson($data);

        } catch (\Exception $e) {
            throw $e;
        }
    }

    public function delete(TweetDeleteRequest $request, Tweet $tweet)
    {
        try {

            $data = $this->tweetService->deleteTweet($tweet);

            return $this->responseJson($data);

        } catch (\Exception $e) {
            throw $e;
        }
    }


}
