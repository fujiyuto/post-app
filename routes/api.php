<?php

use App\Http\Controllers\LikeController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\RestaurantController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::controller(UserController::class)->group(function () {
    // ユーザー情報取得
    Route::get('/users/{user}', 'show')->where('user', '[0-9]+')->name('users.show');
    // ユーザー登録
    Route::post('/users/create', 'create')->name('users.create');
    // ユーザー編集
    Route::patch('/users/{user}/edit', 'edit')->where('user', '[0-9]+')->name('users.edit');
    // 退会
    Route::delete('/users{user}/delete')->where('user', '[0-9]+')->name('users.delete');
    // ログイン
    Route::post('/login', 'login')->name('users.login');
    // ログアウト
    Route::post('/logout', 'logout')->name('users.logout');
});

Route::controller(PostController::class)->group(function () {
    // 投稿一覧取得
    Route::get('/posts', 'index')->name('posts.index');
    // 投稿詳細取得
    Route::get('/posts/{post}', 'show')->where('post', '[0-9]+')->name('posts.show');
    // 投稿作成
    Route::post('/posts/create', 'create')->name('posts.create');
    // 投稿編集
    Route::patch('/posts/{post}/edit', 'edit')->where('post', '[0-9]+')->name('posts.edit');
    // 投稿削除
    Route::delete('/posts/{post}/delete', 'delete')->where('post', '[0-9]+')->name('posts.delete');
});

Route::controller(LikeController::class)->group(function () {
    // いいねした投稿取得
    Route::get('/posts/{user}/likes', 'index_posts')->where('user', '[0-9]+')->name('likes.index_posts');
    // いいねしたユーザー取得
    Route::get('/users/{post}/likes', 'index_users')->where('post', '[0-9]+')->name('likes.index_users');
    // いいね作成
    Route::post('/posts/{post}/likes/create', 'create')->where('post', '[0-9]+')->name('likes.create');
    // いいね削除
    Route::delete('/posts/{post}/likes/delete', 'delete')->where('post', '[0-9]+')->name('likes.delete');
});

Route::controller(RestaurantController::class)->group(function () {
    // 店一覧取得
    Route::get('/restaurants', 'index')->name('restaurants.index');
    // 店詳細取得
    Route::get('/restaurants/{restaurant}', 'show')->where('restaurant', '[0-9]+')->name('restaurants.show');
    // 店作成
    Route::post('/restaurants/create', 'create')->name('restaurants.create');
    // 店編集
    Route::patch('/restaurants/{restaurant}/edit', 'edit')->where('restaurant', '[0-9]+')->name('restaurants.edit');
    // 店削除
    Route::delete('/restaurants/{restaurant}/delete', 'delete')->where('restaurant', '[0-9]+')->name('restaurants.delete');
});

