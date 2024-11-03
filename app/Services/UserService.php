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
use App\Mail\ConfirmEmailMail;
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
            'user_name'    => $user->user_name,
            'email'        => $user->email,
            'tel_no'       => $user->tel_no,
            'birthday'     => $user->birthday,
            'gender'       => User::USER_GENDER_MAP[$user->gender],
            'user_type'    => User::USER_TYPE_MAP[$user->user_type],
            'post_num'     => $user->post_num,
            'follower_num' => $user->follower_num,
            'follow_num'   => $user->follow_num,
            'visited_num'  => $user->visited_num,
        ];

        // ユーザーの投稿一覧取得
        $posts = Post::join('restaurants', 'posts.restaurant_id', '=', 'restaurants.id')
        ->where('user_id', $user->id)
        ->orderByDesc('posts.id')
        ->get();
        if ( $posts->isEmpty() ) {
            $response_data['posts'] = [];
        } else {
            foreach ($posts as $post) {
                $created_datetime = new Carbon($post->created_at);
                $created_date     = $created_datetime->format('Y-m-d');

                $response_data['posts'][] = [
                    'id'              => $post->id,
                    'restaurant_id'   => $post->restaurant_id,
                    'restaurant_name' => $post->restaurant_name,
                    'title'           => $post->title,
                    'visited_at'      => $post->visited_at,
                    'period_of_time'  => $post->period_of_time,
                    'points'          => $post->points,
                    'price_min'       => $post->price_min,
                    'price_max'       => $post->price_max,
                    'image_url'       => $post->image_url1,
                    'created_at'      => $created_date
                ];
            }
        }

        return  $response_data;
    }

    public function createUser(
        string $user_name,
        string $email,
        string $password,
        string $tel_no,
        string $birthday,
        int    $gender,
        int    $user_type
    ) {
        $insert_data = [
            'user_name' => $user_name,
            'email'     => $email,
            'password'  => Hash::make($password),
            'tel_no'    => $tel_no,
            'birthday'  => $birthday,
            'gender'    => $gender,
            'user_type' => $user_type
        ];

        if (! User::create($insert_data)) {
            throw new DataOperationException('ERROR: Exception occur in '.__LINE__.' lines of '.basename(__CLASS__));
        }

        return [
            'ok' => true
        ];
    }

    public function updateUser(
        User   $user,
        string $user_name,
        int    $gender,
        string $tel_no,
        string $birthday
    ) {
        // ユーザーチェック
        $check = Gate::inspect('update', $user);
        if ($check->denied()) {
            throw new UnauthorizationException('ERROR: Exception occur in '.__LINE__.' lines of '.basename(__CLASS__));
        }

        $user->user_name = $user_name;
        $user->gender    = $gender;
        $user->tel_no    = $tel_no;
        $user->birthday  = $birthday;

        if (!$user->save()) {
            throw new DataOperationException('ERROR: Exception occur in '.__LINE__.' lines of '.basename(__CLASS__));
        }

        return [
            'ok' => true
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
            'ok' => true
        ];
    }

    public function loginUser(string $email, string $password)
    {
        $credentials = [
            'email' => $email,
            'password' => $password
        ];

        if (! Auth::attempt($credentials)) {
            throw new AuthenticateException('ERROR: Exception occur in '.__LINE__.' lines of '.basename(__CLASS__));
        }

        return [
            'id'        => Auth::id(),
            'user_name' => Auth::user()->user_name
        ];
    }

    public function logoutUser()
    {
        Auth::logout();

        return [
            'ok' => true
        ];
    }

    public function sendEditEmailLink(User|null $user)
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
        $email_reset_link = "http://localhost:3300/users/forget/{$token}";

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
            'ok' => true
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
            'ok' => true
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
            'ok' => true
        ];
    }

    public function emailConfirm(string $tel_no, string $birthday)
    {
        $user = User::where([
                        'tel_no' => $tel_no,
                        'birthday' => $birthday
                    ])->first();

        if ( !$user ) {
            throw new DataNotFoundException('ERROR: Exception occur in '.__LINE__.' lines of '.basename(__CLASS__));
        }

        $mail_content = [
            'user_name' => $user->user_name
        ];

        try {
            Mail::to($user->email)->send(new ConfirmEmailMail($mail_content));
        } catch (\Exception $e) {
            throw new SendEmailException('ERROR: Exception occur in '.__LINE__.' lines of '.basename(__CLASS__));
        }

        return [
            'ok' => true
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
            'ok' => true
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
