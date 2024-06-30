<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\LecturerController;
use App\Http\Controllers\TechnicianController;
use App\Http\Controllers\ManagerController;
use App\Http\Controllers\UserController;

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
Route::post('/login-api', [LoginController::class, 'loginAPI'])->middleware('guest')->name('login-api');

Route::post('/register', [UserController::class, 'registerAPI'])->name('register-api');
Route::post('/verification-email', [UserController::class, 'verificationEmailAPI'])->name('verification-email-api');

Route::post('/forgot-password-api', [UserController::class, 'forgotPasswordAPI'])->name('forgot-password-api');
Route::get('/reset-password/{token}', [UserController::class, 'resetPassword'])->name('reset-password');
Route::post('/reset-password-api', [UserController::class, 'resetPasswordAPI'])->name('reset-password-api');

Route::middleware(['auth', 'check.role:student'])->group(function () {
    Route::prefix('/student')->group(function () {
        Route::get('/', [StudentController::class, 'index'])->name('student.index')->middleware('check.role:student');
        Route::get('/get-list-class-session', [StudentController::class, 'getListClassSession'])->name('student.get-list-class-session')->middleware('check.role:student');
        Route::post('/get-class-session-api/{class}', [StudentController::class, 'getClassSessionAPI'])->name('student.get-class-session-api');
        Route::get('/get-class-session/{classSession}', [StudentController::class, 'getClassSession'])->name('student.get-class-session')->middleware('check.role:student');
        Route::post('/send-report-api/{computer}', [StudentController::class, 'sendReportAPI'])->name('student.send-report-api');
        Route::post('/attendance-api/{classSession}', [StudentController::class, 'attendanceAPI'])->name('student.attendance-api');
        Route::get('/get-list-class', [StudentController::class, 'getListClass'])->name('student.get-list-class')->middleware('check.role:student');
        Route::post('/join-class-api', [StudentController::class, 'joinClassAPI'])->name('student.join-class-api');
        Route::get('/get-personal-info', [StudentController::class, 'getPersonalInfo'])->name('student.get-personal-info')->middleware('check.role:student');
        Route::put('/update-personal-info-api/{student}', [StudentController::class, 'updatePersonalInfoAPI'])->name('student.update-personal-info-api');
        Route::put('/update-password-api/{user}', [StudentController::class, 'updatePasswordAPI'])->name('student.update-password-api');
    });
});

Route::middleware(['auth', 'check.role:lecturer'])->group(function () {
    Route::resource('/lecturer', LecturerController::class);
});

