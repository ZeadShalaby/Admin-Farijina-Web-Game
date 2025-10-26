<?php

use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Route::get('/', function () {
//     return view('welcome');
// });

// Route::get('/dashboard', function () {
//     return view('dashboard');
// })->middleware(['auth', 'verified'])->name('dashboard');
require __DIR__ . '/auth.php';
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});
Route::post('/send/notification', [NotificationController::class, 'sendNotificationToUser'])->name('send.notification');

Route::post('/send/notificationToAll', [NotificationController::class, 'sendNotificationToAll'])->name('send.notificationToAll');


Route::post('/payments/create', [PaymentController::class, 'createPayment']);
Route::get('/payments/return', [PaymentController::class, 'returnUrl']);
Route::get('/payments/cancel', [PaymentController::class, 'cancelUrl']);
// Optional route for notifications/webhooks
Route::post('/payments/notify', [PaymentController::class, 'notifyUrl']);


Route::get('/download', [NotificationController::class, 'download'])->name('download');
Route::get('/upload', [NotificationController::class, 'upload'])->name('upload');
// web.php
Route::get('/question/preview/{encryptedId}', function ($encryptedId) {
    try {
        $questionId = decrypt($encryptedId);
        $question = \App\Models\Question::findOrFail($questionId);

        return view('preview.question', compact('question'));
    } catch (\Exception $e) {
        abort(404); 
    }
});

