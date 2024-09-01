<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Exceptions\DataOperationException;

class UserService {

    public function getUser(int $user_id)
    {

    }

    public function createUser(
        string $user_name,
        string $email,
        string $password,
        int    $gender,
        int    $user_type
    )
    {
        $insert_data = [
            'user_name' => $user_name,
            'email'     => $email,
            'password'  => Hash::make($password),
            'gender'    => $gender,
            'user_type' => $user_type
        ];

        if ( ! User::create($insert_data) ) {
            // TODO
            throw new DataOperationException('ユーザー登録エラー');
        }

        return [
            'data' => [
                'ok' => true
            ]
        ];
    }

    public function updateUser(
        string $name,
        string $email,
        string $password,
        int    $gender,
        int    $user_type
    )
    {

    }

    public function deleteUser(int $user_id)
    {

    }

    public function loginUser(string $email, string $password)
    {
        $credentials = [
            'email' => $email,
            'password' => $password
        ];

        if ( ! Auth::attempt($credentials) ) {
            // TODO
            // 認証エラー
        }

        return [
            'data' => [
                'ok' => true
            ]
        ];
    }

    public function logoutUser(int $user_id)
    {

    }
}
