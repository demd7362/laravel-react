<?php

use App\Http\Controllers\auth\AuthController;
use App\Http\Controllers\post\CommentController;
use App\Http\Controllers\post\PostController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::controller(AuthController::class)->group(function () {
    Route::post('auth/login', 'login');
    Route::get('auth/email/{email}/exists', 'checkEmail');
    Route::get('auth/nickname/{nickname}/exists', 'checkNickname');
    Route::post('auth/register', 'register');
    Route::post('auth/logout', 'logout');
});

Route::controller(PostController::class)->group(function () {
    Route::get('posts', 'index');
    Route::get('posts/{postId}', 'show');
    Route::post('posts', 'store');
    Route::patch('posts/{postId}', 'update');
    Route::delete('posts/{postId}', 'destroy');
});

Route::controller(CommentController::class)->group(function () {
    Route::get('posts/{postId}/comments', 'index');
    Route::post('posts/{postId}/comments', 'store');
    Route::patch('posts/{postId}/comments/{commentId}', 'update');
    Route::delete('posts/{postId}/comments/{commentId}', 'destroy');
});
