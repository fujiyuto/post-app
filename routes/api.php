<?php

use App\Http\Controllers\FollowController;
use App\Http\Controllers\LikeController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\RestaurantController;
use App\Http\Controllers\UserStoreRestaurantController;
use App\Models\UserStoreRestaurant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::controller(UserController::class)->group(function () {
    // ユーザー情報取得
    Route::get('/users', 'show')->name('users.show');
    // ユーザー登録
    Route::post('/users', 'create')->name('users.create');
    // ユーザー編集
    Route::patch('/users', 'edit')->name('users.edit');
    // 退会
    Route::delete('/users')->name('users.delete');
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
    Route::post('/posts', 'create')->name('posts.create');
    // 投稿編集
    Route::patch('/posts', 'edit')->name('posts.edit');
    // 投稿削除
    Route::delete('/posts', 'delete')->name('posts.delete');
});

Route::controller(LikeController::class)->group(function () {
    // いいねした投稿取得
    Route::get('/posts/{user}/likes', 'index_posts')->where('user', '[0-9]+')->name('likes.index_posts');
    // いいねしたユーザー取得
    Route::get('/users/{post}/likes', 'index_users')->where('post', '[0-9]+')->name('likes.index_users');
    // いいね作成
    Route::post('/posts/likes', 'create')->name('likes.create');
    // いいね削除
    Route::delete('/posts/likes', 'delete')->name('likes.delete');
});

Route::controller(RestaurantController::class)->group(function () {
    // 店一覧取得
    Route::get('/restaurants', 'index')->name('restaurants.index');
    // 店詳細取得
    Route::get('/restaurants/{restaurant}', 'show')->where('restaurant', '[0-9]+')->name('restaurants.show');
    // 店作成
    Route::post('/restaurants', 'create')->name('restaurants.create');
    // 店編集
    Route::patch('/restaurants', 'edit')->name('restaurants.edit');
    // 店削除
    Route::delete('/restaurants', 'delete')->where('restaurant', '[0-9]+')->name('restaurants.delete');
});

Route::controller(UserStoreRestaurantController::class)->group(function () {
    // ユーザー保存店一覧取得
    Route::get('/users/store/restaurants', 'index')->name('user_restaurant_store.index');
    // ユーザー保存店登録
    Route::post('/users/store/restaurants', 'create')->name('user_restaurant_store.create');
    // ユーザー保存店削除
    Route::post('/users/store/restaurants', 'delete')->name('user_restaurant_store.delete');
});

Route::controller(FollowController::class)->group(function () {
    // フォローユーザー取得
    Route::get('/follows/follow', 'index_follow')->name('follows.index_follow');
    // フォロワーユーザー取得
    Route::get('/follows/follower', 'index_follower')->name('follows.index_follower');
    // ユーザーフォロー
    Route::post('/follows', 'create')->name('follows.create');
    // ユーザーアンフォロー
    Route::delete('/follows', 'delete')->name('follows.delete');
});