<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BerandaController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', [BerandaController::class, 'index'])->name('beranda');

Route::resource('post', PostController::class)->only(['index', 'show', 'store']);
Route::controller(PostController::class)->group(function () {
    Route::get('/followingPost', 'postByFollowing')->name('post.following');
    Route::post('/post/{post}/like', 'likePost')->name('post.like');
    Route::post('/post/{post}/comment', 'commentPost')->name('post.comment');
});

Route::controller(UserController::class)->group(function () {
    Route::get('/profile/{id}', 'showProfile')->name('profile');
    Route::patch('/profile/{id}/update', 'updatePhotoProfile')->name('update-profile');
});

Route::controller(AuthController::class)->group(function () {
    Route::middleware('guest')->group(function () {
        Route::get('/register', 'registerPage')->name('register');
        Route::post('/register', 'registerProcess')->name('post-register');

        Route::get('/login', 'loginPage')->name('login');
        Route::post('/login', 'loginProcess')->name('post-login');
    });
    Route::post('/logout', 'logout')->name('logout');
});
