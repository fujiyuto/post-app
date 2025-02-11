<?php

namespace App\Http\Controllers;

use App\Enums\Status;
use App\Http\Requests\ReservationCreateRequest;
use App\Http\Requests\ReservationEditRequest;
use App\Http\Requests\ReservationStatusEditRequest;
use App\Models\Reservation;
use App\Services\ReservationService;
use Illuminate\Http\Request;

class ReservationController extends Controller
{
    private $reservationService;

    public function __construct(ReservationService $reservationService)
    {
        $this->reservationService = $reservationService;
    }

    /**
     * 予約詳細データ取得
     *
     * @param Reservation $reservation
     * @return void
     */
    public function show(Reservation $reservation)
    {
        try {
            $response_data = $this->reservationService->getReservation($reservation->id);

            $response_data = [

            ];

            return $this->responseJson($response_data);
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * 予約データ作成
     *
     * @param  ReservationCreateRequest $request
     * @return void
     */
    public function create(ReservationCreateRequest $request)
    {
        try {
            $data = $this->reservationService->createReservation(
                $request->restaurant_id,
                $request->reserve_date,
                $request->reserve_time,
                $request->num_of_people,
                $request->notes
            );

            return $this->responseJson($data);
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * 予約データ更新
     *
     * @param ReservationEditRequest $request
     * @return void
     */
    public function edit(ReservationEditRequest $request, Reservation $reservation)
    {
        try {
            $data = $this->reservationService->editReservation($reservation, $request->reserve_date, $request->reserve_time, $request->num_of_people, $request->notes);

            return $this->responseJson($data);
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * 予約状況更新
     *
     * @param ReservationStatusEditRequest $request
     * @return void
     */
    public function edit_status(ReservationStatusEditRequest $request, Reservation $reservation)
    {
        try {
            $data = $this->reservationService->editReservationStatus($reservation, Status::{$request->status});

            return $this->responseJson($data);
        } catch (\Exception $e) {
            throw $e;
        }
    }
}
