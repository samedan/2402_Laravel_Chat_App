<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PostController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\FollowController;



/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// ADMIN Route
Route::get('/admins-only', function() {
   return 'Only visible for admins';
})->middleware('can:visitAdminPages');

Route::get('/', [UserController::class, "showCorrectHomepage"])->name('login'); // name given so middleware(auth) sends to 'login' page

// User LOGIN
Route::post('/register', [UserController::class, 'register'])->middleware('guest');
Route::post('/login', [UserController::class, 'login'])->middleware('guest');
Route::post('/logout', [UserController::class, 'logout'])->middleware('mustBeLoggedIn');

// Follow POST Routes
Route::post('/create-follow/{user:username}', [FollowController::class, 'createFollow'])->middleware('mustBeLoggedIn');
Route::post('/remove-follow/{user:username}', [FollowController::class, 'removeFollow'])->middleware('mustBeLoggedIn');

//User Avatar
Route::get('/manage-avatar', [UserController::class, 'showAvatarForm'])->middleware('mustBeLoggedIn');
Route::post('/manage-avatar', [UserController::class, 'storeAvatar'])->middleware('mustBeLoggedIn');

// Blog Posts
Route::get('/create-post', [PostController::class, 'showCreateForm'])->middleware('mustBeLoggedIn');
Route::post('/create-post', [PostController::class, 'storeNewPost'])->middleware('mustBeLoggedIn');
Route::get('/post/{post}', [PostController::class, 'viewSinglePost']);
Route::delete('/post/{post}', [PostController::class, 'delete'])->middleware('can:delete,post');
// view edit form
Route::get('/post/{post}/edit', [PostController::class, 'showEditForm'])->middleware('can:update,post');
Route::put('/post/{post}', [PostController::class, 'actuallyUpdate'])->middleware('can:update,post');

// Profile
Route::get('/profile/{user:username}', [UserController::class, 'profile']); // 'username' is not id, it becomes the defauylt search ietm in the dbb
// Profile Followers
Route::get('/profile/{user:username}/followers', [UserController::class, 'profileFollowers']); // 'username' is not id, it becomes the defauylt search ietm in the dbb
// Profile Following
Route::get('/profile/{user:username}/following', [UserController::class, 'profileFollowing']); // 'username' is not id, it becomes the defauylt search ietm in the dbb
