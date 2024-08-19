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

    public function index_posts(User $user)
    {

    }

    public function index_users(Post $post)
    {

    }

    public function create(LikeCreateRequest $request, Post $post)
    {

    }

    public function delete(LikeDeleteRequest $request, Post $post)
    {
        
    }
}
