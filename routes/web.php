<?php

use Illuminate\Support\Facades\Route;

//Controllers
use App\Http\Controllers\AuthController;
use App\Http\Controllers\VehicleController;
use App\Http\Controllers\ReportController; 

//Admin Controllers
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\VehicleOwnerController;
use App\Http\Controllers\Admin\VehicleTableController;
use App\Http\Controllers\Admin\RfidController;
use App\Http\Controllers\Admin\LogController;
use App\Http\Controllers\Admin\GuardAccountController; 

//Guard Controllers
use App\Http\Controllers\Guard\GuardController;

Route::get('/', function () {
    return redirect()->route('login');
});

//AUTHENTICATION
Route::get('/login/{role?}', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

//Password Reset
Route::get('/forget-password', [AuthController::class, 'forgetPassword'])->name('forget_password');
Route::post('/forget-password', [AuthController::class, 'forgetPasswordSubmit'])->name('forget_password_submit');
Route::get('/reset-password/{token}/{email}', [AuthController::class, 'resetPassword'])->name('reset_password');
Route::post('/reset-password/{token}/{email}', [AuthController::class, 'resetPasswordSubmit'])->name('reset_password_submit');

//ADMIN SIGNUP
Route::get('/admin/signup', [AuthController::class, 'showAdminSignup'])->name('admin.signup');
Route::post('/admin/signup', [AuthController::class, 'createAdmin'])->name('admin.signup.submit');

//ADMIN ROUTES
Route::middleware(['role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    
    //Resource Management
    Route::resource('vehicle_owners', VehicleOwnerController::class);
    Route::resource('vehicles', VehicleTableController::class);
    Route::resource('rfids', RfidController::class);
    
    //Logs
    Route::get('/logs', [LogController::class, 'index'])->name('logs.index');
    Route::delete('/logs/{id}', [LogController::class, 'destroy'])->name('logs.destroy');

    //Manage Guards
    Route::get('/guards', [GuardAccountController::class, 'index'])->name('guards.index'); 
    Route::get('/guards/create', [GuardAccountController::class, 'create'])->name('guards.create');
    Route::post('/guards', [GuardAccountController::class, 'store'])->name('guards.store');
    Route::delete('/guards/{id}', [GuardAccountController::class, 'destroy'])->name('guards.destroy');

    //View Reports
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
});


//GUARD ROUTES
Route::middleware(['role:guard'])->group(function () {
    //Main Dashboard
    Route::get('/guard/dashboard', [GuardController::class, 'dashboard'])->name('guard_dashboard');  
    //Submit Report
    Route::post('/guard/report', [ReportController::class, 'store'])->name('guard.report');
    //RFID SCAN
    Route::post('/guard/rfid/scan', [GuardController::class, 'scanRfid'])->name('guard.rfid.scan');
    //SELECT
    Route::post('/guard/rfid/select', [GuardController::class, 'selectVehicleLog'])->name('guard.rfid.select');
});


//LIVE DETECTION API
Route::get('/vehicle-detection/live', [VehicleController::class, 'liveDetection'])->name('vehicle_detect_live');

