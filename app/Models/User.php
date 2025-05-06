<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasUuids;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = ["id", "username", "fullname", "email", "password"];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = ["password"];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            "email_verified_at" => "datetime",
            "password" => "hashed",
        ];
    }

    public function followers()
    {
        return $this->hasMany(Follower::class);
    }
    public function followersData()
    {
        return $this->hasManyThrough(User::class, Follower::class, "user_id", "id", "id", "follower_id");
    }

    public function followings()
    {
        return $this->hasMany(Following::class);
    }
    public function followingData()
    {
        return $this->hasManyThrough(User::class, Following::class, "user_id", "id", "id", "following_id");
    }

    public function posts()
    {
        return $this->hasMany(Post::class, "author_id");
    }

    public function likes()
    {
        return $this->hasMany(Like::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }
}
