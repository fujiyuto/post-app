<?php

namespace App\Http\Controllers;

use App\Models\Restaurant;
use Illuminate\Http\Request;
use App\Http\Requests\RestaurantCreateRequest;
use App\Http\Requests\RestaurantEditRequest;
use App\Http\Requests\RestaurantDeleteRequest;
use App\Services\RestaurantService;

class RestaurantController extends Controller
{
    private $restaurantService;

    public function __construct(RestaurantService $restaurantService)
    {
        $this->restaurantService = $restaurantService;
    }

    public function index(Request $request)
    {
        try {

            $data = $this->restaurantService->getRestaurants($request->query('genre_name', ''), $request->query('region', ''), $request->query('keyword', ''));

            return $this->responseJson($data);

        } catch (\Exception $e) {
            throw $e;
        }
    }

    public function show(Restaurant $restaurant)
    {
        try {

            $data = $this->restaurantService->getRestaurant($restaurant);

            return $this->responseJson($data);

        } catch (\Exception $e) {
            throw $e;
        }
    }

    public function create(RestaurantCreateRequest $request)
    {
        try {

            $data = $this->restaurantService->createRestaurant($request->restaurant_name, $request->zip_cd, $request->address, $request->email, $request->tel_no, $request->price_min, $request->price_max);

            return $this->responseJson($data);

        } catch (\Exception $e) {
            throw $e;
        }
    }

    public function edit(RestaurantEditRequest $request, Restaurant $restaurant)
    {
        try {

            $data = $this->restaurantService->updateRestaurant($restaurant, $request->restaurant_name, $request->zip_cd, $request->address, $request->email, $request->tel_no, $request->price_min, $request->price_max);

            return $this->responseJson($data);

        } catch (\Exception $e) {
            throw $e;
        }
    }

    public function delete(RestaurantDeleteRequest $request, Restaurant $restaurant)
    {
        try {

            $data = $this->restaurantService->deleteRestaurant($restaurant);

            return $this->responseJson($data);

        } catch (\Exception $e) {
            throw $e;
        }
    }
}
