<?php

use Illuminate\Support\Facades\Route;
use App\Http\Middleware\Subscribed;
use App\Http\Middleware\NotSubscribed;
use App\Http\Controllers\Admin;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RestaurantController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\TermController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\FavoriteController;

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

Route::get('/', function () {
    return view('welcome');
});

require __DIR__.'/auth.php';

// 管理者ページ用ルート
// prefix->URLの先頭　as->名前付きルートの先頭の設定ができます。グループ内のルートURLがadmin/home, 名前付きルートがadmin.homeになります。
Route::group(['prefix' => 'admin', 'as' => 'admin.', 'middleware' => 'auth:admin'], function () {
    Route::get('home', [Admin\HomeController::class, 'index'])->name('home');

    Route::get('users', [Admin\UserController::class, 'index'])->name('users.index');
    Route::get('users/{user}', [Admin\UserController::class, 'show'])->name('users.show');

    Route::get('restaurants', [Admin\RestaurantController::class, 'index'])->name('restaurants.index');
    Route::get('restaurants/create', [Admin\RestaurantController::class, 'create'])->name('restaurants.create');
    Route::post('restaurants', [Admin\RestaurantController::class, 'store'])->name('restaurants.store');
    Route::get('restaurants/{restaurant}', [Admin\RestaurantController::class, 'show'])->name('restaurants.show');
    Route::get('restaurants/{restaurant}/edit', [Admin\RestaurantController::class, 'edit'])->name('restaurants.edit');
    Route::patch('restaurants/{restaurant}', [Admin\RestaurantController::class, 'update'])->name('restaurants.update');
    Route::delete('restaurants/{restaurant}', [Admin\RestaurantController::class, 'destroy'])->name('restaurants.destroy');

    Route::get('categories', [Admin\CategoryController::class, 'index'])->name('categories.index');
    Route::post('categories', [Admin\CategoryController::class, 'store'])->name('categories.store');
    Route::patch('categories/{category}', [Admin\CategoryController::class, 'update'])->name('categories.update');
    Route::delete('categories/{category}', [Admin\CategoryController::class, 'destroy'])->name('categories.destroy');

    Route::get('company', [Admin\CompanyController::class, 'index'])->name('company.index');
    Route::get('company/{company}/edit', [Admin\CompanyController::class, 'edit'])->name('company.edit');
    Route::patch('company/{company}', [Admin\CompanyController::class, 'update'])->name('company.update');

    Route::get('terms', [Admin\TermController::class, 'index'])->name('terms.index');
    Route::get('terms/{term}/edit', [Admin\TermController::class, 'edit'])->name('terms.edit');
    Route::patch('terms/{term}', [Admin\TermController::class, 'update'])->name('terms.update');
});


// 一般ユーザーページ用ルート
// トップページ・会員情報ページ・店舗情報ページ・会社概要ページ・利用規約ページ用ルート
Route::group(['middleware' => 'guest:admin'], function () {
    Route::get('home', [HomeController::class, 'index'])->name('home');

    Route::get('user', [UserController::class, 'index'])->name('user.index');
    Route::get('user/{user}/edit', [UserController::class, 'edit'])->name('user.edit');
    Route::patch('user/{user}', [UserController::class, 'update'])->name('user.update');

    Route::get('restaurants', [RestaurantController::class, 'index'])->name('restaurants.index');
    Route::get('restaurants/{restaurant}', [RestaurantController::class, 'show'])->name('restaurants.show');

    Route::get('company', [CompanyController::class, 'index'])->name('company.index');

    Route::get('terms', [TermController::class, 'index'])->name('terms.index');
});

// サブスクリプションページ用ルート
Route::middleware(['guest:admin', 'auth', 'notsubscribed'])->group(function () {
    Route::get('subscription/create', [SubscriptionController::class, 'create'])->name('subscription.create');
    Route::post('subscription', [SubscriptionController::class, 'store'])->name('subscription.store');
});

Route::middleware(['guest:admin', 'auth', 'subscribed'])->group(function () {
    Route::get('subscription/edit', [SubscriptionController::class, 'edit'])->name('subscription.edit');
    Route::patch('subscription', [SubscriptionController::class, 'update'])->name('subscription.update');
    Route::get('subscription/cancel', [SubscriptionController::class, 'cancel'])->name('subscription.cancel');
    Route::delete('subscription', [SubscriptionController::class, 'destroy'])->name('subscription.destroy');
});

// レビューページ用ルート
Route::get('restaurants/{restaurant}/reviews', [ReviewController::class, 'index'])->middleware(['guest:admin', 'auth'])->name('restaurants.reviews.index');

Route::middleware(['guest:admin', 'auth', 'subscribed'])->group(function () {
    Route::get('restaurants/{restaurant}/reviews/create', [ReviewController::class, 'create'])->name('restaurants.reviews.create');
    Route::post('restaurants/{restaurant}/reviews', [ReviewController::class, 'store'])->name('restaurants.reviews.store');
    Route::get('restaurants/{restaurant}/reviews/{review}/edit', [ReviewController::class, 'edit'])->name('restaurants.reviews.edit');
    Route::patch('restaurants/{restaurant}/reviews/{review}', [ReviewController::class, 'update'])->name('restaurants.reviews.update');
    Route::delete('restaurants/{restaurant}/reviews/{review}', [ReviewController::class, 'destroy'])->name('restaurants.reviews.destroy');
});

// 予約ページ用ルート
Route::middleware(['guest:admin', 'auth', 'subscribed'])->group(function () {
    Route::get('reservations', [ReservationController::class, 'index'])->name('reservations.index');
    Route::get('restaurants/{restaurant}/reservations/create', [ReservationController::class, 'create'])->name('restaurants.reservations.create');
    Route::post('restaurants/{restaurant}/reservations', [ReservationController::class, 'store'])->name('restaurants.reservations.store');
    Route::delete('reservations/{reservation}', [ReservationController::class, 'destroy'])->name('reservations.destroy');
});

// お気に入りページ用ルート
Route::middleware(['guest:admin', 'auth', 'subscribed'])->group(function () {
    Route::get('favorites', [FavoriteController::class, 'index'])->name('favorites.index');
    Route::post('favorites/{restaurant_id}', [FavoriteController::class, 'store'])->name('favorites.store');
    Route::delete('favorites/{restaurant_id}', [FavoriteController::class, 'destroy'])->name('favorites.destroy');
});