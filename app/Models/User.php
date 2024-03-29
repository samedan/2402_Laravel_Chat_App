<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'username',
        'email',
        'password',
    ];

    // Fallback User Image if NO AVATAR
    protected function avatar(): Attribute {
        return Attribute::make(get: function($value) {
            return $value ? '/storage/avatars/'.$value : '/fallback-avatar.jpg';
        });
    }

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    // Get Posts
    public function feedPosts() {
        return $this->hasManyThrough( // from 'user' table to 'posts' table going through 'follows' table
            Post::class, // final model
            Follow::class, // intermediate table 
            'user_id', // foreign key on the intermediate table (follow)
            'user_id', // foreign key on the final model (post)
            'id', // local key (followeduser) on 'follows' table
            'followeduser' // local key on the intermediate table (follow)
        ); 
    }

    // Get User's Posts
    public function posts() {
        return $this->hasMany(Post::class, 'user_id');
    }

    // Get User's Followers who follow him
    public function followers() {
        return $this->hasMany(Follow::class, 'followeduser', 'id'); // 'id' = local key, 'followeduser' = foreign key
    }

    // Get User's the users he is Following
    public function followingTheseUsers() {
        return $this->hasMany(Follow::class, 'user_id', 'id'); // 'id' = local key, 'user_id' = foreign key
    }
}
