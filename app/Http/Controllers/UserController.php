<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Requests\UserCreateRequest;
use App\Http\Requests\UserEditRequest;
use App\Http\Requests\UserDeleteRequest;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\LogoutRequest;
use App\Services\UserService;

class UserController extends Controller
{
    private $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function show(User $user)
    {

    }

    public function create(UserCreateRequest $request)
    {

    }

    public function edit(UserEditRequest $request, User $user)
    {

    }

    public function delete(UserDeleteRequest $request, User $user)
    {

    }

    public function login(LoginRequest $request)
    {

    }

    public function logout(LogoutRequest $request)
    {

    }
}
