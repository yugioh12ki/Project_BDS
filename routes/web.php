<?php


use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\OwnerController;
use App\Http\Controllers\AgentController;
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

// Route::middleware(['auth'])->group(function()
// {

//     Route::get('/admin', [SystemController::class, 'admin'])->name('admin.dashboard');
//     Route::get('/admin/property', [SystemController::class, "getProperty"])->name('admin.property');
//     Route::get('/admin/user', [SystemController::class, "getUser"])->name('admin.users');
//     Route::get('/admin/user/create', [SystemController::class, "createUserForm"])->name('admin.users.create');
//     Route::post('/admin/user/create', [SystemController::class, 'createUser'])->name('admin.users.store');
//     Route::get('/admin/user/role/{role}', [SystemController::class, 'getUserByRole'])->name('admin.users.byRole');
//     Route::get('/admin/appointment', [SystemController::class, "getAppointment"])->name('admin.appointment');
//     Route::get('/admin/transaction', [SystemController::class, "getTransaction"])->name('admin.transaction');
//     Route::get('/admin/feedback', [SystemController::class, "getFeedback"])->name('admin.feedback');
//     Route::get('/admin/commission', [SystemController::class, "getCommission"])->name('admin.commission');

//     Route::get('/owner', [OwnerController::class, 'dashboard'])->name('owner.dashboard');
//     Route::get('/owner/property', [OwnerController::class, 'listProperty'])->name('owner.property.index');
//     Route::get('/owner/property/create', [OwnerController::class, 'createProperty'])->name('owner.property.create');
//     Route::post('/owner/property', [OwnerController::class, 'storeProperty'])->name('owner.property.store');
//     Route::get('/owner/appointments', [OwnerController::class, 'appointments'])->name('owner.appointments.index');
//     Route::get('/owner/transactions', [OwnerController::class, 'transactions'])->name('owner.transactions.index');

//     // Route đăng xuất
//     Route::post('/logout', function () {
//         Auth::logout(); // Đăng xuất người dùng
//         return redirect()->route('login'); // Chuyển hướng về trang chủ
//     })->name('logout');
// });

Route::middleware(['auth'])->group(function () {

    // Admin routes
    Route::middleware(['checkRole:Admin'])->prefix('admin')->name('admin.')->group(function() {
        Route::get('/', [SystemController::class, 'admin'])->name('dashboard');
        Route::get('/property', [SystemController::class, "getProperty"])->name('property');
        Route::get('/user', [SystemController::class, "getUser"])->name('users');
        Route::get('/user/create', [SystemController::class, "createUserForm"])->name('users.create');
        Route::post('/user/create', [SystemController::class, 'createUser'])->name('users.store');
        Route::get('/user/role/{role}', [SystemController::class, 'getUserByRole'])->name('users.byRole');
        Route::get('/appointment', [SystemController::class, "getAppointment"])->name('appointment');
        Route::get('/transaction', [SystemController::class, "getTransaction"])->name('transaction');
        Route::get('/feedback', [SystemController::class, "getFeedback"])->name('feedback');
        Route::get('/commission', [SystemController::class, "getCommission"])->name('commission');
    });

    // Owner routes
    Route::middleware(['checkRole:Owner'])->prefix('owner')->name('owner.')->group(function () {
        Route::get('/', [OwnerController::class, 'dashboard'])->name('dashboard');
        Route::get('/property', [OwnerController::class, 'listProperty'])->name('property.index');
        Route::get('/property/create', [OwnerController::class, 'createProperty'])->name('property.create');
        Route::post('/property', [OwnerController::class, 'storeProperty'])->name('property.store');
        Route::get('/appointments', [OwnerController::class, 'appointments'])->name('appointments.index');
        Route::get('/transactions', [OwnerController::class, 'transactions'])->name('transactions.index');
    });

    // Giả sử thêm 2 quyền nữa (ví dụ: Agent và Customer)
    Route::middleware(['checkRole:Agent'])->prefix('agent')->name('agent.')->group(function () {
        Route::get('/', [AgentController::class, 'dashboard'])->name('dashboard');
        // các route khác của agent...
    });

    // Route::middleware(['checkrole:customer'])->prefix('customer')->name('customer.')->group(function () {
    //     Route::get('/', [CustomerController::class, 'dashboard'])->name('dashboard');
    //     // các route khác của customer...
    // });

    // Route đăng xuất (áp dụng chung cho tất cả quyền)
    Route::post('/logout', function () {
        Auth::logout();
        return redirect()->route('login');
    })->name('logout');

});
