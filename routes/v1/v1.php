<?php

use App\Http\Controllers\v1\Export\HourSessionPdfExportController;
use App\Http\Controllers\v1\Auth\LoginController;
use App\Http\Controllers\v1\Auth\LogOutController;
use App\Http\Controllers\v1\DashBoard\DashboardController;
use App\Http\Controllers\v1\Employee\RegisterEmployeeController;
use App\Http\Controllers\v1\Employee\ShowEmployeeController;
use App\Http\Controllers\v1\Employee\UpdateEmployeeController;
use App\Http\Controllers\v1\Export\HourSessionExportCsvController;
use App\Http\Controllers\v1\HourSession\DeleteHourSession\DeleteHourSessionController;
use App\Http\Controllers\v1\HourSession\RegisterHourSession\RegitsterHourSessionController;
use App\Http\Controllers\v1\HourSession\ShowHourSession\ShowHourSessionController;
use App\Http\Controllers\v1\HourSession\UpdateHourSession\UpdateHourSessionController;
use App\Http\Controllers\v1\Salary\ShowSalaryByMonthController;

use App\Http\Controllers\v1\User\ChangePasswordController;
use App\Http\Controllers\v1\User\DeleteUserController;
use App\Http\Controllers\v1\User\ShowUserController;
use App\Http\Controllers\v1\User\UpdateEmailController;

use Illuminate\Support\Facades\Route;

Route::post('/register', RegisterEmployeeController::class)->middleware('throttle:login')->name('register');
Route::post('/login', LoginController::class)->middleware(['throttle:login', 'ip_block'])->name('login');




Route::middleware(['ip_block', 'throttle:global', 'jwt.auth', 'token_redis', 'role:employee', 'is_active'])->group(function () {
  
    //User Routes
    Route::put('/user/update', UpdateEmailController::class)->name('update_email');
    Route::get('/user/show', ShowUserController::class)->name('show_user');
    Route::post('user/delete', DeleteUserController::class)->name('delete_user');
    Route::put('/user/change_password', ChangePasswordController::class)->name('change_password');
    //Employee Routes
    Route::get('/employee', ShowEmployeeController::class)->name('show_employee');
    Route::put('/employee', UpdateEmployeeController::class)->name('update_employee');

    //HourSession Routes
    Route::post('/hour_session', RegitsterHourSessionController::class)->name('register_hour_session');  // Crear
    Route::get('/hour_session', ShowHourSessionController::class)->name('show_hour_session');       // Leer
    Route::put('/hour_session', UpdateHourSessionController::class)->name('update_hour_session');     // Actualizar
    Route::delete('/hour_session', DeleteHourSessionController::class)->name('delete_hour_session');  // Eliminar

    Route::get('/dashboard', DashboardController::class)->name('dashboard');

    //Salary Routes
    Route::get('/salary', ShowSalaryByMonthController::class)->name('show_salary_by_month');
    Route::get('/export/csv', HourSessionExportCsvController::class)->name('export_csv');
    Route::get('/export/pdf', HourSessionPdfExportController::class)->name('export_pdf');
    //Logout
    Route::post('/logout', LogOutController::class)->name('logout');

});
