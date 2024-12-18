<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Log;

class Community extends Model
{
    /** @use HasFactory<\Database\Factories\CommunityFactory> */
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'slug',
        'logo_id',
        'banner_id',
    ];

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    /**
     * Get the users that belong to the community.
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_communities')
            ->withPivot(['is_admin', 'is_manager', 'is_neighbor'])
            ->withTimestamps();
    }

    /**
     * Get the posts for the community.
     */
    public function posts(): HasMany
    {
        return $this->hasMany(Post::class);
    }

    /**
     * Get the admins of the community.
     */
    public function admins(): BelongsToMany
    {
        return $this->users()->wherePivot('is_admin', true);
    }

    /**
     * Get the managers of the community.
     */
    public function managers(): BelongsToMany
    {
        return $this->users()->wherePivot('is_manager', true);
    }

    /**
     * Get the neighbors of the community.
     */
    public function neighbors(): BelongsToMany
    {
        return $this->users()->wherePivot('is_neighbor', true);
    }

    /**
     * Get the logo of the community.
     */
    public function logo(): BelongsTo
    {
        return $this->belongsTo(File::class, 'logo_id');
    }

    /**
     * Get the banner of the community.
     */
    public function banner(): BelongsTo
    {
        return $this->belongsTo(File::class, 'banner_id');
    }
}
