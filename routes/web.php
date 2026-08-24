<?php

use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\MenuController;
use App\Http\Controllers\Admin\PaymentController;
use App\Http\Controllers\Admin\ReservationListController;
use App\Http\Controllers\Admin\TableController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PaymentReceiptController;
use App\Http\Controllers\PublicController;
use App\Http\Controllers\ReservationController;
use Illuminate\Support\Facades\Route;

Route::get('/', [PublicController::class, 'home'])->name('home');
Route::get('/menu', [PublicController::class, 'menu'])->name('menu');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'login'])->name('login');
    Route::post('/login', [AuthController::class, 'authenticate']);
    Route::get('/register', [AuthController::class, 'register'])->name('register');
    Route::post('/register', [AuthController::class, 'storeUser'])->middleware('throttle:10,1');
});
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

Route::middleware(['auth', 'role:customer'])->group(function () {
    Route::get('/payments/{payment}', PaymentReceiptController::class)->name('payments.show');
    Route::get('/reservations', [ReservationController::class, 'index'])->name('reservations.index');
    Route::get('/reservations/create', [ReservationController::class, 'create'])->name('reservations.create');
    Route::get('/reservations/availability', [ReservationController::class, 'availability'])->name('reservations.availability');
    Route::post('/reservations/check', [ReservationController::class, 'check'])->name('reservations.check');
    Route::post('/reservations', [ReservationController::class, 'store'])->name('reservations.store');
    Route::get('/reservations/{reservation}', [ReservationController::class, 'show'])->name('reservations.show');
    Route::get('/reservations/{reservation}/edit', [ReservationController::class, 'edit'])->name('reservations.edit');
    Route::post('/reservations/{reservation}/check', [ReservationController::class, 'check'])->name('reservations.edit-check');
    Route::patch('/reservations/{reservation}', [ReservationController::class, 'update'])->name('reservations.update');
    Route::post('/reservations/{reservation}/cancel', [ReservationController::class, 'cancel'])->name('reservations.cancel');
});
Route::prefix('admin')->name('admin.')->middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/', DashboardController::class)->name('dashboard');
    Route::get('/payments', [PaymentController::class, 'index'])->name('payments.index');
    Route::get('/payments/{payment}', PaymentReceiptController::class)->name('payments.show');
    Route::get('/reservations/{reservation}/payment', [PaymentController::class, 'create'])->name('payments.create');
    Route::post('/reservations/{reservation}/payment', [PaymentController::class, 'store'])->name('payments.store');
    Route::post('/reservations/{reservation}/refund', [PaymentController::class, 'refund'])->name('payments.refund');
    Route::post('/reservations/{reservation}/finish', [PaymentController::class, 'finish'])->name('payments.finish');
    Route::resource('categories', CategoryController::class)->except('show');
    Route::resource('menus', MenuController::class)->except('show');
    Route::resource('tables', TableController::class)->except('show');
    Route::get('/reservations', ReservationListController::class)->name('reservations.index');
    Route::get('/reservations/{reservation}', [ReservationController::class, 'show'])->name('reservations.show');
    Route::get('/reservations/{reservation}/edit', [ReservationController::class, 'edit'])->name('reservations.edit');
    Route::post('/reservations/{reservation}/check', [ReservationController::class, 'check'])->name('reservations.edit-check');
    Route::patch('/reservations/{reservation}', [ReservationController::class, 'update'])->name('reservations.update');
    Route::post('/reservations/{reservation}/cancel', [ReservationController::class, 'cancel'])->name('reservations.cancel');
});
