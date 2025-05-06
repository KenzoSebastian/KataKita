<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BerandaController;
use Illuminate\Support\Facades\Route;

Route::controller(BerandaController::class)->group(function () {
    Route::get("/", "index")->name("beranda");
    Route::get("/post/{slug}", "showPost")->name("show-post");
    Route::post("/post/{slug}/like", "likePost")->name("like-post");
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
