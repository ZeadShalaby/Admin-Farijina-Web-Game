<?php

use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\Dashboard\RoleController;
use App\Http\Controllers\Dashboard\UserController;
use App\Http\Controllers\Dashboard\SkillController;
use App\Http\Controllers\Dashboard\BannerController;
use App\Http\Controllers\Dashboard\CouponController;
use App\Http\Controllers\Dashboard\VendorController;
use App\Http\Controllers\Dashboard\CompanyController;
use App\Http\Controllers\Dashboard\AdminLoginHistoryController;
use App\Http\Controllers\Dashboard\SettingController;
use App\Http\Controllers\Dashboard\CategoryController;
use App\Http\Controllers\Dashboard\QuestionController;
use App\Http\Controllers\Dashboard\ContactUsController;
use App\Http\Controllers\Dashboard\DashboardController;
use App\Http\Controllers\Dashboard\SettingWebController;
use App\Http\Controllers\Dashboard\QuestionVatController;
use App\Http\Controllers\Dashboard\PaymentGatewayController;
use App\Http\Controllers\Dashboard\QuestionHorrorController;
use App\Http\Controllers\Dashboard\QuestionImportController;
use App\Http\Controllers\Dashboard\UserTransactionController;
use App\Exports\CouponsExport;
use Illuminate\Http\Request;
use App\Models\AdminLoginHistory;
use App\Enums\CategoryFilterMode;


