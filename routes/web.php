<?php


use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\CustomerController;
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

//     // Route điều hướng đến trang quản lý bất động sản

//     Route::get('/admin/property', [SystemController::class, "getProperty"])->name('admin.property');
//     Route::get('/admin/property/type/{type}', [SystemController::class, "getPropertyByType"])->name('admin.property.type');
//     Route::get('/admin/property/create', [SystemController::class, "createPropertyForm"])->name('admin.property.create');
//     Route::post('/admin/property/create', [SystemController::class, 'createProperty'])->name('admin.property.store');
//     Route::delete('/admin/property/{id}',[SystemController::class, 'deleteProperty'])->name('admin.property.delete');
//     Route::get('/admin/property/{id}/edit', [SystemController::class, 'EditProperty'])->name('admin.property.edit');
//     Route::put('/admin/property/{id}', [SystemController::class, 'EditProperty'])->name('admin.property.update');
//     Route::get('/admin/property/search', [SystemController::class, 'SearchProperty'])->name('admin.property.search');

//     // Route điều hướng đến trang quản lý người dùng

//     Route::get('/admin/user', [SystemController::class, "getUser"])->name('admin.users');
//     Route::get('/admin/user/create', [SystemController::class, "createUserForm"])->name('admin.users.create');
//     Route::post('/admin/user/create', [SystemController::class, 'createUser'])->name('admin.users.store');
//     Route::delete('/admin/user/{id}',[SystemController::class, 'deleteUser'])->name('admin.users.delete');
//     Route::get('/admin/user/{id}/edit', [SystemController::class, 'EditUser'])->name('admin.users.edit');
//     Route::put('/admin/user/{id}', [SystemController::class, 'EditUser'])->name('admin.users.update');
//     Route::get('/admin/user/role/{role}', [SystemController::class, 'getUserByRole'])->name('admin.users.byRole');
//     Route::get('/admin/user/search', [SystemController::class, 'SearchUser'])->name('admin.users.search');

//     // Route điều hướng đến trang quản lý lịch hẹn

//     Route::get('/admin/appointment', [SystemController::class, "getAppointment"])->name('admin.appointment');
//     Route::get('/admin/transaction', [SystemController::class, "getTransaction"])->name('admin.transaction');
//     Route::get('/admin/feedback', [SystemController::class, "getFeedback"])->name('admin.feedback');
//     Route::get('/admin/commission', [SystemController::class, "getCommission"])->name('admin.commission');

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
    // Route::middleware(['checkRole:Owner'])->prefix('owner')->name('owner.')->group(function () {
    //     Route::get('/', [OwnerController::class, 'dashboard'])->name('dashboard');
    //     Route::get('/property', [OwnerController::class, 'listProperty'])->name('property.index');
    //     Route::get('/property/create', [OwnerController::class, 'createProperty'])->name('property.create');
    //     Route::post('/property', [OwnerController::class, 'storeProperty'])->name('property.store');
    //     Route::get('/appointments', [OwnerController::class, 'appointments'])->name('appointments.index');
    //     Route::get('/transactions', [OwnerController::class, 'transactions'])->name('transactions.index');
    // });

    // Giả sử thêm 2 quyền nữa (ví dụ: Agent và Customer)
    // Route::middleware(['checkRole:Agent'])->prefix('agent')->name('agent.')->group(function () {
    //     Route::get('/', [AgentController::class, 'dashboard'])->name('dashboard');
    //     // các route khác của agent...
    // });

    Route::middleware(['checkRole:Customer'])->prefix('customer')->name('customer.')->group(function () {
        Route::get('/', [CustomerController::class, 'index'])->name('home');
        // Profile routes
        Route::get('/profile', [CustomerController::class, 'showProfile'])->name('profile');
        Route::put('/profile', [CustomerController::class, 'updateProfile'])->name('profile.update');
        // Change password routes
        Route::get('/change-password', [CustomerController::class, 'showChangePasswordForm'])->name('change-password');
        Route::post('/change-password', [CustomerController::class, 'changePassword'])->name('password.change');
    });

    // Route đăng xuất (áp dụng chung cho tất cả quyền)
    Route::post('/logout', function () {
        Auth::logout();
        return redirect()->route('login');
    })->name('logout');

});

// Route tìm kiếm bất động sản
Route::get('/search', [App\Http\Controllers\CustomerController::class, 'search'])->name('customer.search');

// Route chi tiết bất động sản
Route::get('/property/{id}', [App\Http\Controllers\CustomerController::class, 'propertyDetail'])->name('property.detail');
