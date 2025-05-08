<?php


use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\RegisterController;
use PHPUnit\Event\Telemetry\System;
use App\Http\Controllers\SystemController;
use Illuminate\Support\Facades\Auth;

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

// Route::get('/', function () {
//     return view('welcome');
// });

//Trang chủ
Route::get('/',[HomeController::class,"index"])->name('home');

//Đăng Nhập
Route::get('/login',[LoginController::class,"login"])->name('login');
Route::post('/login', [LoginController::class, "authenticate"])->name('login.authenticate');

//Đăng Ký
Route::get('/register',[RegisterController::class,"register"])->name('register');

// Route::prefix('admin')->group(
//     function () {
//         Route::get('/dashboard', [SystemController::class, "admin"])->name('admin.dashboard');
//         Route::get('/property', [SystemController::class, "getProperty"])->name('property');
//         Route::get('/user', [SystemController::class, "getUser"])->name('users');
//         Route::get('/user/role/{role}', [SystemController::class, 'getUserByRole'])->name('users.byRole');
//         Route::get('/appointment', [SystemController::class, "getAppointment"])->name('appointment');
//         Route::get('/transaction', [SystemController::class, "getTransaction"])->name('transaction');
//         Route::get('/feedback', [SystemController::class, "getFeedback"])->name('feedback');
//         Route::get('/commission', [SystemController::class, "getCommission"])->name('commission');
//     }
// );

Route::middleware(['auth'])->group(function()
{

    Route::get('/admin', [SystemController::class, 'admin'])->name('admin.dashboard');
    Route::get('/admin/property', [SystemController::class, "getProperty"])->name('admin.property');
    Route::get('/admin/user', [SystemController::class, "getUser"])->name('admin.users');
    Route::get('/admin/user/role/{role}', [SystemController::class, 'getUserByRole'])->name('admin.users.byRole');
    Route::get('/admin/appointment', [SystemController::class, "getAppointment"])->name('admin.appointment');
    Route::get('/admin/transaction', [SystemController::class, "getTransaction"])->name('admin.transaction');
    Route::get('/admin/feedback', [SystemController::class, "getFeedback"])->name('admin.feedback');
    Route::get('/admin/commission', [SystemController::class, "getCommission"])->name('admin.commission');

    // Route đăng xuất
    Route::post('/logout', function () {
        Auth::logout(); // Đăng xuất người dùng
        return redirect()->route('home'); // Chuyển hướng về trang chủ
    })->name('logout');
});