Route::group(['middleware' => ['auth']], function () {
  
 Route::post('/logout', function (Request $request) {
         
        // ? Log The Logout Time
        AdminLoginHistory::logLogout(session()->getId());
   
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect('/login');
    })->name('logout');

    Route::get('/', function () {
        if (Auth::User()->type == 'admin') {
            return redirect()->route('home')->with('success', 'successfully');
        } else if (Auth::User()->type == 'vendor') {
            return redirect()->route('vendorMain')->with('success', 'successfully');
        } else {
            return 'user';
        }
    });
    Route::get('/orders-statistics', [DashboardController::class, 'getStatistics']);
    Route::get('/home', [DashboardController::class, 'main'])->name('home');
    Route::get('/vendor', [VendorController::class, 'vendorMain'])->name('vendorMain');

    Route::get('/delete-statics', [DashboardController::class, 'deleteStaticsUser'])->name('delete-statics');
    Route::resource('roles', RoleController::class);


    Route::get('/notification/markAllAsRead', [NotificationController::class, 'markAllAsRead'])->name('notification.markAllAsRead');
    Route::controller(ContactUsController::class)->group(function () {
        Route::get('/contactus', 'index')->name('contactus');
        Route::get('/contactus/{id}', 'show')->name('contactus.show');
        Route::delete('/contactus/destroy', 'destroy')->name('contactus.destroy');
        Route::post('/contactus/bulk-delete', 'bulkDelete')->name('contactus.bulk-delete');
    });

    Route::controller(CouponController::class)->group(function () {
        Route::get('/coupons', 'index')->name('coupons');
        Route::get('/coupons/create', 'create')->name('coupons.create');
        Route::post('/coupons/store', 'store')->name('coupons.store');
        Route::post('/coupons/update', 'update')->name('coupons.update');
        Route::post('/coupons/destroy', 'destroy')->name('coupons.destroy');
    });
  
    Route::controller(AdminLoginHistoryController::class)->group(function () {
        Route::get('/login-history', 'index')->name('login.history');
    });



    Route::controller(CompanyController::class)->group(function () {
        Route::get('/companies', 'index')->name('companies');
        Route::post('/companies/store', 'store')->name('companies.store');
        Route::get('/companies/{company}/coupons', [CompanyController::class, 'CompanyCoupon'])->name('companies.coupons');
        Route::put('/companies/update', 'update')->name('companies.update');
        Route::delete('/companies/delete', 'delete')->name('companies.destroy');
        Route::get('/companies/{company}/analysis', [CompanyController::class, 'analysis']);

        Route::get('/export-coupons', function () {
            $start = request('start'); // YYYY-MM-DD
            $end = request('end');     // YYYY-MM-DD
            $createdAt = request('created_by'); // اختياري
            $companyId = request('company_id');
            $companyName = request('company_name') ?: 'جميع الشركات';
            $batch = request('batch') ?: 'كل المجموعات';

            $fileName = $companyName . ' (' . $batch . ').xlsx';

            return Excel::download(
                new CouponsExport($start, $end, $createdAt, $companyId, $batch),
                $fileName
            );
        })->name('export.coupons');
        Route::get('/coupons/batches', [CouponController::class, 'batch'])->name('coupons.batches');


    });

    Route::get('/category/{categoryId}/questions', [CategoryController::class, 'showCategoryQuestions'])->name('categories.show');
    Route::post('/categories/update-position', [CategoryController::class, 'reorder'])->name('categories.reorder');
    Route::get('/categories/sort',[CategoryController::class,'showSort'])->name('categories.sort');
    Route::get('/categories/no-words/{mode}', [CategoryController::class, 'noWordCategory'])
      ->whereIn('mode', [
        CategoryFilterMode::DEFAULT->value,
        CategoryFilterMode::MEDIUM->value,
        CategoryFilterMode::SPECIAL->value
      ])
      ->name('categories.noWordCategory');
    Route::resource('categories', CategoryController::class);
    Route::post('/categories/import', [CategoryController::class, 'import'])->name('categories.import');
    Route::post('/questions/import', [QuestionController::class, 'import'])->name('questions.import');
    Route::post('/questions/horror/import', [QuestionHorrorController::class, 'import'])->name('questionshorror.import');
    Route::post('/questions/vertebrae/import', [QuestionVatController::class, 'import'])->name('vertebrae.import');


    Route::resource('transaction', UserTransactionController::class);
    Route::post('/transaction/bulk-delete', [UserTransactionController::class, 'bulkDelete'])->name('transaction.bulk-delete');
    Route::get('/transaction/export', [UserTransactionController::class, 'exportData'])->name('transaction.export');
    Route::resource('questions', QuestionController::class);
    Route::post('/questions/update-status', [QuestionController::class, 'updateStatus'])->name('questions.updateStatus');
    Route::resource('question_horror', QuestionHorrorController::class);
    Route::resource('question_vertebrae', QuestionVatController::class);

    Route::resource('skills', SkillController::class);
    Route::controller(SettingController::class)->group(function () {
        Route::get('/setting', 'index')->name('setting');
        ;
        Route::post('/setting.store', 'store')->name('setting.store');
        Route::post('/setting.update', 'update')->name('setting.update');
        Route::post('/setting.destroy', 'destroy')->name('setting.destroy');
    });
    Route::controller(BannerController::class)->group(function () {
        Route::get('/banners', 'index')->name('banners');
        // Route::get('/coupons/create', 'create')->name('coupons.create');
        Route::post('/banners/store', 'store')->name('banners.store');
        Route::put('/banners/update', 'update')->name('banners.update');
        Route::delete('/banners/destroy', 'destroy')->name('banners.destroy');
        Route::post('/banners/update-status', 'updateStatusBanner')->name('banners.update-status');
    });

    Route::controller(UserController::class)->group(function () {
        Route::get('/user', 'index')->name('user');
        Route::post('/user.store', 'store')->name('user.store');
        Route::post('/user.edit', 'edit')->name('user.edit');
        Route::post('/user.update', 'update')->name('user.update');
        Route::post('/user/update/note', 'userUpdateNote')->name('user.updateNote');
        Route::post('/user/user.destroy', 'destroy')->name('user.destroy');
        Route::post('/user/wallet', 'chargeWallet')->name('user.wallet');
        Route::post('/userCreate', 'create')->name('userCreate');
        Route::get('/userUpdate/{id}', 'userUpdate')->name('userUpdate');
        Route::get('/user/vendeors', 'vendeors')->name('user.vendeors');
    });

    Route::controller(SettingWebController::class)->group(function () {
        Route::get('/setting_web', 'index')->name('setting_web');
        Route::get('/setting/gift', 'gift')->name('setting.gift');
        Route::get('/colorweb', 'colorweb')->name('colorweb');
        Route::post('/settings/update', 'update')->name('settings.update');
        Route::post('/settings/updateGift', 'updateGift')->name('settings.updateGift');
        Route::post('/settings/store', 'store')->name('settings.store');
        Route::post('updatewebsite', 'updatewebsite')->name('admin.updatewebsite');
    });
    Route::controller(PaymentGatewayController::class)->group(function () {
        Route::get('/gateways', 'index')->name('gateways');
        Route::post('/gateways/update', 'update')->name('gateways.update');
    });

    Route::get('import/preview-file/{filename}', [QuestionImportController::class, 'previewFile'])->name('import.previewFile');
    Route::post('import/upload-file', [QuestionImportController::class, 'uploadFileForRow'])->name('import.uploadFileForRow');
    Route::post('import/update-row', [QuestionImportController::class, 'updateRowData'])->name('import.updateRowData');

    Route::post('import/upload', [QuestionImportController::class, 'uploadAndPreview'])->name('import.upload');
    // Page to show the preview (the view 'import.preview' should be created by you)
    Route::get('import/index', function () {
        // session()->flush();
        return view('dashboard.import.index');
    })->name('import.index');

    Route::post('import/confirm', [QuestionImportController::class, 'confirmImport'])->name('import.confirm');
    Route::post('import/cancel', [QuestionImportController::class, 'cancelImport'])->name('import.cancel');
    Route::get('import/download', [QuestionImportController::class, 'downloadUpdatedExcel'])->name('import.download');
    
    Route::controller(RulesController::class)->group(function () {
      Route::get('/rules', 'index')->name('rules.index');
      Route::post('/rules/store', 'store')->name('rules.store');
      Route::get('/rules/{id}', 'show')->name('rules.show');
      Route::delete('/rules/delete', 'destroy')->name('rules.destroy');
      Route::put('/rules/{roleId}/update', 'update')->name('rules.update');
      Route::put('/rules/updateRole', 'updateRole')->name('rules.updateRole');
      Route::get('/rules/{id}/analysis',  'analysis');
    });
  
    // A success route after confirm.
    Route::get('import/success', function () {
        return view('sucss-page.blade');
    })->name('import.success');
});
