<?php

namespace App\Http\Controllers;

use App\Http\Requests\EditEmailRequest;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Requests\UserCreateRequest;
use App\Http\Requests\UserEditRequest;
use App\Http\Requests\UserDeleteRequest;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\LogoutRequest;
use App\Services\UserService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    private $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function show(User $user)
    {
        try {

            $data = $this->userService->getUser($user);

            return $this->responseJson($data);

        } catch (\Exception $e) {
            throw $e;
        }
    }

    public function create(UserCreateRequest $request)
    {
        try {

            $data = $this->userService->createUser(
                $request->user_name,
                $request->email,
                $request->password,
                $request->gender,
                $request->user_type,
            );

            return $this->responseJson($data);

        } catch (\Exception $e) {
            throw $e;
        }
    }

    public function edit(UserEditRequest $request, User $user)
    {
        try {

            $data = $this->userService->updateUser($user, $request->user_name, $request->gender);

            return $this->responseJson($data);

        } catch (\Exception $e) {
            throw $e;
        }
    }

    public function delete(UserDeleteRequest $request, User $user)
    {
        try {

            $data = $this->userService->deleteUser($user);

            return $this->responseJson($data);

        } catch (\Exception $e) {
            throw $e;
        }
    }

    public function login(LoginRequest $request)
    {
        try {

            $data = $this->userService->loginUser($request->email, $request->password);

            // セッション再生成
            $request->session()->regenerate();

            return response()->json($data);

        } catch (\Exception $e) {
            throw $e;
        }
    }

    public function logout(LogoutRequest $request)
    {
        try {

            $data = $this->userService->logoutUser();

            return $this->responseJson($data);

        } catch (\Exception $e) {
            throw $e;
        }
    }

    public function emailLink()
    {
        try {

            $data = $this->userService->sendEditEmail(Auth::user());

            return $this->responseJson($data);

        } catch (\Exception $e) {
            throw $e;
        }
    }

    public function checkEmailToken(Request $request)
    {
        try {

            $data = $this->userService->checkToken($request->token, Auth::id());

            return $this->responseJson($data);

        } catch (\Exception $e) {
            throw $e;
        }
    }

    public function editEmail(EditEmailRequest $request)
    {
        try {

            $data = $this->userService->updateEmail($request->email, Auth::user());

            return $this->responseJson($data);

        } catch (\Exception $e) {
            throw $e;
        }
    }
}
