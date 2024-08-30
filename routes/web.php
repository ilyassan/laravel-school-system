<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GradeController;
use App\Http\Controllers\ThemeController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\InvoiceController;

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

require __DIR__ . '/auth.php';

Route::get('/', function () {
    return redirect()->route('dashboard');
});

Route::middleware('auth')->get('/dashboard', DashboardController::class)->name('dashboard');

Route::middleware('auth')->prefix('profile')->name('profile.')->controller(ProfileController::class)->group(function () {
    Route::get('/', 'index')->name('index');
    Route::get('/edit', 'edit')->name('edit');
    Route::patch('/update', 'update')->name('update');

    Route::get('/reset-password', 'showResetPassword')->name('show-reset-password');
    Route::patch('/reset-password', 'resetPassword')->name('reset-password');
    Route::get('/reset-image', 'resetImage')->name('reset-image');

    Route::get('/{id}', 'show')->name('show');
});

Route::middleware('auth')->group(function () {
    Route::resource('grades', GradeController::class);

    // Custom route for file download
    Route::post('grades/download', [GradeController::class, 'download'])->name('grades.download');
});

Route::middleware(['auth', 'admin'])->group(function () {
    Route::resource('invoices', InvoiceController::class);
});


// theme route
Route::get('/{page}', [ThemeController::class, 'index']);

// Route::middleware('auth')->get('/{page}', fn() => view('errors.404'));