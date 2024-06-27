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
    Route::resource('/technician', TechnicianController::class)->middleware('check.role:technician');
    // Lecturer
    Route::get('/get-list-lecturer', [TechnicianController::class, 'getListLecturer'])->name('technician.get-list-lecturer')->middleware('check.role:technician');

    Route::get('/get-list-lecturer-api', [TechnicianController::class, 'getListLecturerAPI'])->name('technician.get-list-lecturer-api');
    Route::post('/store-lecturer-api', [TechnicianController::class, 'storeLecturerAPI'])->name('technician.store-lecturer-api');
    Route::put('/update-lecturer-api/{lecturer}', [TechnicianController::class, 'updateLecturerAPI'])->name('technician.update-lecturer-api');
    Route::delete('/delete-lecturer-api/{lecturer}', [TechnicianController::class, 'destroyLecturerAPI'])->name('technician.destroy-lecturer-api');
    Route::post('/import-lecturer-api', [TechnicianController::class, 'importLecturerAPI'])->name('technician.import-lecturer-api');

    // Student
    Route::get('/get-list-student', [TechnicianController::class, 'getListStudent'])->name('technician.get-list-student')->middleware('check.role:technician');

    Route::get('/get-list-student-api', [TechnicianController::class, 'getListStudentAPI'])->name('technician.get-list-student-api');
    Route::post('/store-student-api', [TechnicianController::class, 'storeStudentAPI'])->name('technician.store-student-api');
    Route::put('/update-student-api/{student}', [TechnicianController::class, 'updateStudentAPI'])->name('technician.update-student-api');
    Route::delete('/delete-student-api/{student}', [TechnicianController::class, 'destroyStudentAPI'])->name('technician.destroy-student-api');
    Route::post('/import-student-api', [TechnicianController::class, 'importStudentAPI'])->name('technician.import-student-api');

    // Class
    Route::get('/get-list-class', [TechnicianController::class, 'getListClass'])->name('technician.get-list-class')->middleware('check.role:technician');

    Route::get('/get-list-class-api', [TechnicianController::class, 'getListClassAPI'])->name('technician.get-list-class-api');
    Route::get('/get-lesson-of-class-session-api/{class}', [TechnicianController::class, 'getLessonOfClassSessionAPI'])->name('technician.get-lesson-of-class-session-api');
    Route::post('/store-class-api', [TechnicianController::class, 'storeClassAPI'])->name('technician.store-class-api');
    Route::put('/update-class-api/{class}', [TechnicianController::class, 'updateClassAPI'])->name('technician.update-class-api');
    Route::put('/update-class-status/{class}', [TechnicianController::class, 'updateStatusClassAPI'])->name('technician.update-status-class-api');
    Route::delete('/delete-class-api/{class}', [TechnicianController::class, 'destroyClassAPI'])->name('technician.destroy-class-api');
    Route::post('/import-class-api', [TechnicianController::class, 'importClassAPI'])->name('technician.import-class-api');
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
