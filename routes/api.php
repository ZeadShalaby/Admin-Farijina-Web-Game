<?php

use App\Http\Controllers\Api\Auth\AuthController;
use App\Http\Controllers\Api\Auth\EmailVerificationController;
use App\Http\Controllers\Api\Auth\ResetPasswordController;
use App\Http\Controllers\Api\Auth\UserController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\ContactUsController;
use App\Http\Controllers\Api\MyGameController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\QuestionController;
use App\Http\Controllers\Api\SettingPageController;
use App\Http\Controllers\Dashboard\CouponController;
use App\Http\Controllers\PaymentController;
use Illuminate\Support\Facades\Route;

Route::post('/payments/create', [PaymentController::class, 'createPayment'])->middleware('sanctum');
Route::group(
    ['middleware' => ['ChangeLanguage']],
    function () {
        Route::post('verification-notification', [EmailVerificationController::class, 'verificationNotification']);
        Route::post('verify-code', [EmailVerificationController::class, 'verifyCode']);
        Route::post('reset-password', [ResetPasswordController::class, 'resetPassword'])->middleware('sanctum');

        Route::controller(AuthController::class)->group(function () {

            Route::post('/login', 'login');
            Route::get('/login/invitation', 'useInvitationCode');
            Route::get('getOtpForUser',  'getOtpForUser');
            Route::post('/social/register', 'socialRegister');
            Route::post('/register', 'register');
            Route::post('/logout', 'logout')->middleware('sanctum');
            Route::delete('delete-account', 'deleteAccount')->middleware('sanctum');
        });
        Route::controller(UserController::class)->group(function () {
            Route::get('/user/data', [UserController::class, 'getUserInfo'])->middleware('sanctum');
            Route::post('/profile/update', [UserController::class, 'updateProfile'])->middleware('sanctum');
            Route::post('/profile/change-password', [UserController::class, 'changePassword'])->middleware('sanctum');
        });
        Route::controller(SettingPageController::class)->group(function () {
            Route::get('terms', 'termsPage');
            Route::get('about', 'aboutPage');
            Route::get('privacy', 'privacyPage');
            Route::post('sendOtp', 'sendOtp')->name('sendOtp');
        });

        Route::post('/games/{gameId}', [MyGameController::class, 'updateGame'])->middleware('sanctum');
        Route::get('/games/{gameId}', [MyGameController::class, 'show'])->middleware('sanctum');


        Route::controller(ContactUsController::class)->group(function () {
            Route::post('/contact-us', 'store');
        });
        Route::post('/coupons/apply', [UserController::class, 'applyCoupon'])->middleware('sanctum');

        Route::controller(NotificationController::class)->group(function () {
            Route::get('/test-notification', 'sendNotficationTest')->middleware('sanctum');
            Route::get('/notifications', 'getUserNotifications')->middleware('sanctum');
            Route::get('/notifications/unread', 'getUnReadNotifications')->middleware('sanctum');
            Route::post('/notifications/{notification}/read', 'markAsRead')->middleware('sanctum');
            Route::post('/notifications/mark-all-read', 'markAllAsRead')->middleware('sanctum');
            Route::delete('/notifications/{notificationId}/delete',  'deleteNotification')->middleware('sanctum');
            Route::delete('/notifications/delete-all', 'deleteAllNotifications')->middleware('sanctum');
        });

        Route::controller(CategoryController::class)->group(function () {
            Route::get('/categories', 'index');
        });
        Route::post('/game/duplicate', [MyGameController::class, 'duplicateGame'])->middleware('sanctum');

        Route::controller(QuestionController::class)->group(function () {
            Route::get('/get-question', 'show')->middleware('sanctum');
            Route::get('/get-question-horror', 'showQuestionHrorr')->middleware('sanctum');

            Route::post('/store-question-view', [QuestionController::class, 'storeQuestionView'])->middleware('sanctum');
            Route::get('/qrcode-generate', 'qrCode')->middleware('sanctum');
        });
        Route::get('/my-games', [MyGameController::class, 'index'])->middleware('sanctum');
        Route::post('/store-game', [MyGameController::class, 'store'])->middleware('sanctum');
    },
);


Route::get('/analytics/app', [QuestionController::class, 'main']);
Route::get('/categories/app', [QuestionController::class, 'categories']);
Route::post('/questions/storeQuestion', [QuestionController::class, 'storeQuestion']);



