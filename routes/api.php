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
use App\Http\Controllers\API\MediaController;
use App\Http\Controllers\API\ManageLinkController;
use App\Http\Controllers\API\AccountInfluencerController;
use App\Http\Controllers\API\AccountInfluencerItemController;
use App\Http\Controllers\API\InternalCommunicationController;
use App\Http\Controllers\API\InternalCommunicationItemController;
use App\Http\Controllers\API\ScoringController;
use App\Http\Controllers\API\ScoringItemController;

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
    Route::get('posts', [PostsController::class, 'indexWeb']);
    Route::get('post/{id}', [PostsController::class, 'showById']);
    Route::get('post/slug/{id}', [PostsController::class, 'showBySlug']);
    Route::get('posts/relates', [PostsController::class, 'showRelates']);

    // Categories
    Route::get('categories', [CategoriesController::class, 'index']);

});


Route::group(['middleware' => 'auth:api'], function() {

    // Grafik
    Route::get('grafik/counts', [GrafikController::class, 'index']);
    Route::get('grafik/posts', [GrafikController::class, 'indexPostStatusByUnit']);
    Route::get('grafik/unit', [GrafikController::class, 'indexUserTypeByUnit']);

    // logout / detail user
    Route::post('logout', [UserController::class, 'logout']);
    Route::get('user/me', [UserController::class, 'me']);
    Route::get('users', [UserController::class, 'index']);
    Route::get('user/{id}', [UserController::class, 'showById']);
    Route::patch('user/{id}',  [UserController::class, 'updateById']);
    Route::delete('user/{id}', [UserController::class, 'deleteById']);
    Route::post('user/restore/{id}', [UserController::class, 'restoreById']);
    Route::post('update-password', [UserController::class, 'updatePassword']);

    // Posts
    Route::get('posts', [PostsController::class, 'index']);
    Route::get('posts/download', [PostsController::class, 'indexDownload']);
    Route::get('posts/archived', [PostsController::class, 'indexArchived']);
    Route::post('post', [PostsController::class, 'create']);
    Route::post('post/restore/{id}', [PostsController::class, 'restoreById']);
    Route::get('post/{id}', [PostsController::class, 'showById']);
    Route::get('post/slug/{id}', [PostsController::class, 'showBySlug']);
    Route::get('posts/relates', [PostsController::class, 'showRelates']);
    Route::patch('post/{id}', [PostsController::class, 'updateById']);
    Route::patch('post/status/{id}', [PostsController::class, 'updateStatusById']);
    Route::post('post/recreate/{id}', [PostsController::class, 'updateReplicateById']);
    Route::delete('post/{id}', [PostsController::class, 'deleteById']);

    // Media
    Route::get('media', [MediaController::class, 'index']);
    Route::get('media/archived', [MediaController::class, 'indexArchived']);
    Route::post('media', [MediaController::class, 'create']);
    Route::post('media/restore/{id}', [MediaController::class, 'restoreById']);
    Route::get('media/{id}', [MediaController::class, 'showById']);
    Route::patch('media/{id}', [MediaController::class, 'updateById']);
    Route::patch('media/status/{id}', [MediaController::class, 'updateStatusById']);
    Route::delete('media/{id}', [MediaController::class, 'deleteById']);

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

    // Link
    Route::get('links', [ManageLinkController::class, 'index']);
    Route::get('links/archived', [ManageLinkController::class, 'indexArchived']);
    Route::get('link/{id}', [ManageLinkController::class, 'showById']);
    Route::get('link/key/{id}', [ManageLinkController::class, 'showByKey']);
    Route::get('link/key/{id}/active', [ManageLinkController::class, 'showByKeyActive']);
    Route::post('link', [ManageLinkController::class, 'create']);
    Route::patch('link/{id}', [ManageLinkController::class, 'updateById']);
    Route::delete('link/{id}', [ManageLinkController::class, 'deleteById']);
    Route::post('link/restore/{id}', [ManageLinkController::class, 'restoreById']);

    // Account Influencer
    Route::get('account-influencer', [AccountInfluencerController::class, 'index']);
    Route::post('account-influencer', [AccountInfluencerController::class, 'create']);
    Route::get('account-influencer/{id}', [AccountInfluencerController::class, 'showById']);
    Route::patch('account-influencer/{id}', [AccountInfluencerController::class, 'updateById']);
    Route::delete('account-influencer/{id}', [AccountInfluencerController::class, 'deleteById']);

    // Account Influencer Item
    Route::get('account-influencer-item', [AccountInfluencerItemController::class, 'index']);
    Route::post('account-influencer-item', [AccountInfluencerItemController::class, 'create']);
    Route::get('account-influencer-item/{id}', [AccountInfluencerItemController::class, 'showById']);
    Route::patch('account-influencer-item/{id}', [AccountInfluencerItemController::class, 'updateById']);
    Route::delete('account-influencer-item/{id}', [AccountInfluencerItemController::class, 'deleteById']);

    // Internal Communication
    Route::get('internal_communication', [InternalCommunicationController::class, 'index']);
    Route::post('internal_communication', [InternalCommunicationController::class, 'create']);
    Route::get('internal_communication/{id}', [InternalCommunicationController::class, 'showById']);
    Route::patch('internal_communication/{id}', [InternalCommunicationController::class, 'updateById']);
    Route::delete('internal_communication/{id}', [InternalCommunicationController::class, 'deleteById']);

    // Internal Communication Item
    Route::get('internal_communication-item', [InternalCommunicationItemController::class, 'index']);
    Route::post('internal_communication-item', [InternalCommunicationItemController::class, 'create']);
    Route::get('internal_communication-item/{id}', [InternalCommunicationItemController::class, 'showById']);
    Route::patch('internal_communication-item/{id}', [InternalCommunicationItemController::class, 'updateById']);
    Route::delete('internal_communication-item/{id}', [InternalCommunicationItemController::class, 'deleteById']);

    // Scoring
    Route::get('scoring', [ScoringController::class, 'index']);
    Route::post('scoring', [ScoringController::class, 'create']);
    Route::get('scoring/{id}', [ScoringController::class, 'showById']);
    Route::patch('scoring/{id}', [ScoringController::class, 'updateById']);
    Route::delete('scoring/{id}', [ScoringController::class, 'deleteById']);

    // Scoring Item
    Route::get('scoring-item', [ScoringItemController::class, 'index']);
    Route::post('scoring-item', [ScoringItemController::class, 'create']);
    Route::get('scoring-item/{id}', [ScoringItemController::class, 'showById']);
    Route::patch('scoring-item/{id}', [ScoringItemController::class, 'updateById']);
    Route::delete('scoring-item/{id}', [ScoringItemController::class, 'deleteById']);

    // Notifications
    Route::get('notifications', [NotificationsController::class, 'index']);

    // Service Upload
    Route::post('upload-image', [ServicesController::class, 'uploadImage']);

});
