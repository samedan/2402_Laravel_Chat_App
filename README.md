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
