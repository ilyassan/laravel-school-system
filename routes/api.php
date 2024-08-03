<?php

use App\Http\Controllers\GradeController;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->prefix('export')->name('export.')->group(function () {
    Route::post('grades', [GradeController::class, 'export'])->name('grades');
    Route::post('cancel', [GradeController::class, 'cancelExport'])->name('cancel');
});

Route::middleware(['auth:sanctum', 'teacher'])->post('/students-search', function (Request $request) {

    $search = $request->get('search', '');
    $classId = $request->get('class_id', '');

    if (!$classId) {
        return response()->json(['error' => 'Missing class parameter'], 400);
    }

    $queryBuilder = User::students();

    $queryBuilder->where(User::CLASS_COLUMN, $classId);

    if ($search) {
        $queryBuilder->whereFullNameLike($search);
    }

    $users = $queryBuilder->get([User::PRIMARY_KEY_COLUMN_NAME, User::FIRST_NAME_COLUMN, User::LAST_NAME_COLUMN]);

    return response()->json(["students" => $users]);

})->name('students.search');