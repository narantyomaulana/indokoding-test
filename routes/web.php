<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PostController;
use App\Http\Controllers\ReplyController;
use App\Http\Controllers\ContentController;
use App\Http\Controllers\CommentsController;

// Route::get('/', function () {
//     return view('welcome');
// });

Route::get('/', [ContentController::class, 'index'])->name('content.index');
Route::resource('comment', CommentsController::class);

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::resource('posts', PostController::class);
Route::post('/like-post', [PostController::class, 'likePost'])->name('like.post');
Route::post('/comments/{commentId}/replies', [ReplyController::class, 'store']);
