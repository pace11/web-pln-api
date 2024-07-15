<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\UserController;
use App\Http\Controllers\API\PostsController;
use App\Http\Controllers\API\ServicesController;
use App\Http\Controllers\API\CategoriesController;
use App\Http\Controllers\API\GrafikController;
use App\Http\Controllers\API\NotificationsController;
use App\Http\Controllers\API\UnitController;

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

    // Grafik
    Route::get('grafik', [GrafikController::class, 'index']);

    // logout / detail user
    Route::post('logout', [UserController::class, 'logout']);
    Route::get('user/me', [UserController::class, 'me']);
    Route::get('users', [UserController::class, 'index']);
    Route::get('user/{id}', [UserController::class, 'showById']);
    Route::patch('user/{id}',  [UserController::class, 'updateById']);
    Route::post('update-password', [UserController::class, 'updatePassword']);

    // Posts
    Route::get('posts', [PostsController::class, 'index']);
    Route::get('posts/archived', [PostsController::class, 'indexArchived']);
    Route::post('post', [PostsController::class, 'create']);
    Route::post('post/restore/{id}', [PostsController::class, 'restoreById']);
    Route::get('post/{id}', [PostsController::class, 'showById']);
    Route::get('post/slug/{id}', [PostsController::class, 'showBySlug']);
    Route::get('posts/relates', [PostsController::class, 'showRelates']);
    Route::patch('post/{id}', [PostsController::class, 'updateById']);
    Route::patch('post/status/{id}', [PostsController::class, 'updateStatusById']);
    Route::delete('post/{id}', [PostsController::class, 'deleteById']);

    // Categories
    Route::get('categories', [CategoriesController::class, 'index']);
    Route::get('categories/archived', [CategoriesController::class, 'indexArchived']);
    Route::post('category', [CategoriesController::class, 'create']);
    Route::post('category/restore/{id}', [CategoriesController::class, 'restoreById']);
    Route::get('category/{id}', [CategoriesController::class, 'showById']);
    Route::patch('category/{id}', [CategoriesController::class, 'updateById']);
    Route::delete('category/{id}', [CategoriesController::class, 'deleteById']);

    // Unit
    Route::get('units', [UnitController::class, 'index']);
    Route::get('units/option', [UnitController::class, 'indexOption']);
    Route::get('units/archived', [UnitController::class, 'indexArchived']);
    Route::post('unit', [UnitController::class, 'create']);
    Route::post('unit/restore/{id}', [UnitController::class, 'restoreById']);
    Route::get('unit/{id}', [UnitController::class, 'showById']);
    Route::patch('unit/{id}', [UnitController::class, 'updateById']);
    Route::delete('unit/{id}', [UnitController::class, 'deleteById']);

    // Notifications
    Route::get('notifications', [NotificationsController::class, 'index']);

    // Service Upload
    Route::post('upload-image', [ServicesController::class, 'uploadImage']);

});
