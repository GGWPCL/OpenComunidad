<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    public const INTERNAL_NAME_PROPOSALS = 'proposals';
    public const INTERNAL_NAME_POLLS = 'polls';
    public const INTERNAL_NAME_IMAGINE = 'imagine';

    /** @use HasFactory<\Database\Factories\CategoryFactory> */
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'display_name',
        'internal_name',
        'icon',
    ];

    /**
     * Get the posts that belong to this category.
     */
    public function posts(): HasMany
    {
        return $this->hasMany(Post::class);
    }
}
