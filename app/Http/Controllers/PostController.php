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
        try {

            $data = $this->postService->getPosts();

            return $this->responseJson($data);

        } catch (\Exception $e) {
            throw $e;
        }
    }

    public function index_user(User $user)
    {
        try {

        } catch (\Exception $e) {

        }
    }

    public function show(Post $post)
    {
        try {

            $data = $this->postService->getPost($post->id);

            return $this->responseJson($data);

        } catch (\Exception $e) {

        }
    }

    public function create(PostCreateRequest $request)
    {
        try {

        } catch (\Exception $e) {

        }
    }

    public function edit(PostEditRequest $request, Post $post)
    {
        try {

        } catch (\Exception $e) {

        }
    }

    public function delete(PostDeleteRequest $request, Post $post)
    {
        try {

        } catch (\Exception $e) {

        }
    }
}
