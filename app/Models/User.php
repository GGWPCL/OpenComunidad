<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Grosv\LaravelPasswordlessLogin\Traits\PasswordlessLogin;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, PasswordlessLogin;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

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
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Get the communities that the user belongs to.
     */
    public function communities(): BelongsToMany
    {
        return $this->belongsToMany(Community::class, 'user_communities')
            ->withPivot('is_admin', 'is_manager', 'is_neighbor')
            ->withTimestamps();
    }

    /**
     * Get the posts that the user follows.
     */
    public function followedPosts(): BelongsToMany
    {
        return $this->belongsToMany(Post::class, 'follows')
            ->withTimestamps();
    }

    /**
     * @param Post $post
     * @param bool $shouldFollow
     * @return Follow|null
     */
    public function followPost(Post $post, bool $shouldFollow): ?Follow
    {
        if ($shouldFollow) {
            if ($this->followedPosts->contains($post)) {
                return Follow::where('user_id', $this->id)
                    ->where('post_id', $post->id)
                    ->first();
            }

            $follow = Follow::create([
                'user_id' => $this->id,
                'post_id' => $post->id
            ]);

            // Refresh the relationship
            $this->load('followedPosts');

            return $follow;
        }

        if ($this->followedPosts->contains($post)) {
            // Delete existing follow relationship
            Follow::where('user_id', $this->id)
                ->where('post_id', $post->id)
                ->delete();

            // Refresh the relationship
            $this->load('followedPosts');
        }

        return null;
    }

    /**
     * Get the posts authored by the user.
     */
    public function posts(): HasMany
    {
        return $this->hasMany(Post::class, 'author_id');
    }

    /**
     * Get the comments authored by the user.
     */
    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class, 'author_id');
    }
}
