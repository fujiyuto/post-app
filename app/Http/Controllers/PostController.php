<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;
use App\Models\User;
use App\Http\Requests\PostCreateRequest;
use App\Http\Requests\PostEditRequest;
use App\Http\Requests\PostDeleteRequest;
use App\Services\PostService;

class PostController extends Controller
{
    private $postService;

    public function __construct(PostService $postService)
    {
        $this->postService = $postService;
    }

    public function index()
    {

    }

    public function index_user(User $user)
    {
        
    }

    public function show(Post $post)
    {

    }

    public function create(PostCreateRequest $request)
    {

    }

    public function edit(PostEditRequest $request, Post $post)
    {

    }

    public function delete(PostDeleteRequest $request, Post $post)
    {

    }


}
