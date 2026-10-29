<?php

use App\Http\Controllers\Admin\AuditLogController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\FeeRateController;
use App\Http\Controllers\Admin\FeeTypeController;
use App\Http\Controllers\Admin\RequirementTypeController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Auth\LoginController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Admin Panel — Front Page, Login, Admin Dashboard + Full Admin Features
|--------------------------------------------------------------------------
*/

// Front Page
Route::get('/', function () {
    return view('public.landing');
})->name('landing');

// Stub routes for landing page links
Route::get('/track', fn() => redirect('/login'))->name('track.show');
Route::get('/verify', fn() => redirect('/login'))->name('verify.lookup');
Route::get('/register', fn() => redirect('/login'))->name('register');

// Login / Logout
Route::get('/login', [LoginController::class, 'show'])->name('login')->middleware('guest');
Route::post('/login', [LoginController::class, 'authenticate'])->middleware('guest');
Route::post('/logout', [LoginController::class, 'destroy'])->name('logout');

// Redirect /dashboard to admin dashboard
Route::middleware(['auth'])->get('/dashboard', function () {
    if (! auth()->user()->isAdmin()) {
        abort(403, 'Access restricted to administrators.');
    }
    return redirect('/admin/dashboard');
});

// Full Admin Routes
Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::resource('users', UserController::class)->except(['show', 'destroy']);
    Route::resource('requirement-types', RequirementTypeController::class)
        ->parameters(['requirement-types' => 'requirementType'])
        ->except(['show', 'destroy']);
    Route::resource('fee-types', FeeTypeController::class)
        ->parameters(['fee-types' => 'feeType'])
        ->except(['show', 'destroy']);
    Route::resource('fee-rates', FeeRateController::class)
        ->parameters(['fee-rates' => 'feeRate'])
        ->except(['show', 'destroy']);

    Route::get('/settings', [SettingController::class, 'show'])->name('settings');
    Route::get('/audit-logs', [AuditLogController::class, 'index'])->name('audit-logs.index');
});
