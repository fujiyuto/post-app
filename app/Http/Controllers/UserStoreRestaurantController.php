<?php

namespace App\Http\Controllers;

use App\Models\UserStoreRestaurant;
use App\Services\UserStoreRestaurantService;
use Illuminate\Http\Request;
use App\Models\User;
use App\Http\Requests\UserStoreRestaurantCreateRequest;
use App\Http\Requests\UserStoreRestaurantDeleteRequest;
use Illuminate\Support\Facades\Auth;

class UserStoreRestaurantController extends Controller
{
    private $userStoreRestaurantService;

    public function __construct(UserStoreRestaurantService $userStoreRestaurantService)
    {
        $this->userStoreRestaurantService = $userStoreRestaurantService;
    }

    public function index(User $user)
    {
        try {

            $data = $this->userStoreRestaurantService->getUserStoreRestaurant($user);

            return $this->responseJson($data);

        } catch (\Exception $e) {
            throw $e;
        }
    }

    public function create(UserStoreRestaurantCreateRequest $request)
    {
        try {

            $data = $this->userStoreRestaurantService->createUserStoreRestaurant(Auth::id(), $request->restaurant_id);

            return $this->responseJson($data);

        } catch (\Exception $e) {
            throw $e;
        }
    }

    public function delete(UserStoreRestaurantDeleteRequest $request)
    {
        try {

            $data = $this->userStoreRestaurantService->deleteUserStoreRestaurant(Auth::id(), $request->restaurant_id);

            return $this->responseJson($data);

        } catch (\Exception $e) {
            throw $e;
        }
    }

}
