<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;

use App\Http\Controllers\User\ShopController as UserShopController;
use App\Http\Controllers\User\FavoriteController as UserFavoriteController;
use App\Http\Controllers\User\ReservationController as UserReservationController;
use App\Http\Controllers\User\MypageController as UserMypageController;
use App\Http\Controllers\User\ReservationPaymentController as UserReservationPaymentController;
use App\Http\Controllers\User\ReservationQrController as UserReservationQrController;

use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\AdminLoginController;
use App\Http\Controllers\Auth\OwnerLoginController;

use App\Http\Controllers\Admin\AdminController;

use App\Http\Controllers\Owner\DashboardController;
use App\Http\Controllers\Owner\ShopController as OwnerShopController;
use App\Http\Controllers\Owner\ReservationController as OwnerReservationController;
use App\Http\Controllers\Owner\OwnerMailController;
use App\Http\Controllers\Owner\ReservationCheckInController;

/*
|--------------------------------------------------------------------------
| 認証（利用者 web）
|--------------------------------------------------------------------------
*/

Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [RegisterController::class, 'store']);

Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login.post');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

/*
|--------------------------------------------------------------------------
| 管理者（admin）
|--------------------------------------------------------------------------
*/
Route::prefix('admin')->name('admin.')->group(function () {
    Route::middleware('guest:admin')->group(function () {
        Route::get('login', [AdminLoginController::class, 'showLoginForm'])->name('login');
        Route::post('login', [AdminLoginController::class, 'login'])->name('login.post');
    });

    Route::middleware('auth:admin')->group(function () {
        Route::get('/', [AdminController::class, 'index'])->name('dashboard');

        Route::get('/owners/create', [AdminController::class, 'createOwner'])->name('owners.create');
        Route::post('/owners', [AdminController::class, 'storeOwner'])->name('owners.store');

        Route::post('/logout', [AdminLoginController::class, 'logout'])->name('logout');
    });
});

/*
|--------------------------------------------------------------------------
| オーナー（owner）
|--------------------------------------------------------------------------
*/
Route::prefix('owner')->name('owner.')->group(function () {
    Route::middleware('guest:owner')->group(function () {
        Route::get('/login', [OwnerLoginController::class, 'showLoginForm'])->name('login');
        Route::post('/login', [OwnerLoginController::class, 'login'])->name('login.post');
    });

    Route::middleware('auth:owner')->group(function () {
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

        Route::get('/shop/create', [OwnerShopController::class, 'create'])->name('shop.create');
        Route::post('/shop', [OwnerShopController::class, 'store'])->name('shop.store');
        Route::get('/shop/edit', [OwnerShopController::class, 'edit'])->name('shop.edit');
        Route::put('/shop', [OwnerShopController::class, 'update'])->name('shop.update');

        Route::get('/reservations', [OwnerReservationController::class, 'index'])->name('reservations.index');

        Route::get('/mail', [OwnerMailController::class, 'create'])->name('mail.create');
        Route::post('/mail', [OwnerMailController::class, 'send'])->name('mail.send');

        Route::get('/checkin', [ReservationCheckInController::class, 'form'])->name('checkin.form');
        Route::post('/checkin', [ReservationCheckInController::class, 'checkin'])->name('checkin');
        Route::get('/checkin/{token}', [ReservationCheckInController::class, 'checkinByToken'])->name('checkin.token');

        Route::post('/logout', [OwnerLoginController::class, 'logout'])->name('logout');
    });
});

/*
|--------------------------------------------------------------------------
| 公開ページ
|--------------------------------------------------------------------------
*/
Route::get('/', [UserShopController::class, 'index'])->name('shops.index');
Route::get('/shops/{shop}', [UserShopController::class, 'show'])->name('shops.show');
Route::get('/detail/{shop}', [UserShopController::class, 'detail'])->name('detail');

/*
|--------------------------------------------------------------------------
| 利用者ログイン必須（web）
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    Route::get('/mypage', [UserMypageController::class, 'index'])->name('mypage');

    Route::get('/favorites', [UserFavoriteController::class, 'index'])->name('favorite.index');
    Route::post('/shops/{shop}/favorite', [UserFavoriteController::class, 'store'])->name('favorite.store');
    Route::delete('/shops/{shop}/favorite', [UserFavoriteController::class, 'destroy'])->name('favorite.destroy');

    Route::get('/reservations', [UserReservationController::class, 'index'])->name('reservations.index');
    Route::post('/shops/{shop}/reservations', [UserReservationController::class, 'store'])->name('reservations.store');
    Route::get('/reservations/{reservation}/edit', [UserReservationController::class, 'edit'])->name('reservations.edit');
    Route::patch('/reservations/{reservation}', [UserReservationController::class, 'update'])->name('reservations.update');
    Route::delete('/reservations/{reservation}', [UserReservationController::class, 'destroy'])->name('reservations.destroy');

    Route::get('/reservations/{reservation}/review', [UserReservationController::class, 'reviewForm'])->name('reservations.review.form');
    Route::post('/reservations/{reservation}/review', [UserReservationController::class, 'review'])->name('reservations.review');

    Route::post('/qr/verify', [UserReservationController::class, 'verifyQr'])->name('qr.verify');

    Route::get('/reservations/{reservation}/qr', [UserReservationQrController::class, 'show'])->name('reservations.qr');

    Route::get('/reservations/{reservation}/checkout', [UserReservationPaymentController::class, 'checkout'])->name('reservations.checkout');
    Route::get('/reservations/success', [UserReservationPaymentController::class, 'success'])->name('reservations.success');
    Route::get('/reservations/cancel', [UserReservationPaymentController::class, 'cancel'])->name('reservations.cancel');

    Route::view('/done', 'reservations.done')->name('reservations.done');

    Route::get('/email/verify', fn() => view('auth.verify_prompt'))->name('verification.notice');

    Route::post('/email/verification-notification', function (Request $request) {
        $request->user()->sendEmailVerificationNotification();
        return back()->with('status', 'verification-link-sent');
    })->middleware('throttle:6,1')->name('verification.send');
});

/*
|--------------------------------------------------------------------------
| メール内リンクからの遷移
|--------------------------------------------------------------------------
*/
Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
    $request->fulfill();
    return redirect('/');
})->middleware(['auth', 'signed'])->name('verification.verify');

/*
|--------------------------------------------------------------------------
| MailHog
|--------------------------------------------------------------------------
*/
Route::get('/verify/mailhog', fn() => redirect('http://localhost:8025'))->name('verify.mailhog');
