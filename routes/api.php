<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\CommentController;

// Public Routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Protected Routes (Requires Authentication)
Route::middleware(['auth:api'])->group(function () {

    // Authentication
    Route::post('/logout', [AuthController::class, 'logout']);

    // Blog Posts
    Route::get('/posts', [PostController::class, 'index']);
    Route::get('/posts/{id}', [PostController::class, 'show']);

    // Create, Update, and Delete Posts (Restricted to Authors & Admins)
    Route::post('/posts', [PostController::class, 'store'])->middleware('role:author,admin');
    Route::put('/posts/{id}', [PostController::class, 'update'])->middleware('role:author,admin');
    Route::delete('/posts/{id}', [PostController::class, 'destroy'])->middleware('role:author,admin');

    // Comments
    Route::post('/posts/{id}/comments', [CommentController::class, 'store']);
});

Route::get('/posts/search', [PostController::class, 'search']);
