<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Post;
use App\Http\Requests\LikeCreateRequest;
use App\Http\Requests\LikeDeleteRequest;

class LikeController extends Controller
{
    public function __construct()
    {

    }

    /**
     * ユーザーがいいねした投稿の一覧を取得
     *
     * @param User $user
     * @return void
     */
    public function index_posts(User $user)
    {

    }

    /**
     * 投稿にいいねしたユーザー一覧を取得
     *
     * @param Post $post
     * @return void
     */
    public function index_users(Post $post)
    {

    }

    public function create(LikeCreateRequest $request)
    {

    }

    public function delete(LikeDeleteRequest $request)
    {

    }
}
