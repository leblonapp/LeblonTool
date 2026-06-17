<?php

use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProviderController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ChatRoomController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\PaymentController;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return Inertia::render('Dashboard');
    })->name('dashboard');


    Route::resource('categories', CategoryController::class);

    Route::resource('users', UserController::class)->except(['show']);

    // Providers (coleção "providers" no Firestore)
    Route::resource('providers', ProviderController::class);

    // Provider sub-resources
    Route::post('providers/{provider}/services', [ProviderController::class, 'storeService'])->name('providers.services.store');
    Route::put('providers/{provider}/services/{service}', [ProviderController::class, 'updateService'])->name('providers.services.update');
    Route::delete('providers/{provider}/services/{service}', [ProviderController::class, 'destroyService'])->name('providers.services.destroy');
    Route::put('providers/{provider}/schedule', [ProviderController::class, 'updateSchedule'])->name('providers.schedule.update');

    Route::resource('chat-rooms', ChatRoomController::class)->only(['index', 'show']);

    Route::resource('bookings', BookingController::class)->only(['index', 'show']);
    Route::resource('payments', PaymentController::class)->only(['index', 'show']);

});
