<?php

namespace App\Services;

use App\Exceptions\DataNotFoundException;
use App\Exceptions\DataOperationException;
use App\Models\Reservation;
use App\Enums\Status;
use App\Events\ReservationCreated;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use App\Events\ReservationStatusCancelled;

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

        //===== トランザクション処理開始 =====//
        DB::beginTransaction();

        $created_reservation = Reservation::create($insert_data);

        // DB登録
        if ( !$created_reservation ) {
            // ロールバック
            DB::rollback();
            throw new DataOperationException('ERROR: Exception occur in '.__LINE__.' lines of '.basename(__CLASS__));
        }

        // 予約状況データ更新イベント
        $event_ok = event(new ReservationCreated($created_reservation));
        if (!$event_ok) {
            DB::rollback();
            throw new DataOperationException('予約ができませんでした');
        }

        //===== トランザクションコミット =====//
        DB::commit();

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

        //===== トランザクション処理 =====//
        DB::beginTransaction();

        // DBへ反映
        if ( !$reservation->save() ) {
            DB::rollBack();
            $context = [
                'reservation_id'     => $reservation->id,
                'reservation_status' => $status->name
            ];
            Log::debug("予約状況の更新に失敗しました。予約ID:{reservation_id}, 予約ステータス:{reservation_status}");
            throw new DataOperationException('ERROR: Exception occur in '.__LINE__.' lines of '.basename(__CLASS__));
        }

        // 予約ステータスがキャンセルに変更の場合のイベント
        if ( $reservation->status === Reservation::STATUS_CANCELLED ) {
            // 店予約状況データでキャンセルが出た分の人数を削除
            $event_ok = event(new ReservationStatusCancelled($reservation));

            // 更新できなかった場合はエラー
            if ( !$event_ok ) {
                DB::rollBack();
                throw new DataOperationException('ERROR: Exception occur in '.__LINE__.' lines of '.basename(__CLASS__));
            }
        }

        //===== トランザクションコミット  =====//
        DB::commit();

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
