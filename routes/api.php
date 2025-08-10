<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\Api\PostController;
use App\Http\Controllers\Api\TagsController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\LogActivityController;
use App\Http\Controllers\Api\PostActivityController;

/*
|--------------------------------------------------------------------------
| AUTHENTICATION
|--------------------------------------------------------------------------
*/
Route::post('/login', [AuthController::class, 'login']);
Route::middleware('auth:sanctum')->post('/logout', [AuthController::class, 'logout']);

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


/*
|--------------------------------------------------------------------------
| CATEGORIES
|--------------------------------------------------------------------------
*/
// Public
Route::get('/categories', [CategoryController::class, 'index']);
Route::get('/categories/{slug}', [CategoryController::class, 'show']);

// Admin
Route::middleware(['auth:sanctum', 'role:admin'])->prefix('categories')->group(function () {
    Route::get('/admin', [CategoryController::class, 'adminIndex']);
    Route::post('/', [CategoryController::class, 'store']);
    Route::put('/{id}', [CategoryController::class, 'update']);
    Route::delete('/{id}', [CategoryController::class, 'destroy']);
});


/*
|--------------------------------------------------------------------------
| TAGS
|--------------------------------------------------------------------------
*/
// Public
Route::get('/tags', [TagsController::class, 'index']);
Route::get('/tags/{id}', [TagsController::class, 'show']);

// Admin
Route::middleware(['auth:sanctum', 'role:admin'])->prefix('tags')->group(function () {
    Route::get('/admin', [TagsController::class, 'adminIndex']);
    Route::post('/', [TagsController::class, 'store']);
    Route::put('/{id}', [TagsController::class, 'update']);
    Route::delete('/{id}', [TagsController::class, 'destroy']);
});


/*
|--------------------------------------------------------------------------
| POSTS
|--------------------------------------------------------------------------
*/
// Customer / Public
Route::get('/posts', [PostController::class, 'index']);
Route::get('/posts/{slug}', [PostController::class, 'show']);

// Admin
Route::middleware(['auth:sanctum', 'role:admin'])->prefix('posts')->group(function () {
    Route::get('/admin', [PostController::class, 'adminIndex']);
    Route::post('/', [PostController::class, 'store']);
    Route::put('/{slug}', [PostController::class, 'update']);
    Route::delete('/{slug}', [PostController::class, 'destroy']);
});


/*
|--------------------------------------------------------------------------
| POST ACTIVITIES (Customer & Admin)
|--------------------------------------------------------------------------
*/
// Customer: Catat aktivitas kunjungan ke post
Route::post('/post-activities', [PostActivityController::class, 'store']);

// Admin: Lihat statistik aktivitas berdasarkan post_id (opsional)
Route::middleware(['auth:sanctum', 'role:admin'])->get('/post-activities/{post_id}', [PostActivityController::class, 'showByPost']);


/*
|--------------------------------------------------------------------------
| LOG ACTIVITIES (Admin Only)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth:sanctum', 'role:admin'])->prefix('log-activities')->group(function () {
    Route::get('/', [LogActivityController::class, 'index']);
    Route::get('/{id}', [LogActivityController::class, 'show']);
});
