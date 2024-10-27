<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;
use App\Models\User;
use App\Models\Restaurant;
use App\Http\Requests\PostCreateRequest;
use App\Http\Requests\PostEditRequest;
use App\Http\Requests\PostDeleteRequest;
use App\Services\PostService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class PostController extends Controller
{
    private $postService;

    public function __construct(PostService $postService)
    {
        $this->postService = $postService;
    }

    public function index_restaurant(Restaurant $restaurant)
    {
        try {

            $data = $this->postService->getRestaurantPosts($restaurant->id);

            return $this->responseJson($data);

        } catch (\Exception $e) {
            throw $e;
        }
    }

    public function show(Post $post)
    {
        try {

            $data = $this->postService->getPost($post->id);

            return $this->responseJson($data);

        } catch (\Exception $e) {
            throw $e;
        }
    }

    public function index_user(User $user)
    {
        try {

            $data = $this->postService->getUserPosts($user->id, $user->user_name);

            return $this->responseJson($data);

        } catch (\Exception $e) {
            throw $e;
        }
    }


    public function create(PostCreateRequest $request)
    {
        try {

            // 画像保存処理
            $image_url1 = $request->file('image_url1')
                        ? Storage::putFile('posts', $request->file('image_url1'))
                        : null;
            $image_url2 = $request->file('image_url2')
                        ? Storage::putFile('posts', $request->file('image_url2'))
                        : null;
            $image_url3 = $request->file('image_url3')
                        ? Storage::putFile('posts', $request->file('image_url3'))
                        : null;

            $data = $this->postService->createPost(
                Auth::id(),
                $request->restaurant_id,
                $request->title,
                $request->content,
                $request->visited_at,
                $request->period_of_time,
                $request->points,
                $request->price_min,
                $request->price_max,
                $image_url1,
                $image_url2,
                $image_url3,
            );

            return $this->responseJson($data);

        } catch (\Exception $e) {
            throw $e;
        }
    }

    public function edit(PostEditRequest $request, Post $post)
    {
        try {

            // 画像保存処理
            $image_url1 = $request->file('image_url1')
                        ? Storage::putFile('posts', $request->file('image_url1'))
                        : null;
            $image_url2 = $request->file('image_url2')
                        ? Storage::putFile('posts', $request->file('image_url2'))
                        : null;
            $image_url3 = $request->file('image_url3')
                        ? Storage::putFile('posts', $request->file('image_url3'))
                        : null;

            $data = $this->postService->updatePost(
                $post,
                $request->title,
                $request->content,
                $request->visited_at,
                $request->period_of_time,
                $request->points,
                $request->price_min,
                $request->price_max,
                $image_url1,
                $image_url2,
                $image_url3,
            );

            return $this->responseJson($data);

        } catch (\Exception $e) {
            throw $e;
        }
    }

    public function delete(PostDeleteRequest $request, Post $post)
    {
        try {

            $data = $this->postService->deletePost($post);

            return $this->responseJson($data);

        } catch (\Exception $e) {
            throw $e;
        }
    }
}
