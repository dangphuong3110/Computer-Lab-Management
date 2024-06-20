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

Route::middleware(['check.role:student'])->group(function () {
    Route::resource('/student', StudentController::class);
});

Route::middleware(['check.role:lecturer'])->group(function () {
    Route::resource('/lecturer', LecturerController::class);
});

Route::middleware(['check.role:technician'])->group(function () {
    Route::resource('/technician', TechnicianController::class);
});

Route::middleware(['check.role:manager'])->group(function () {
    Route::resource('/manager', ManagerController::class);
});

Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

//Route::middleware(['check.role:null'])->group(function () {
//    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
//});

//Route::get('/{any}', function () {
//    return redirect('/login');
//})->where('any', '.*');
