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

        // Routes danh cho người dùng đã đăng nhập không cần phải liên quan đến quyền
        Route::get('/document/view/{id}', [SystemController::class, 'viewDocument'])->name('admin.document.view');
        Route::get('/document/download/{id}', [SystemController::class, 'downloadDocument'])->name('admin.document.download');


        // Routes dành cho người dùng đã đăng nhập có quyền là Admin


        Route::middleware(['checkRole:Admin'])->prefix('admin')->name('admin.')->group(function () {
        Route::get('/admin', [SystemController::class, 'admin'])->name('dashboard');

        // Route điều hướng đến trang quản lý bất động sản

        Route::get('/property', [SystemController::class, "getProperty"])->name('property');
        Route::get('/property/search', [SystemController::class, 'SearchProperty'])->name('property.search');
        Route::get('/property/{id}', [SystemController::class, "getPropertyById"])->name('property.id');
        Route::get('/property/type/{type}', [SystemController::class, "getPropertyByType"])->name('property.type');
        Route::get('/property/status/{status}', [SystemController::class, "getPropertyByStatus"])->name('property.status');
        Route::get('/property/create', [SystemController::class, "createPropertyForm"])->name('property.create');
        Route::post('/property/create', [SystemController::class, 'createProperty'])->name('property.store');
        Route::delete('/property/{id}',[SystemController::class, 'deleteProperty'])->name('property.delete');
        Route::get('/property/{id}/edit', [SystemController::class, 'EditPropertyByStatus'])->name('property.edit');
        Route::put('/property/{id}', [SystemController::class, 'EditPropertyByStatus'])->name('property.update');

        // Route cho việc gán và quản lý agent cho bất động sản
        Route::get('/assign-property', [SystemController::class, 'getAssignProperty'])->name('assign.property');
        Route::post('/assign-property', [SystemController::class, 'assignAgentToProperty'])->name('assign.property.store');
        Route::get('/check-agent-limit/{agentId}', [SystemController::class, 'checkAgentPropertyCount'])->name('check.agent.limit');

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
        Route::get('/appointment/search-by-date', [SystemController::class, "searchAppointmentByDate"])->name('appointment.search.date');
        Route::get('/appointment/{id}', [SystemController::class, "getAppointmentById"])->name('appointment.id');
        Route::delete('/appointment/{id}', [SystemController::class, "deleteAppointment"])->name('appointment.delete');


        // Transaction routes - ordered from most specific to general
        Route::put('/transaction/{transactionId}/payment-statuses', [SystemController::class, 'updatePaymentStatuses'])->name('transaction.updatePaymentStatuses');
        Route::post('/transaction/{transactionId}/add-payment', [SystemController::class, 'addPayment'])->name('transaction.addPayment');
        Route::post('/transaction/{id}/document', [SystemController::class, 'addDocument'])->name('transaction.addDocument');
        Route::get('/transaction/{id}', [SystemController::class, "getTransactionById"])->name('transaction.id');
        Route::delete('/transaction/{id}', [SystemController::class, 'deleteTransaction'])->name('transaction.delete');
        Route::get('/transaction', [SystemController::class, "getTransaction"])->name('transaction');

        // Document routes
        Route::delete('/document/{id}', [SystemController::class, 'deleteDocument'])->name('admin.document.delete');

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
