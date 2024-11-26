<?php

namespace App\Listeners;

use App\Events\PostUpdated;
use App\Models\User;
use App\Notifications\PostCommented;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendPostUpdateNotification implements ShouldQueue
{
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(PostUpdated $event): void
    {
        $this->sendPostUpdateNotification($event);
    }

    public function sendPostUpdateNotification(PostUpdated $event): void
    {
        $event->post->followers()->each(function (User $user) use ($event) {
            if ($event->type === PostUpdated::TYPE_POST_COMMENTED) {
                $user->notify(new PostCommented($event->post));
            }
        });
    }
}
