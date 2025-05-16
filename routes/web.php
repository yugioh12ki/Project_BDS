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

    Route::middleware(['checkRole:Admin'])->prefix('admin')->name('admin.')->group(function () {
        Route::get('/admin', [SystemController::class, 'admin'])->name('dashboard');

        // Route điều hướng đến trang quản lý bất động sản

        Route::get('/property', [SystemController::class, "getProperty"])->name('property');
        Route::get('/property/{id}', [SystemController::class, "getPropertyById"])->name('property.id');
        Route::get('/property/type/{type}', [SystemController::class, "getPropertyByType"])->name('property.type');
        Route::get('/property/status/{status}', [SystemController::class, "getPropertyByStatus"])->name('property.status');
        Route::get('/property/create', [SystemController::class, "createPropertyForm"])->name('property.create');
        Route::post('/property/create', [SystemController::class, 'createProperty'])->name('property.store');
        Route::delete('/property/{id}',[SystemController::class, 'deleteProperty'])->name('property.delete');
        Route::get('/property/{id}/edit', [SystemController::class, 'EditPropertyByStatus'])->name('property.edit');
        Route::put('/property/{id}', [SystemController::class, 'EditPropertyByStatus'])->name('property.update');
        Route::get('/property/search', [SystemController::class, 'SearchProperty'])->name('property.search');

        // Route điều hướng đến trang quản lý người dùng

        Route::get('/user', [SystemController::class, "getUser"])->name('users');
        Route::get('/user/create', [SystemController::class, "createUserForm"])->name('.users.create');
        Route::post('/user/create', [SystemController::class, 'createUser'])->name('users.store');
        Route::delete('/user/{id}',[SystemController::class, 'deleteUser'])->name('users.delete');
        Route::get('/user/{id}/edit', [SystemController::class, 'editUserForm'])->name('users.edit');
        Route::put('/user/{id}', [SystemController::class, 'UpdateUser'])->name('users.update');
        Route::get('/user/role/{role}', [SystemController::class, 'getUserByRole'])->name('users.byRole');
        Route::get('/user/status/{status}', [SystemController::class, 'getUserByStatus'])->name('users.byStatus');
        Route::get('/user/search', [SystemController::class, 'SearchUser'])->name('users.search');

        // Route điều hướng đến trang quản lý lịch hẹn

        Route::get('/appointment', [SystemController::class, "getAppointment"])->name('appointment');
        Route::get('/transaction', [SystemController::class, "getTransaction"])->name('transaction');


        // Route điều hướng đến trang quản lý đánh giá khách hàng tới môi giới
        Route::get('/feedback', [SystemController::class, "getFeedback"])->name('feedback');
        Route::get('/feedback/filter', [SystemController::class, "getFeedbackByStatusRating"])->name('feedback.filter');
        Route::get('/feedback/{id}', [SystemController::class, "getFeedbackById"])->name('feedback.id');
        Route::patch('/feedback/{id}', [SystemController::class, "updateFeedback"])->name('feedback.update');
        Route::delete('/feedback/{id}', [SystemController::class, "deleteFeedback"])->name('feedback.delete');

        // Route điều hướng đến trang quản lý hoa hồng
        Route::get('/commission', [SystemController::class, "getCommission"])->name('commission');

    });






    // Route đăng xuất
    Route::post('/logout', function () {
        Auth::logout(); // Đăng xuất người dùng
        return redirect()->route('login'); // Chuyển hướng về trang chủ
    })->name('logout');
});
