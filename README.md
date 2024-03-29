# install dependencies

composer install

# create new app

composer create-project laravel/laravel anothertestapp

> php artisan serve

### CONTROLLER

> php artisan make:controller ExampleController

### MIGRATIONS

> php artisan migrate

# drop tables & create anew

> php artisan migrate:fresh

### MIDDLEWARE

> php artisan make:middleware MustBeLoggedIn
> /app/Http/Kernel.php add the middleware

### Get User Posts

> web.php -> Route::get('/profile/{user:username}') ->pass the User username to UserController
> User.php model -> posts() -> $this->hasMany(Post::class, 'user_id');
> UserController -> 'posts' => $user->posts()->latest()->get()

### Policies

> Create a policy to allow only you to edit your posts
> php artisan make:policy PostPolicy --model=Post
> Add PostPolicy to app/Providers/AuthServiceProvider.php
> add in the blade template single-post.blade: @can('update', $post)

### IsAdmin Gate

> Add Gate to /app/Providers/AuthServiceProvider.php
> add route to web.php ->middleware('can:visitAdminPages')

### Upload files / Add link to Folder

php artisan storage:link

### Added Avatar / avatar link

> User model : Attribute::make()

### Follows Table

> 'user_id' = user doing the following, 'followeduser'

### Profile pages subs

> profile.blade.php includes profile-posts.blade.php
> UserController : private function getSharedProfileData($user)
> View::share('sharedData', [xyz])

### Get Additional user data from Other Table

> Models/Follow.php -> public function userDoingTheFollowing() {}
> UserController -> ['followers' => $user->followers()->latest()->get()]
> use in template profile-followers.blade.php

### Get followed posts on the first page (login users)

> User model -> public function feedPosts() {}
> Reads the tables: User -> Follows -> Posts

### Search post with Laravel Scout

> composer require laravel/scout
> install: php artisan vendor:publish --provider="Laravel\Scout\ScoutServiceProvider"
> .env -> SCOUT_DRIVER=database

> Load additional: search($term) {$posts->load('user:id,username,avatar');}

### JavaScript Search

> npm i -> resources folder, npm i dompurify
> /resources/js/live-search.js
> /views/components/layout.blade.js -> @vite(['resources/css/app.css']) & @vite(['resources/js/app.js'])
> /resources/js/app.js -> new Search()

### Events & Listeners

> edit file /app/provider/EventServiceProvider.php
> php artisan event:generate
> OurExampleListener.php -> Log::debug("")
> logging in file: /storage/logs/laravel.log
> OurExampleEvent -> \_\_construct($theEvent)  { $this->username = $theEvent['username']; $this->action = $theEvent['action']; }
> OurExampleListener -> Log::debug("The user {$event->username} just performed {$event->action}.");
> UserController.php -> event(new OurExampleEvent(['username' => auth()->user()->username,'action' => 'logout']));

### Chat event

> pusher account
> .env edited
> /app/Events/ChatMessage.php
> /app/routes/channels.php
> added /resources/js/chat.js
> layout.blade.php added -> <div data-username="{{auth()->user()->username}}" data-avatar="{{auth()->user()->avatar}}">
> /app/resources/bootstrap.js added -> import Echo from "laravel-echo"; import Pusher from "pusher-js";
> /routes/web.php -> Route::post('/send-chat-message')

### send email

> mail trap account -> .env
> views -> new-post-email.php
> php artisan make:mail NewPostEmail -> /app/Mail/NewPostEmail.php
> PostController.php -> public function storeNewPost( ... Mail::to() ...)

### Job

> php artisan make:job SendNewPostEmail -> /Jobs/
>
> .env -> QUEUE_CONNECTION=database
> new table migration: php artisan queue:table
> always run Job: php artisan queue:work
