<?php

use App\Events\ChatMessage;
use Illuminate\Http\Request;
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
// search blogs
Route::get('/search/{term}', [PostController::class, 'search']);

// Profile
Route::get('/profile/{user:username}', [UserController::class, 'profile']); // 'username' is not id, it becomes the defauylt search ietm in the dbb
// Profile Followers
Route::get('/profile/{user:username}/followers', [UserController::class, 'profileFollowers']); // 'username' is not id, it becomes the defauylt search ietm in the dbb
// Profile Following
Route::get('/profile/{user:username}/following', [UserController::class, 'profileFollowing']); // 'username' is not id, it becomes the defauylt search ietm in the dbb

// CACHE routes
Route::middleware('cache.headers:public;max_age=20;etag')->group(function() {
      // Profile JSON
      Route::get('/profile/{user:username}/raw', [UserController::class, 'profileRaw']); // 'username' is not id, it becomes the defauylt search ietm in the dbb
      // Profile Followers JSON
      Route::get('/profile/{user:username}/followers/raw', [UserController::class, 'profileFollowersRaw']); // 'username' is not id, it becomes the defauylt search ietm in the dbb
      // Profile Following JSON
      Route::get('/profile/{user:username}/following/raw', [UserController::class, 'profileFollowingRaw']); // 'username' is not id, it becomes the defauylt search ietm in the dbb
});


// Pusher POST chat
Route::post('/send-chat-message', function (Request $request) {
   $formFields = $request->validate([
      'textvalue' => 'required'
   ]);
   // trim white spaces
   if(!trim(strip_tags($formFields['textvalue']))) {
      return response()->noContent();
   }
   broadcast(new ChatMessage([
      'username' => auth()->user()->username,
      'textvalue' => strip_tags($request->textvalue),
      'avatar' => auth()->user()->avatar,
   ]))->toOthers();
   return response()->noContent();
})->middleware('mustBeLoggedIn');
