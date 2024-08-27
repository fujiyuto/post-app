<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserService {

    public function getUser(int $user_id)
    {

    }

    public function createUser(
        string $name,
        string $email,
        string $password,
        int    $gender,
        int    $user_type
    )
    {
        $insert_data = [
            'name'      => $name,
            'email'     => $email,
            'password'  => Hash::make($password),
            'gender'    => $gender,
            'user_type' => $user_type
        ];

        if ( ! User::create($insert_data) ) {
            // TODO
            // 作成エラー
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

    }

    public function logoutUser(int $user_id)
    {

    }
}