Route::middleware(['auth'])->group(function () {
   Route::prefix('/technician')->group(function () {
       Route::get('/', [TechnicianController::class, 'index'])->name('technician.index')->middleware('check.role:technician');
       // Lecturer
       Route::get('/get-list-lecturer', [TechnicianController::class, 'getListLecturer'])->name('technician.get-list-lecturer')->middleware('check.role:technician');

       Route::get('/get-list-lecturer-api', [TechnicianController::class, 'getListLecturerAPI'])->name('technician.get-list-lecturer-api');
       Route::post('/store-lecturer-api', [TechnicianController::class, 'storeLecturerAPI'])->name('technician.store-lecturer-api');
       Route::put('/update-lecturer-api/{lecturer}', [TechnicianController::class, 'updateLecturerAPI'])->name('technician.update-lecturer-api');
       Route::put('/update-password-lecturer-api/{lecturer}', [TechnicianController::class, 'updatePasswordLecturerAPI'])->name('technician.update-password-lecturer-api');
       Route::delete('/delete-lecturer-api/{lecturer}', [TechnicianController::class, 'destroyLecturerAPI'])->name('technician.destroy-lecturer-api');
       Route::post('/import-lecturer-api', [TechnicianController::class, 'importLecturerAPI'])->name('technician.import-lecturer-api');

       // Student
       Route::get('/get-list-student', [TechnicianController::class, 'getListStudent'])->name('technician.get-list-student')->middleware('check.role:technician');

       Route::get('/get-list-student-api', [TechnicianController::class, 'getListStudentAPI'])->name('technician.get-list-student-api');
       Route::post('/search-student-api', [TechnicianController::class, 'getStudentByStudentCodeAPI'])->name('technician.search-student-api');
       Route::post('/store-student-api', [TechnicianController::class, 'storeStudentAPI'])->name('technician.store-student-api');
       Route::put('/update-student-api/{student}', [TechnicianController::class, 'updateStudentAPI'])->name('technician.update-student-api');
       Route::put('/update-password-student-api/{student}', [TechnicianController::class, 'updatePasswordStudentAPI'])->name('technician.update-password-student-api');
       Route::delete('/delete-student-api/{student}', [TechnicianController::class, 'destroyStudentAPI'])->name('technician.destroy-student-api');
       Route::post('/import-student-api', [TechnicianController::class, 'importStudentAPI'])->name('technician.import-student-api');

       // Class
       Route::get('/get-list-class', [TechnicianController::class, 'getListClass'])->name('technician.get-list-class')->middleware('check.role:technician');
       Route::get('/get-list-student-class/{class}', [TechnicianController::class, 'getListStudentClass'])->name('technician.get-list-student-class')->middleware('check.role:technician');

       Route::get('/get-list-class-api', [TechnicianController::class, 'getListClassAPI'])->name('technician.get-list-class-api');
       Route::get('/get-lesson-of-class-session-api/{class}', [TechnicianController::class, 'getLessonOfClassSessionAPI'])->name('technician.get-lesson-of-class-session-api');
       Route::post('/store-class-api', [TechnicianController::class, 'storeClassAPI'])->name('technician.store-class-api');
       Route::post('/store-student-class-api', [TechnicianController::class, 'storeStudentClassAPI'])->name('technician.store-student-class-api');
       Route::put('/update-class-api/{class}', [TechnicianController::class, 'updateClassAPI'])->name('technician.update-class-api');
       Route::put('/update-class-status-api/{class}', [TechnicianController::class, 'updateStatusClassAPI'])->name('technician.update-status-class-api');
       Route::delete('/delete-class-api/{class}', [TechnicianController::class, 'destroyClassAPI'])->name('technician.destroy-class-api');
       Route::delete('/delete-student-class-api/{student}', [TechnicianController::class, 'destroyStudentClassAPI'])->name('technician.destroy-student-class-api');
       Route::post('/import-student-class-api', [TechnicianController::class, 'importStudentClassAPI'])->name('technician.import-student-class-api');

       // Building
       Route::get('/get-list-building', [TechnicianController::class, 'getListBuilding'])->name('technician.get-list-building')->middleware('check.role:technician');

       Route::get('/get-list-building-api', [TechnicianController::class, 'getListBuildingAPI'])->name('technician.get-list-building-api');
       Route::post('/store-building-api', [TechnicianController::class, 'storeBuildingAPI'])->name('technician.store-building-api');
       Route::put('/update-building-api/{building}', [TechnicianController::class, 'updateBuildingAPI'])->name('technician.update-building-api');
       Route::delete('/delete-building-api/{building}', [TechnicianController::class, 'destroyBuildingAPI'])->name('technician.destroy-building-api');

       // Room
       Route::get('/get-list-room/{building}', [TechnicianController::class, 'getListRoom'])->name('technician.get-list-room')->middleware('check.role:technician');

       Route::get('/get-list-room-api', [TechnicianController::class, 'getListRoomAPI'])->name('technician.get-list-room-api');
       Route::post('/store-room-api', [TechnicianController::class, 'storeRoomAPI'])->name('technician.store-room-api');
       Route::put('/update-room-api/{room}', [TechnicianController::class, 'updateRoomAPI'])->name('technician.update-room-api');
       Route::delete('/delete-room-api/{room}', [TechnicianController::class, 'destroyRoomAPI'])->name('technician.destroy-room-api');

       // Room
       Route::get('/get-list-computer/{room}', [TechnicianController::class, 'getListComputer'])->name('technician.get-list-computer')->middleware('check.role:technician');

       Route::get('/get-list-computer-api', [TechnicianController::class, 'getListComputerAPI'])->name('technician.get-list-computer-api');
       Route::post('/store-computer-api', [TechnicianController::class, 'storeComputerAPI'])->name('technician.store-computer-api');
       Route::put('/update-computer-api/{computer}', [TechnicianController::class, 'updateComputerAPI'])->name('technician.update-computer-api');
       Route::put('/start-maintenance-computer-api/{computer}', [TechnicianController::class, 'startMaintenanceClassAPI'])->name('technician.start-maintenance-computer-api');
       Route::put('/end-maintenance-computer-api/{computer}', [TechnicianController::class, 'endMaintenanceClassAPI'])->name('technician.end-maintenance-computer-api');
       Route::delete('/delete-computer-api/{computer}', [TechnicianController::class, 'destroyComputerAPI'])->name('technician.destroy-computer-api');
   });
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
