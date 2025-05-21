<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BerandaController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

// halaman utama
Route::get('/', [BerandaController::class, 'index'])->name('beranda');

// post
Route::resource('post', PostController::class)->only(['index', 'show', 'store']);
Route::controller(PostController::class)->group(function () {
    Route::get('/followingPost', 'postByFollowing')->name('post.following');
    Route::post('/post/{post}/like', 'likePost')->name('post.like');
    Route::post('/post/{post}/comment', 'commentPost')->name('post.comment');
});
Route::get('/user/{id}/posts', [PostController::class, 'postsByUser'])->name('user.posts');

// user
Route::controller(UserController::class)->group(function () {
    Route::get('/profile/{id}', 'showProfile')->name('profile');
    Route::patch('/profile/{id}/updatePhoto', 'updatePhotoProfile')->name('profile.updatePhoto');
    Route::patch('/profile/{id}/updateBanner', 'updateBannerProfile')->name('profile.updateBanner');
    Route::patch('/profile/{id}/updateBio', [UserController::class, 'updateBio'])->name('profile.updateBio');
    // function follow and unfollow
    Route::post('/profile/{id}/follow', 'follow')->name('profile.follow');
    Route::post('/profile/{id}/unfollow', 'unfollow')->name('profile.unfollow');
    // function view followers and followings
    Route::get('/profile/{id}/followers', 'followers')->name('profile.followers');
    Route::get('/profile/{id}/followings', 'followings')->name('profile.followings');
    // function get followers and followings data
    Route::get('/profile/{id}/followers-data', 'followersData')->name('profile.followers.data');
    Route::get('/profile/{id}/followings-data', 'followingsData')->name('profile.followings.data');
});

// halaman login dan register
Route::controller(AuthController::class)->group(function () {
    Route::middleware('guest')->group(function () {
        Route::get('/register', 'registerPage')->name('register');
        Route::post('/register', 'registerProcess')->name('post-register');

        Route::get('/login', 'loginPage')->name('login');
        Route::post('/login', 'loginProcess')->name('post-login');
    });
    Route::post('/logout', 'logout')->name('logout');
});
