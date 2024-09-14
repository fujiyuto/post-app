<?php

namespace App\Services;

use App\Models\User;
use App\Models\Follow;
use App\Models\PasswordToken;
use App\Models\Post;
use App\Models\EmailToken;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Exceptions\DataOperationException;
use App\Exceptions\UnauthorizationException;
use App\Exceptions\AuthenticateException;
use App\Exceptions\DataNotFoundException;
use App\Exceptions\SendEmailException;
use App\Mail\EditEmailAddressMail;
use App\Mail\ResetPasswordMail;
use Illuminate\Support\Facades\Gate;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;


class UserService
{
    public function getUser(User $user)
    {
        $response_data = [];

        $response_data['user'] = [
            'user_name'         => $user->user_name,
            'email'             => $user->email,
            'gender'            => $user->gender,
            'user_type'         => $user->user_type
        ];

        // フォロワー数
        $follower_count = Follow::where('follower_id', $user->id)->count();
        // フォロー数
        $follow_count   = Follow::where('follow_id', $user->id)->count();
        $response_data['follow'] = [
            'follower_count' => $follower_count,
            'follow_count'   => $follow_count
        ];

        // 店の訪問数を集計
        $visited_num = Post::where('user_id', $user->id)
                            ->distinct('restaurant_id')
                            ->count('restaurant_id');
        $response_data['visited_count'] = $visited_num;

        // ユーザーの投稿一覧取得
        $posts = Post::join('restaurants', 'posts.restaurant_id', '=', 'restaurants.id')
                                ->orderByDesc('posts.id')
                                ->get();
        if ( $posts->isEmpty() ) {
            $response_data['posts'] = [];
        } else {
            foreach ($posts as $post) {
                $created_datetime = new Carbon($post->created_at);
                $created_date     = $created_datetime->format('Y-m-d');
                $response_data['posts'][]  = [
                    'id'              => $post->id,
                    'restaurant_id'   => $post->restaurant_id,
                    'restaurant_name' => $post->restaurant_name,
                    'title'           => $post->title,
                    'content'         => $post->content,
                    'visited_at'      => $post->visited_at,
                    'period_of_time'  => $post->period_of_time,
                    'points'          => $post->points,
                    'price_min'       => $post->price_min,
                    'price_max'       => $post->price_max,
                    'image_url1'      => $post->image_url1,
                    'image_url2'      => $post->image_url2,
                    'image_url3'      => $post->image_url3,
                    'created_at'      => $created_date
                ];
            }
        }

        return [
            'data' => $response_data
        ];
    }

    public function createUser(
        string $user_name,
        string $email,
        string $password,
        int    $gender,
        int    $user_type
    ) {
        $insert_data = [
            'user_name' => $user_name,
            'email'     => $email,
            'password'  => Hash::make($password),
            'gender'    => $gender,
            'user_type' => $user_type
        ];

        if (! User::create($insert_data)) {
            throw new DataOperationException('ERROR: Exception occur in '.__LINE__.' lines of '.basename(__CLASS__));
        }

        return [
            'data' => [
                'ok' => true
            ]
        ];
    }

    public function updateUser(
        User   $user,
        string $user_name,
        int    $gender
    ) {
        // ユーザーチェック
        $check = Gate::inspect('update', $user);
        if ($check->denied()) {
            throw new UnauthorizationException('ERROR: Exception occur in '.__LINE__.' lines of '.basename(__CLASS__));
        }

        $user->user_name = $user_name;
        $user->gender    = $gender;

        if (!$user->save()) {
            throw new DataOperationException('ERROR: Exception occur in '.__LINE__.' lines of '.basename(__CLASS__));
        }

        return [
            'data' => [
                'ok' => true
            ]
        ];
    }

    public function deleteUser(User $user)
    {
        // ユーザーチェック
        $check = Gate::inspect('delete', $user);
        if ($check->denied()) {
            throw new UnauthorizationException('ERROR: Exception occur in '.__LINE__.' lines of '.basename(__CLASS__));
        }

        if (!$user->delete()) {
            throw new DataOperationException('ERROR: Exception occur in '.__LINE__.' lines of '.basename(__CLASS__));
        }

        return [
            'data' => [
                'ok' => true
            ]
        ];
    }

    public function loginUser(string $email, string $password)
    {
        $credentials = [
            'email' => $email,
            'password' => $password
        ];

        if (! Auth::attempt($credentials)) {
            // TODO
            throw new AuthenticateException('ERROR: Exception occur in '.__LINE__.' lines of '.basename(__CLASS__));
        }

        return [
            'data' => [
                'ok' => true
            ]
        ];
    }

    public function logoutUser()
    {
        Auth::logout();

        return [
            'data' => [
                'ok' => true
            ]
        ];
    }

    public function sendEditEmail(User $user)
    {
        // 有効なトークンを無効
        EmailToken::where([
                        'user_id' => $user->id,
                        'status'  => EmailToken::EMAIL_TOKEN_VALID
                    ])
                    ->update(['status'  => EmailToken::EMAIL_TOKEN_INVALID]);

        // 新たなトークンを発行
        $token = Str::random(60);
        $insert_data = [
            'user_id' => $user->id,
            'token'   => $token
        ];
        if ( !EmailToken::create($insert_data) ){
            throw new DataOperationException('ERROR: Exception occur in '.__LINE__.' lines of '.basename(__CLASS__));
        }

        // TODO
        // メールに記載するリセットリンク
        $email_reset_link = "http://localhost:8573/api/email/token?token={$token}";

        // リンク有効時間
        $expire_time = $this->getExpireTime(env('MAIL_UPDATE_EXPIRE_TIME', 1440));

        $mail_content = [
            'user_name'        => $user->user_name,
            'email_reset_link' => $email_reset_link,
            'expire_time'      => $expire_time
        ];

        try {
            Mail::to($user->email)->send(new EditEmailAddressMail($mail_content));
        } catch (\Exception $e) {
            throw new SendEmailException('ERROR: Exception occur in '.__LINE__.' lines of '.basename(__CLASS__));
        }

        return [
            'data' => [
                'ok' => true
            ]
        ];
    }

