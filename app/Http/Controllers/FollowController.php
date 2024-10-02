<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\FollowService;
use Illuminate\Http\Request;
use App\Http\Requests\FollowCreateRequest;
use App\Http\Requests\FollowDeleteRequest;
use Illuminate\Support\Facades\Auth;

class FollowController extends Controller
{
    private $followService;

    public function __construct(FollowService $followService)
    {
        $this->followService = $followService;
    }

    public function index_follow(Request $request, User $user)
    {
        try {

            $data = $this->followService->getFollowUsers($user, $request->keyword);

            return $this->responseJson($data);

        } catch (\Exception $e) {
            throw $e;
        }
    }

    public function index_follower(Request $request, User $user)
    {
        try {

            $data = $this->followService->getFollowers($user, $request->keyword);

            return $this->responseJson($data);

        } catch (\Exception $e) {
            throw $e;
        }
    }

    public function create(FollowCreateRequest $request)
    {
        try {

            $data = $this->followService->createFollows(Auth::id(), $request->follower_id);

            return $this->responseJson($data);

        } catch (\Exception $e) {
            throw $e;
        }
    }

    public function delete(FollowDeleteRequest $request, User $user)
    {
        try {

            $data = $this->followService->deleteFollows(Auth::id(), $user->id);

            return $this->responseJson($data);

        } catch (\Exception $e) {
            throw $e;
        }
    }
}
