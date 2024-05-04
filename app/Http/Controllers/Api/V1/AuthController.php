<?php

namespace App\Http\Controllers\Api\V1;

use App\Helpers\Common;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\ForgotRequest;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Requests\Auth\ResetRequest;
use App\Jobs\SendForgotPasswordEmail;
use App\Mail\ForgotPassword;
use App\Mail\ResetPasswordMail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Redis;
use Laravel\Socialite\Facades\Socialite;
use Nette\Utils\Random;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    // Function for sign up
    public function signUp(RegisterRequest $request)
    {
        $user = User::create($request->validated());

        if ($user) {
            $credentials = $request->only('email', 'password');
            if (Auth::attempt($credentials)) {
                $user = Auth::user();
                $token = $user->createToken('AccessToken')->accessToken;
                $user['access_token'] = $token;
                return Common::response(200, 'Tạo tài khoản thành công.', $user, null, 'access_token', $token);
            }
        }

        return Common::response(500, 'Có lỗi xảy ra, vui lòng thử lại.');
    }

    public function signIn(LoginRequest $request)
    {
        if (Auth::attempt($request->validated())) {
            $user = Auth::user();
            $token = $user->createToken('AccessToken')->accessToken;
            $user['access_token'] = $token;
            return Common::response(200, 'Đăng nhập thành công.', Auth::user());
        } else {
            return Common::response(500, 'Xác thực thất bại, vui lòng kiểm tra lại email và mật khẩu.');
        }
    }

    public function googleSignIn()
    {
        try {
            $url = Socialite::driver('google')->stateless()
                ->redirect()->getTargetUrl();
            return response()->json([
                'url' => $url,
            ])->setStatusCode(Response::HTTP_OK);
        } catch (\Exception $exception) {
            return $exception;
        }
    }

    public function handleGoogleSignIn(Request $request)
    {
        try {
            $state = $request->input('state');

            parse_str($state, $result);
            $googleUser = Socialite::driver('google')->stateless()->user();

            $user = User::where('email', $googleUser->email)->first();
            if ($user) {
                // Gọi lại account cũ
                Auth::login($user);
                $user = Auth::user();
                $token = $user->createToken('AccessToken')->accessToken;
                $user['access_token'] = $token;
                return Common::response(200, "Đăng nhập thành công.", $user, null, 'first', true);
            }
            // Tạo account mới
            $user = User::create(
                [
                    'email' => $googleUser->email,
                    'name' => $googleUser->name,
                    'google_id' => $googleUser->id,
                    'username' => Random::generate(21),
                    'avatar' => $googleUser->getAvatar(),
                    'password' => Random::generate(15),
                ]
            );
            Auth::login($user);
            $user = Auth::user();
            $token = $user->createToken('AccessToken')->accessToken;
            $user['access_token'] = $token;
            return Common::response(200, "Đăng nhập thành công.", $user, null, 'first', false);
        } catch (\Exception $exception) {
            return Common::response(500, "Có lỗi xảy ra, vui lòng thử lái.");
        }
    }

    public function forgot(ForgotRequest $request)
    {
        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return Common::response(404, 'Địa chỉ email này chưa đăng kí tài khoản nào.');
        }

        $token = Str::random(60);
        Redis::setex($request->email . '_reset_token', 500, $token);

        dispatch(new SendForgotPasswordEmail($user->email, $token));

        return Common::response(200, 'Thành công, vui lòng kiểm tra email');
    }
    public function reset(ResetRequest $request)
    {
        $storedToken = Redis::get($request->email . '_reset_token');

        if (!$storedToken || $storedToken !== $request->token) {
            return Common::response(400, 'Token không hợp lệ hoặc liên kết đã hết hạn!');
        }

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return Common::response(404, "Địa chỉ email này chưa đăng kí tài khoản nào.");
        }

        $user->password = Hash::make($request->password);
        $user->save();

        Redis::del($request->email . '_reset_token');

        return Common::response(200, "Thay đổi mật khẩu thành công.");
    }
}
