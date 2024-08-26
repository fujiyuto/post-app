<?php

namespace App\Services;

class UserService {

    public function getUser(int $user_id)
    {

    }

    public function createUser(
        string $name,
        string $email,
        string $password,
        int $type,
        string $email_verifyed_at=null,
    )
    {

    }

    public function updateUser(
        string $name,
        string $email,
        string $password,
        int $type,
        string $email_verifyed_at=null
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
