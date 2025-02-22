<?php

use App\Http\Controllers\auth\AuthController;
use App\Http\Controllers\post\PostController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::controller(AuthController::class)->group(function () {
    Route::post('login', 'login');
    Route::get('check-email', 'checkEmail');
    Route::get('check-nickname', 'checkNickname');
    Route::post('register', 'register');
    Route::post('logout', 'logout');
});

Route::controller(PostController::class)->group(function () {
    Route::get('posts', 'index');
    Route::get('posts/{post}', 'show');
    Route::post('posts', 'store');
    Route::put('posts/{post}', 'update');
    Route::delete('posts/{post}', 'destroy');
});

// TODO comment
