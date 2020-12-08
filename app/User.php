<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;

    protected $table = 'users';

    protected $fillable = [
        'name', 'email', 'password',
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    public function posts()
    {
        return $this->hasMany(Post::class);
    }
    public function likes()
    {
        return $this->hasMany('App\Like');
    }
    //All the followers of a user
    public function followers() 
    {
        return $this->belongsToMany(self::class, 'followers', 'follows_id', 'user_id')
                    ->withTimestamps();
    }

    //Everyone the user is following
    public function follows() 
    {
        return $this->belongsToMany(self::class, 'followers', 'user_id', 'follows_id')
                    ->withTimestamps();
    }
    //when you want to follow
    public function follow($userId) 
    {
        $this->follows()->attach($userId);
        return $this;
    }
    //when you want to unfollow
    public function unfollow($userId)
    {
        $this->follows()->detach($userId);
        return $this;
    }
    //user already you follow
    public function isFollowing($userId) 
    {
        return (boolean) $this->follows()->where('follows_id', $userId)->first(['id']);
    }
}
