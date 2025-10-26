<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\ChangePasswordRequest;
use App\Http\Requests\UpdateProfileRequest;
use App\Models\Coupon;
use App\Models\CouponUsage;
use App\Models\Question;
use App\Models\User;
use App\Models\UserQuestionView;
use App\Traits\ImageProcessing;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\PersonalAccessToken;

class UserController extends Controller
{
    use ImageProcessing;
    /**
     * Update user profile
     */
    public function updateProfile(UpdateProfileRequest $request)
    {
        try {
            $user = $request->user;

            // Check if email is being changed and is unique
            if ($request->filled('email') && $request->email !== $user->email) {
                if (User::where('email', $request->email)->exists()) {
                    return response()->json([
                        'message' => 'Email already exists',
                        'status_code' => 422
                    ], 422);
                }
            }

            // Check if phone is being changed and is unique
            if ($request->filled('phone') && $request->phone !== $user->phone) {
                if (User::where('phone', $request->phone)->exists()) {
                    return response()->json([
                        'message' => 'Phone number already exists',
                        'status_code' => 422
                    ], 422);
                }
            }

            // Check if username is being changed and is unique
            if ($request->filled('username') && $request->username !== $user->username) {
                if (User::where('username', $request->username)->exists()) {
                    return response()->json([
                        'message' => 'Username already exists',
                        'status_code' => 422
                    ], 422);
                }
            }

            $user->update($request->validated());
            $userUpdate = User::where('id', $user->id)->first();
            $userUpdate['phone'] = phoneNumberFormat($user->phone);

            return response()->json([
                'message' => 'Profile updated successfully',
                'data' => $userUpdate,
                'status_code' => 200
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error updating profile: ' . $e->getMessage(),
                'status_code' => 500
            ], 500);
        }
    }

    /**
     * Change user password
     */
    public function changePassword(ChangePasswordRequest $request)
    {
        try {
            $user = $request->user;

            // Check if current password is correct
            if (!Hash::check($request->current_password, $user->password)) {
                return response()->json([
                    'message' => 'Current password is incorrect',
                    'status_code' => 422
                ], 422);
            }

            // Update password
            $user->update([
                'password' => Hash::make($request->new_password)
            ]);

            return response()->json([
                'message' => 'Password changed successfully',
                'status_code' => 200
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error changing password: ' . $e->getMessage(),
                'status_code' => 500
            ], 500);
        }
    }

    public function getUserInfo(Request $request)
    {
        try {
            $token = $request->bearerToken();
            $accessToken = PersonalAccessToken::findToken($token);
            if (!$accessToken) {
                return response()->json(['message' => __('custom.unauthorized'), 401], 401);
            }

            $user = $accessToken->tokenable;
          
            return successResponse(["user" => $user]);
        } catch (\Throwable $th) {
            return response()->json(['message' => __('custom.server_issue'), 'status_code' => 404,], 404);
        }
    }
    public function getUserProfile(int $id)
    {
        try {
            $user = User::find($id);
            $userId = $user->id;
            $userData = User::with([
                'evaluations' => function ($query) use ($id) {
                    $query->where('owner_id', $id);
                },
                'skills',
                "profession",
                'services'
            ])->find($user->id);

            return successResponse(["user" => $userData]);
        } catch (\Throwable $th) {
            return errorResponse("Something went wrong", 500);
        }
    }

    public function getOtpForUser(Request $request)
    {
        echo $request->email . "+";
        $email = $request->email;
        $otp = DB::table('otps')->where('identifier', "+" . $email)->orderBy('id', 'desc')->get();
        return response()->json(['otp' => $otp], 200);
    }
    /**
     * Validate and apply a coupon.
     *
     * Expected request payload:
     * {
     *   "code": "YOUR_COUPON_CODE"
     * }
     */
    public function applyCoupon(Request $request)
    {
        // Validate the incoming coupon code
        $request->validate([
            'code' => 'required|string',
        ]);

        $code = $request->input('code');

        // Find the coupon by code
        $coupon = Coupon::where('code', $code)->first();

        if (!$coupon) {
            return response()->json(['error' => 'رمز الكوبون غير صحيح.'], 404);
        }

        // Check if the coupon is active
        if (!$coupon->active) {
            return response()->json(['error' => 'الكوبون غير مفعل.'], 400);
        }

        // Validate the coupon's validity period if set
        $now = now();

        if ($coupon->start_date && $now->lt($coupon->start_date)) {
            return response()->json(['error' => 'الكوبون ليس ساري المفعول بعد.'], 400);
        }

        if ($coupon->end_date && $now->gt($coupon->end_date)) {
            return response()->json(['error' => 'الكوبون منتهي الصلاحية.'], 400);
        }

        // Check overall usage limit
        $totalUsage = CouponUsage::where('coupon_id', $coupon->id)->count();
        if ($coupon->usage_limit !== null && $totalUsage >= $coupon->usage_limit) {
            return response()->json(['error' => 'تم الوصول إلى الحد الأقصى لاستخدام الكوبون.'], 400);
        }

        // Check usage limit per user (if a user is authenticated)
        if ($request->user) {
            $userUsage = CouponUsage::where('coupon_id', $coupon->id)
                ->where('user_id', $request->user->id)
                ->count();

            if ($coupon->usage_per_user !== null && $userUsage >= $coupon->usage_per_user) {
                return response()->json(['error' => 'لقد وصلت إلى الحد الأقصى لاستخدام هذا الكوبون.'], 400);
            }
        }

        // If all validations pass, record the coupon usage
        if ($request->user) {
            CouponUsage::create([
                'coupon_id' => $coupon->id,
                'user_id'   => $request->user->id,
            ]);
        } else {
            // If there is no authenticated user, record usage without user_id
            CouponUsage::create([
                'coupon_id' => $coupon->id,
            ]);
        }
        if ($coupon->type == 'free_games') {
            // Apply discount to the user's cart
            $request->user->update([
                'num_of_games' => $request->user->num_of_games + $coupon->total_games,
            ]);
        }

        // Return coupon details if validation is successful
        return response()->json([
            'message' => 'الكوبون صالح وتم تطبيقه بنجاح.',
            'coupon'  => $coupon,
        ]);
    }
}
