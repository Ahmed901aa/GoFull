<?php

use App\Http\Controllers\Web\Admin\AnalyticsController;
use App\Http\Controllers\Web\Admin\DashboardController;
use App\Http\Controllers\Web\Admin\DriverIncomeController;
use App\Http\Controllers\Web\Admin\EmployeeController;
use App\Http\Controllers\Web\Admin\FuelPriceController;
use App\Http\Controllers\Web\Admin\ProviderVerificationController;
use App\Http\Controllers\Web\Admin\ServiceMonitorController;
use App\Http\Controllers\Web\Admin\UserController;
use App\Http\Controllers\Web\Auth\LoginController;
use Illuminate\Support\Facades\Route;

// ─── Guest ────────────────────────────────────────────────────
Route::middleware('guest')->prefix('admin')->name('admin.')->group(function () {
    Route::get('/login',  [LoginController::class, 'showForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
});

// ─── Admin + Employee ─────────────────────────────────────────
Route::middleware(['auth', 'role:admin,employee'])->prefix('admin')->name('admin.')->group(function () {

    Route::post('/logout',   [LoginController::class, 'logout'])->name('logout');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/providers',                          [ProviderVerificationController::class, 'index'])->name('providers.index');
    Route::get('/providers/{provider}',               [ProviderVerificationController::class, 'show'])->name('providers.show');
    Route::patch('/providers/{provider}/appointment', [ProviderVerificationController::class, 'setAppointment'])->name('providers.appointment');
    Route::patch('/providers/{provider}/approve',     [ProviderVerificationController::class, 'approve'])->name('providers.approve');
    Route::patch('/providers/{provider}/reject',      [ProviderVerificationController::class, 'reject'])->name('providers.reject');

    Route::get('/income',          [DriverIncomeController::class, 'index'])->name('income.index');
    Route::get('/income/{driver}', [DriverIncomeController::class, 'show'])->name('income.show');

    Route::get('/monitor',           [ServiceMonitorController::class, 'index'])->name('monitor.index');
    Route::get('/monitor/{request}', [ServiceMonitorController::class, 'show'])->name('monitor.show');

    // Admin Only
    Route::middleware('role:admin')->group(function () {
        Route::get('/users',                   [UserController::class, 'index'])->name('users.index');
        Route::get('/users/{user}',            [UserController::class, 'show'])->name('users.show');
        Route::patch('/users/{user}/suspend',  [UserController::class, 'suspend'])->name('users.suspend');
        Route::patch('/users/{user}/activate', [UserController::class, 'activate'])->name('users.activate');
        Route::delete('/users/{user}',         [UserController::class, 'destroy'])->name('users.destroy');

        Route::get('/analytics', [AnalyticsController::class, 'index'])->name('analytics.index');

        Route::get('/fuel-prices',                  [FuelPriceController::class, 'index'])->name('fuel_prices.index');
        Route::patch('/fuel-prices/{fuelPrice}',    [FuelPriceController::class, 'update'])->name('fuel_prices.update');

        Route::get('/employees',           [EmployeeController::class, 'index'])->name('employees.index');
        Route::get('/employees/create',    [EmployeeController::class, 'create'])->name('employees.create');
        Route::post('/employees',          [EmployeeController::class, 'store'])->name('employees.store');
        Route::delete('/employees/{user}', [EmployeeController::class, 'destroy'])->name('employees.destroy');
    });
});