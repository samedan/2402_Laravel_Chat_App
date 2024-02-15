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