    public function updateEmail(string $token, string $new_email, User $user)
    {
        if ( !$this->checkEmailToken($token, $user->id) ) {
            throw new DataOperationException('ERROR: Exception occur in '.__LINE__.' lines of '.basename(__CLASS__));
        }

        $user->email = $new_email;

        if ( !$user->save() ) {
            throw new DataOperationException('ERROR: Exception occur in '.__LINE__.' lines of '.basename(__CLASS__));
        }

        // メールアドレス変更を行なったユーザーのトークンを無効
        EmailToken::where([
                        'user_id' => $user->id,
                        'status'  => EmailToken::EMAIL_TOKEN_VALID
                    ])
                    ->update(['status'  => EmailToken::EMAIL_TOKEN_INVALID]);


        return [
            'data' => [
                'ok' => true
            ]
        ];
    }

    public function sendResetPwd(User $user)
    {
        // 有効なトークンを無効
        PasswordToken::where([
                            'user_id' => $user->id,
                            'status' => PasswordToken::PASSWORD_TOKEN_VALID
                        ])
                        ->update(['status' => PasswordToken::PASSWORD_TOKEN_INVALID]);

        // 新たなトークンを発行
        $token = Str::random(60);
        $insert_data = [
            'user_id' => $user->id,
            'token'   => $token
        ];
        if ( !PasswordToken::create($insert_data) ){
            throw new DataOperationException('ERROR: Exception occur in '.__LINE__.' lines of '.basename(__CLASS__));
        }

        // TODO
        // メールに記載するリセットリンク
        $pwd_reset_link = "http://localhost:8573/api/pwd/token?token={$token}";

        // リンク有効時間
        $expire_time = $this->getExpireTime(env('PASSWORD_UPDATE_EXPIRE_TIME', 10));

        $mail_content = [
            'user_name'      => $user->user_name,
            'pwd_reset_link' => $pwd_reset_link,
            'expire_time'    => $expire_time
        ];

        try {
            Mail::to($user->email)->send(new ResetPasswordMail($mail_content));
        } catch (\Exception $e) {
            throw new SendEmailException('ERROR: Exception occur in '.__LINE__.' lines of '.basename(__CLASS__));
        }

        return [
            'data' => [
                'ok' => true
            ]
        ];
    }

    public function resetPwd(string $token, string $new_password, User $user)
    {
        if ( !$this->checkPwdToken($token, $user->id) ) {
            throw new DataOperationException('ERROR: Exception occur in '.__LINE__.' lines of '.basename(__CLASS__));
        }

        $hash_new_password = Hash::make($new_password);
        if ( $hash_new_password === $user->password ) {
            throw new DataOperationException('パスワードが変更されていません');
        }

        $user->password = $hash_new_password;
        if ( !$user->save() ) {
            throw new DataOperationException('ERROR: Exception occur in '.__LINE__.' lines of '.basename(__CLASS__));
        }

        // パスワードリセットを行なったユーザーのトークンを無効
        PasswordToken::where([
            'user_id' => $user->id,
            'status'  => PasswordToken::PASSWORD_TOKEN_VALID
        ])
        ->update(['status'  => PasswordToken::PASSWORD_TOKEN_INVALID]);

        return [
            'data' => [
                'ok' => true
            ]
        ];
    }

    private function checkEmailToken(string $token, int $user_id)
    {
        $email_token = EmailToken::where([
                                        'user_id' => $user_id,
                                        'token'   => $token,
                                        'status'  => EmailToken::EMAIL_TOKEN_VALID
                                    ])
                                    ->first();
        if ( !$email_token ) {
            throw new DataNotFoundException('ERROR: Exception occur in '.__LINE__.' lines of '.basename(__CLASS__));
        }

        $now = Carbon::now();
        $created_at = new Carbon($email_token->created_at);

        return $created_at->diffInMinutes($now) <= env('MAIL_UPDATE_EXPIRE_TIME');
    }

    private function checkPwdToken(string $token, int $user_id)
    {
        $pwd_token = PasswordToken::where([
            'user_id' => $user_id,
            'token'   => $token,
            'status'  => PasswordToken::PASSWORD_TOKEN_VALID
        ])
        ->first();
        if ( !$pwd_token ) {
            throw new DataNotFoundException('ERROR: Exception occur in '.__LINE__.' lines of '.basename(__CLASS__));
        }

        $now = Carbon::now();
        $created_at = new Carbon($pwd_token->created_at);

        return $created_at->diffInMinutes($now) <= env('PASSWORD_UPDATE_EXPIRE_TIME');
    }

    private function getExpireTime(int $time): string
    {
        if ( floor($time / 60) > 0 ) {
            $hour   = floor($time / 60) . '時間';
            $minute = $time % 60 == 0 ? '' : $time % 60 . '分';
            $expire_time = $hour . $minute;
        } else {
            $expire_time = $time % 60 . '分';
        }

        return $expire_time;
    }
}
