<?php

namespace App\Listeners;

use App\Events\PostCreated;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Models\Restaurant;
use App\Exceptions\DataNotFoundException;
use App\Exceptions\DataOperationException;

class UpdatePointAvg
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
        // 店の平均点を再計算
        $restaurant = Restaurant::where('id', $$event->restaurant->id)->first();
        if ( !$restaurant ) {
            throw new DataNotFoundException('ERROR: Exception occur in '.__LINE__.' lines of '.basename(__CLASS__));
        }
        $restaurant->point_avg = round((($restaurant->point_avg * $restaurant->post_num) + $event->post->points) / ($restaurant->post_num + 1), 1);
        $restaurant->post_num++;

        // 店データを保存
        if ( !$restaurant->save() ) {
            throw new DataOperationException('ERROR: Exception occur in '.__LINE__.' lines of '.basename(__CLASS__));
        }
    }
}
