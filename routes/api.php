<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\UserController;
use App\Http\Controllers\API\PostsController;
use App\Http\Controllers\API\ServicesController;
use App\Http\Controllers\API\CategoriesController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::post('register',  [UserController::class, 'register']);
Route::post('login',  [UserController::class, 'login']);
Route::post('forgot-password',  [UserController::class, 'forgotPassword']);


// WEB 
Route::prefix('web')->group(function () {

    // Posts
    Route::get('posts', [PostsController::class, 'index']);
    Route::get('post/{id}', [PostsController::class, 'showById']);
    Route::get('post/slug/{id}', [PostsController::class, 'showBySlug']);
    Route::get('posts/relates', [PostsController::class, 'showRelates']);

    // Categories
    Route::get('categories', [CategoriesController::class, 'index']);

});


Route::group(['middleware' => 'auth:api'], function() {

    // logout / detail user
    Route::post('logout', [UserController::class, 'logout']);
    Route::get('user/me', [UserController::class, 'me']);
    Route::get('user/{id}', [UserController::class, 'showById']);
    Route::post('update-password', [UserController::class, 'updatePassword']);

    // Posts
    Route::get('posts', [PostsController::class, 'index']);
    Route::post('post', [PostsController::class, 'create']);
    Route::get('post/{id}', [PostsController::class, 'showById']);
    Route::get('post/slug/{id}', [PostsController::class, 'showBySlug']);
    Route::get('posts/relates', [PostsController::class, 'showRelates']);
    Route::patch('post/{id}', [PostsController::class, 'updateById']);
    Route::delete('post/{id}', [PostsController::class, 'deleteById']);

    // Categories
    Route::get('categories', [CategoriesController::class, 'index']);
    Route::post('category', [CategoriesController::class, 'create']);
    Route::get('category/{id}', [CategoriesController::class, 'showById']);
    Route::patch('category/{id}', [CategoriesController::class, 'updateById']);
    Route::delete('category/{id}', [CategoriesController::class, 'deleteById']);

    Route::post('upload-image', [ServicesController::class, 'uploadImage']);

});
