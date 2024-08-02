<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GradeController;
use App\Http\Controllers\ThemeController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;

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
    return redirect()->route('dashboard');
});

Route::get('/dashboard', DashboardController::class)->middleware('auth')->name('dashboard');

Route::middleware('auth')->prefix('profile')->name('profile.')->group(function () {
    Route::get('/', [ProfileController::class, 'index'])->name('index');
    Route::get('/edit', [ProfileController::class, 'edit'])->name('edit');
    Route::patch('/update', [ProfileController::class, 'update'])->name('update');
    Route::get('/reset-password', [ProfileController::class, 'showResetPassword'])->name('show-reset-password');
    Route::patch('/reset-password', [ProfileController::class, 'resetPassword'])->name('reset-password');
});

Route::middleware('auth')->prefix('grades')->name('grades.')->group(function () {
    Route::resource('/', GradeController::class);

    // Custom route for file download
    Route::post('/download', [GradeController::class, 'download'])->name('download');
});

require __DIR__ . '/auth.php';


// theme route
Route::get('/{page}', [ThemeController::class, 'index']);

// Route::middleware('auth')->get('/{page}', fn() => view('errors.404'));