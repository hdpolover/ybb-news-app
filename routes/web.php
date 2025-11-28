<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Frontend\HomeController;
use App\Http\Controllers\Frontend\PostController;
use App\Http\Controllers\Frontend\CategoryController;

// Frontend Routes
// Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/posts', [PostController::class, 'index'])->name('posts.index');
Route::get('/post/{slug}', [PostController::class, 'show'])->name('posts.show');
Route::get('/category/{slug}', [CategoryController::class, 'show'])->name('categories.show');
Route::get('/tag/{slug}', [CategoryController::class, 'show'])->name('tags.show');

Route::get('/', function () {
    return redirect('app/login');
});