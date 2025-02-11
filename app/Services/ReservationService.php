<?php

namespace App\Services;

use App\Exceptions\DataNotFoundException;
use App\Exceptions\DataOperationException;
use App\Models\Reservation;
use App\Enums\Status;
use Illuminate\Support\Str;

class ReservationService
{

    /**
     * 予約IDを受け取り、予約の詳細情報を返す
     *
     * @param integer $reservation_id
     * @return void
     */
    public function getReservation(int $reservation_id)
    {
        $reservation = Reservation::selectRaw(
            'reservation.id as reservation_id,
             reservation.reserve_date,
             reservation.num_of_people,
             reservation.status,
             reservation.notes,
             time_slots.hour as reserve_hour,
             time_slots.minute as reserve_minute,
             restaurants.restaurant_name
             users.user_name
            ')
            ->join('users', 'reservations.reserved_by', '=', 'users.id')
            ->join('restaurants', 'reservations.restaurant_id', '=', 'restaurants.id')
            ->join('time_slots', 'reservations.time_slot_id', '=', 'time_slots.id')
            ->where('reservations.id', $reservation_id)
            ->get();

        if ( $reservation->isEmpty() ) {
            throw new DataNotFoundException('ERROR: Exception occur in '.__LINE__.' lines of '.basename(__CLASS__));
        }

        $response_data = [
            'reservation_id'  => $reservation->reservation_id,
            'restaurant_name' => $reservation->restaurant_name,
            'reserved_by'     => $reservation->use_name,
            'reserve_date'    => $reservation->reserve_date,
            'reserve_hour'    => $reservation->reserve_hour,
            'reserve_minute'  => $reservation->reserve_minute,
            'reserve_time'    => Str::padLeft((string)$reservation->reserve_hour, 2, '0') . Str::padLeft((string)$reservation->reserve_minute, 2, '0'),
            'num_of_people'   => $reservation->num_of_people,
            'status'          => Status::{$reservation->status}->value,
            'notes'           => $reservation->notes
        ];

        return $response_data;
    }

    /**
     * 予約データ作成
     *
     * @param integer $restaurant_id
     * @param string $reserve_date
     * @param string $reserve_time
     * @param integer $num_of_people
     * @param string|null $notes
     * @return array
     */
    public function createReservation(
        int $restaurant_id,
        string $reserve_date,
        string $reserve_time,
        int $num_of_people,
        string|null $notes
    ) {
        // 作成予約データ（一旦全てuser_id=1のデータで作成）
        $insert_data = [
            'reserved_by'   => 1,
            'restaurant_id' => $restaurant_id,
            'reserve_date'  => $reserve_date,
            'reserve_time'  => $reserve_time,
            'num_of_people' => $num_of_people,
            'notes'         => $notes
        ];

        $is_created = Reservation::create($insert_data);

        // DB登録
        if ( !$is_created ) {
            throw new DataOperationException('ERROR: Exception occur in '.__LINE__.' lines of '.basename(__CLASS__));
        }

        return ['message' => '予約データが作成されました。'];
    }

    /**
     * 予約データ更新
     *
     * @param  Reservation $reservation
     * @param  string      $reserve_date
     * @param  string      $reserve_time
     * @param  integer     $num_of_people
     * @param  string|null $notes
     * @return array
     */
    public function editReservation(
        Reservation $reservation,
        string $reserve_date,
        string $reserve_time,
        int $num_of_people,
        string|null $notes
    ) {
        // 更新
        $reservation->reserve_date = $reserve_date;
        $reservation->reserve_time = $reserve_time;
        $reservation->num_of_people = $num_of_people;
        if ( $notes ) {
            $reservation->notes = $notes;
        }

        // 予約データ更新
        if ( !$reservation->save() ) {
            throw new DataOperationException('ERROR: Exception occur in '.__LINE__.' lines of '.basename(__CLASS__));
        }

        // レスポンスデータ
        $response_data = [
            'reserve_date'  => $reservation->reserve_date,
            'reserve_time'  => $reservation->reserve_time,
            'num_of_people' => $reservation->num_of_people,
            'notes'         => $reservation->notes
        ];

        return $response_data;
    }

    /**
     * 予約状況更新
     *
     * @param  Status $status
     * @return array
     */
    public function editReservationStatus(Reservation $reservation, Status $status)
    {
        // 更新
        $reservation->status = $status->name;

        // DBへ反映
        if ( !$reservation->save() ) {
            throw new DataOperationException('ERROR: Exception occur in '.__LINE__.' lines of '.basename(__CLASS__));
        }

        // レスポンスデータ
        $response_data = [
            'status' => [
                'label' => $status->value,
                'value' => $status->name
            ]
        ];

        return $response_data;
    }
}
