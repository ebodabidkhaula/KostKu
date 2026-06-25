<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\KostController;
use App\Http\Controllers\Admin;
use App\Http\Controllers\User;
use Illuminate\Support\Facades\Route;

// ───────── Public Routes ─────────
Route::get('/', function () {
    $featured = \App\Models\Kost::featured()->active()
        ->with(['primaryPhoto', 'reviews'])->limit(6)->get();
    $cities = \App\Models\Kost::active()->distinct()->pluck('city');
    $stats = [
        'total_kost'  => \App\Models\Kost::active()->count(),
        'total_users' => \App\Models\User::where('role', 'user')->count(),
        'total_cities'=> \App\Models\Kost::active()->distinct('city')->count('city'),
    ];
    return view('welcome', compact('featured', 'cities', 'stats'));
})->name('home');

Route::get('/kost', [KostController::class, 'index'])->name('kost.index');
Route::get('/kost/{kost:slug}', [KostController::class, 'show'])->name('kost.show');
Route::post('/kost/{kost:slug}/review', [KostController::class, 'storeReview'])
    ->name('kost.review.store')
    ->middleware('auth');

// ───────── Auth Routes ─────────
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout')
    ->middleware('auth');

// ───────── User Routes ─────────
Route::middleware(['auth'])->prefix('user')->name('user.')->group(function () {
    Route::get('/dashboard', [User\DashboardController::class, 'index'])->name('dashboard');

    // Profile
    Route::get('/profile', [AuthController::class, 'showProfile'])->name('profile');
    Route::put('/profile', [AuthController::class, 'updateProfile'])->name('profile.update');
    Route::put('/password', [AuthController::class, 'changePassword'])->name('password.update');

    // Bookings
    Route::get('/booking', [User\BookingController::class, 'index'])->name('booking.index');
    Route::get('/booking/create/{kost}', [User\BookingController::class, 'create'])->name('booking.create');
    Route::post('/booking', [User\BookingController::class, 'store'])->name('booking.store');
    Route::get('/booking/{booking}', [User\BookingController::class, 'show'])->name('booking.show');
    Route::patch('/booking/{booking}/cancel', [User\BookingController::class, 'cancel'])->name('booking.cancel');
    Route::post('/booking/{booking}/payment', [User\BookingController::class, 'uploadPayment'])->name('booking.payment');
    Route::post('/booking/{booking}/review', [User\BookingController::class, 'storeReview'])->name('booking.review');
});

// ───────── Admin Routes ─────────
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [Admin\DashboardController::class, 'index'])->name('dashboard');

    // Kost CRUD
    Route::resource('kost', Admin\KostController::class);
    Route::delete('/kost/photo/{photo}', [Admin\KostController::class, 'deletePhoto'])->name('kost.photo.delete');

    // Room CRUD (nested under kost)
    Route::post('/kost/{kost}/rooms', [Admin\RoomController::class, 'store'])->name('kost.rooms.store');
    Route::put('/kost/{kost}/rooms/{room}', [Admin\RoomController::class, 'update'])->name('kost.rooms.update');
    Route::delete('/kost/{kost}/rooms/{room}', [Admin\RoomController::class, 'destroy'])->name('kost.rooms.destroy');

    // Booking management
    Route::resource('booking', Admin\BookingController::class)->except(['edit']);
    Route::get('/booking/{kost}/rooms', [Admin\BookingController::class, 'getRooms'])->name('booking.rooms');

    // User management
    Route::get('/users', [Admin\UserController::class, 'index'])->name('users.index');
    Route::get('/users/{user}', [Admin\UserController::class, 'show'])->name('users.show');
    Route::patch('/users/{user}/status', [Admin\UserController::class, 'toggleStatus'])->name('users.toggle');
    Route::delete('/users/{user}', [Admin\UserController::class, 'destroy'])->name('users.destroy');

    // Reviews
    Route::get('/reviews', [Admin\ReviewController::class, 'index'])->name('reviews.index');
    Route::patch('/reviews/{review}/approve', [Admin\ReviewController::class, 'approve'])->name('reviews.approve');
    Route::delete('/reviews/{review}', [Admin\ReviewController::class, 'destroy'])->name('reviews.destroy');

    // Reports
    Route::get('/reports', [Admin\ReportController::class, 'index'])->name('reports.index');
});
