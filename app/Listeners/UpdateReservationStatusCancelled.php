<?php

namespace App\Listeners;

use App\Events\ReservationStatusCancelled;
use App\Models\RestaurantReservationStatus;
use App\Models\Restaurant;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class UpdateReservationStatusCancelled
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
    public function handle(ReservationStatusCancelled $event): bool
    {
        //店の予約状況取得
        $where = [
            'restaurant_id' => $event->reservation->restaurant_id,
            'reserve_date'  => $event->reservation->reserve_date,
            'is_reservable' => RestaurantReservationStatus::IS_RESERVABLE
        ];
        $restaurant_reservation_status = RestaurantReservationStatus::selectRaw('id, reserve_status_info, reserve_date')
                                        ->where($where)
                                        ->get();
        // 店予約状況データ取得エラー
        if (!$restaurant_reservation_status) {
            $context = [
                'restaurant_reservation_status_id' => $restaurant_reservation_status->id,
            ];
            Log::debug('店予約状況データの取得に失敗しました。予約状況ID:{restaurant_reservation_status_id}', $context);
            return false;
        }

        // 予約された店を取得
        $restaurant = Restaurant::selectRaw('restaurant_name, seating_duration')
                                ->where('id', $event->reservation->restaurant_id)
                                ->first();
        // 店データ取得エラー
        if (!$restaurant) {
            $context = [
                'restaurant_id' => $event->reservation->restaurant_id,
            ];
            Log::debug('店データの取得に失敗しました。店ID:{restaurant_id}', $context);
            return false;
        }

        // 予約状況データ
        $reservation_status_data = json_decode($restaurant_reservation_status->reserve_status_info, true);

        // ループ回数
        $loop_count = (int)($restaurant->seating_duration / 0.25);
        $reserve_time = $$event->reservation->reserve_time;

        for ($i = 0; $i < $loop_count; $i++) {
            $reservation_time_info = $reservation_status_data[$reserve_time];

            // 予約人数を減算
            $reservation_time_info['num_of_people'] -= $event->reservation->num_of_people;

            // 予約可能フラグがfalseの場合はtrueにする
            $reservation_time_info['available'] = $reservation_time_info['available'] ?? true;

            // 予約状況データに反映
            $reservation_status_data[$reserve_time] = $reservation_time_info;

            // 次のループのために時間を15分加算
            list($reserve_hour, $reserve_minute) = explode(':', $event->reservation->reserve_time);
            $reserve_hour = (int)$reserve_hour;
            $reserve_minute = (int)$reserve_minute;
            $reserve_minute += 15;

            // 15分加算した時に60になった場合は、時間に1を加算し、分を0にする
            if ($reserve_minute === 60) {
                $reserve_minute = 0;
                $reserve_hour++;
            }

            //時間と分を文字列化
            $reserve_time = Str::padLeft((string)$reserve_hour, 2, '0') . ':' . Str::padLeft((string)$reserve_minute, 2, '0');
        }

        // 予約不可に設定されていた場合は予約可能に変更
        if ( $restaurant_reservation_status->is_reservable === RestaurantReservationStatus::IS_NOT_RESERVABLE ) {
            $restaurant_reservation_status->is_reservable = RestaurantReservationStatus::IS_RESERVABLE;
        }

        // 予約状況更新
        $restaurant_reservation_status->reserve_status_info = $reservation_status_data;
        if ($restaurant_reservation_status->save()) {
            // DBエラー
            $context = [
                'restaurant_reservation_status_id' => $restaurant_reservation_status->id
            ];
            Log::debug('予約状況の更新に失敗しました。予約状況ID:{restaurant_reservation_status_id}', $context);
            return false;
        }

        return true;
    }
}
