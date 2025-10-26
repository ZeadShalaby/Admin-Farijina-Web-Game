<?php

namespace App\Http\Controllers\Api\Auth;

use libphonenumber\PhoneNumberUtil;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\SocialRegisterRequest;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Traits\Api\AuthTrait as ApiAuthTrait;
use App\Traits\AuthTrait;
use Ichtrojan\Otp\Otp;
use App\Traits\WhatsAppTrait;
use Illuminate\Http\Request;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Auth;
// __('custom.')
class AuthController extends Controller
{
    use Notifiable, WhatsAppTrait, ApiAuthTrait;
    private $auth;
    public $otp;

    public function __construct()
    {
        $this->otp = new Otp;
    }
    public function login(LoginRequest $request)
    {
        $credentials = $request->only(['email', 'password']);
        if (!Auth::attempt($credentials)) {
            throw new \Illuminate\Auth\AuthenticationException(__('custom.authentication_failed'));
        }
        $user = auth()->user();
        if ($user->status == '0') {
            throw new \Illuminate\Auth\AuthenticationException(__('custom.user_blocked'));
        }
        // if (!$user->email_verified_at) {
        //     return response()->json(['message' => 'ارجوك فعل الحساب الخاص بك', 'status_code' => 404], 404);
        // }
        $user['phone'] = phoneNumberFormat($user->phone);
        $token = $this->createTokenForUser($user);
        $this->updateFcm($user, $request->fcm);
        return response()->json(['token' => $token, "user" => $user, 'message' => 'Success', 'status_code' => 200], 200);
    }



    public function logout(Request $request)
    {
        $request->user->tokens()->delete();
        return response()->json(['message' => 'Success', 'status_code' => 200,], 200);
    }


    public function register(RegisterRequest $request)
    {
        try {
            $user = $this->createUser($request->validated());

            // $inpout = $user->email;
            // $otp = generateOtp($inpout);
            // send email
            $newUser = User::where('email', $user->email)->first();
            $token = $this->createTokenForUser($newUser);
            $newUser['phone'] = phoneNumberFormat($user->phone);

            return response()->json([
                'token' => $token,
                'user' => $newUser,
                'message' => 'Success',
                'status_code' => 200
            ], 200);
        } catch (\Throwable $th) {
            return errorResponse($th->getMessage());
        }
    }
}
