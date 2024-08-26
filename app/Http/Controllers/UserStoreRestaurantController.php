<?php

namespace App\Http\Controllers;

use App\Models\UserStoreRestaurant;
use App\Services\UserStoreRestaurantService;
use Illuminate\Http\Request;

class UserStoreRestaurantController extends Controller
{
    private $userStoreRestaurantService;

    public function __construct(UserStoreRestaurantService $userStoreRestaurantService)
    {
        $this->userStoreRestaurantService = $userStoreRestaurantService;
    }

    public function index()
    {

    }

    public function create()
    {

    }

    public function delete()
    {

    }

}
