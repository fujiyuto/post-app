<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Post;
use App\Http\Requests\LikeCreateRequest;
use App\Http\Requests\LikeDeleteRequest;
use App\Services\LikeService;

use Illuminate\Support\Facades\Log;

class LikeController extends Controller
{
    private $likeService;

    public function __construct(LikeService $likeService)
    {
        $this->likeService = $likeService;
    }

    /**
     * ユーザーがいいねした投稿の一覧を取得
     *
     * @param User $user
     * @return void
     */
    public function index_posts(User $user)
    {
        try {

            $data = $this->likeService->getLikePosts($user);

            return $this->responseJson($data);

        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * 投稿にいいねしたユーザー一覧を取得
     *
     * @param Post $post
     * @return void
     */
    public function index_users(Post $post)
    {
        try {

            $data = $this->likeService->getLikeUsers($post);

            return $this->responseJson($data);

        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * いいね作成
     *
     * @param LikeCreateRequest $request
     * @return void
     */
    public function create(LikeCreateRequest $request)
    {
        try {

            $data = $this->likeService->createLike($request->user_id, $request->post_id);

            return $this->responseJson($data);

        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * いいね削除
     *
     * @param LikeDeleteRequest $request
     * @return void
     */
    public function delete(LikeDeleteRequest $request)
    {
        try {

            $data = $this->likeService->deleteLike($request->user_id, $request->post_id);

            return $this->responseJson($data);

        } catch (\Exception $e) {
            throw $e;
        }
    }
}
