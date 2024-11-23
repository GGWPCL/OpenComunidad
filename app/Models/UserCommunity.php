<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserCommunity extends Model
{
    /** @use HasFactory<\Database\Factories\UserCommunityFactory> */
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'community_id',
        'is_admin',
        'is_manager',
        'is_neighbor',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_admin' => 'boolean',
        'is_manager' => 'boolean',
        'is_neighbor' => 'boolean',
    ];

    /**
     * Get the user that belongs to the community.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the community that the user belongs to.
     */
    public function community(): BelongsTo
    {
        return $this->belongsTo(Community::class);
    }
}
