<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BerandaController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::controller(BerandaController::class)->group(function () {
    Route::get("/", "index")->name("beranda");
    Route::get("/post/{slug}", "showPost")->name("show-post");
    Route::post("/post/create", "createPost")->name("create-post");
    Route::post("/post/{slug}/like", "likePost")->name("like-post");
    Route::post("/post/{slug}/comment", "commentPost")->name("comment-post");
});

Route::controller(UserController::class)->group(function () {
    Route::get("/profile/{id}", "showProfile")->name("profile");
    Route::patch("/profile/{id}/update", "updateProfile")->name("update-profile");
});

Route::controller(AuthController::class)->group(function () {
    Route::middleware("guest")->group(function () {
        Route::get("/register", "registerPage")->name("register");
        Route::post("/register", "registerProcess")->name("post-register");

        Route::get("/login", "loginPage")->name("login");
        Route::post("/login", "loginProcess")->name("post-login");
    });
    Route::post("/logout", "logout")->name("logout");
});
