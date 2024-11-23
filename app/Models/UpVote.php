<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UpVote extends Model
{
    /** @use HasFactory<\Database\Factories\UpVoteFactory> */
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'post_id',
    ];

    /**
     * Get the user that owns the up vote.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the post that is being up voted.
     */
    public function post(): BelongsTo
    {
        return $this->belongsTo(Post::class);
    }
}
