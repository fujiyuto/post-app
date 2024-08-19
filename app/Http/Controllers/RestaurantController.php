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

    public function index()
    {

    }

    public function show(Restaurant $restaurant)
    {

    }

    public function create(RestaurantCreateRequest $request)
    {

    }

    public function edit(RestaurantEditRequest $request, Restaurant $restaurant)
    {

    }

    public function delete(RestaurantDeleteRequest $request, Restaurant $restaurant)
    {

    }
}
