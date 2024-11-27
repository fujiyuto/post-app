<?php

namespace App\Listeners;

use App\Events\PostCreated;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Models\Post;
use App\Models\User;
use App\Exceptions\DataOperationException;

class UpdateUserVisitedNum
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(PostCreated $event): void
    {
        $restaurant_id_list = Post::where('user_id', $event->user->id)->pluck('restaurant_id');
        if ( !in_array($event->restaurant_id, $restaurant_id_list) ) {

            $event->user->visited_num++;

            if ( !$event->user->save() ) {
                throw new DataOperationException('ERROR: Exception occur in '.__LINE__.' lines of '.basename(__CLASS__));
            }
        }
    }
}
