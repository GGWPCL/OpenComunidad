<?php

namespace App\Events;

use App\Models\Post;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PostUpdated
{
    use Dispatchable, SerializesModels;

    public const TYPE_POST_COMMENTED = 'POST_COMMENTED';

    public function __construct(
        public readonly string $type,
        public readonly Post $post
    ) {
        //
    }
}
