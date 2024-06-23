<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\LecturerController;
use App\Http\Controllers\TechnicianController;
use App\Http\Controllers\ManagerController;

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
    return redirect('/login');
});

Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->middleware('guest')->name('login');

Route::middleware(['auth', 'check.role:student'])->group(function () {
    Route::resource('/student', StudentController::class);
});

Route::middleware(['auth', 'check.role:lecturer'])->group(function () {
    Route::resource('/lecturer', LecturerController::class);
});

Route::middleware(['auth'])->group(function () {
    Route::resource('/technician', TechnicianController::class);
    // Lecturer
    Route::get('/get-list-lecturer', [TechnicianController::class, 'getListLecturer'])->name('technician.get-list-lecturer')->middleware('check.role:technician');
    Route::get('/create-lecturer', [TechnicianController::class, 'createLecturer'])->name('technician.create-lecturer')->middleware('check.role:technician');
    Route::post('/store-lecturer', [TechnicianController::class, 'storeLecturer'])->name('technician.store-lecturer')->middleware('check.role:technician');
    Route::get('/edit-lecturer/{lecturer}/edit', [TechnicianController::class, 'editLecturer'])->name('technician.edit-lecturer')->middleware('check.role:technician');
    Route::put('/update-lecturer/{lecturer}', [TechnicianController::class, 'updateLecturer'])->name('technician.update-lecturer')->middleware('check.role:technician');
    Route::delete('/delete-lecturer/{lecturer}', [TechnicianController::class, 'destroyLecturer'])->name('technician.destroy-lecturer')->middleware('check.role:technician');

    Route::get('/get-list-lecturer-api', [TechnicianController::class, 'getListLecturerAPI'])->name('technician.get-list-lecturer-api');
    Route::post('/store-lecturer-api', [TechnicianController::class, 'storeLecturerAPI'])->name('technician.store-lecturer-api');
    Route::put('/update-lecturer-api/{lecturer}', [TechnicianController::class, 'updateLecturerAPI'])->name('technician.update-lecturer-api');
    Route::delete('/delete-lecturer-api/{lecturer}', [TechnicianController::class, 'destroyLecturerAPI'])->name('technician.destroy-lecturer-api');
    Route::post('/import-lecturer-api', [TechnicianController::class, 'importLecturerAPI'])->name('technician.import-lecturer-api');
});

Route::middleware(['auth', 'check.role:manager'])->group(function () {
    Route::resource('/manager', ManagerController::class);
});

Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

//Route::middleware(['check.role:null'])->group(function () {
//    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
//});

//Route::get('/{any}', function () {
//    return redirect('/login');
//})->where('any', '.*');
