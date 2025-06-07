<?php


use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\OwnerController;
use App\Http\Controllers\AgentController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\OtpPassController;
use PHPUnit\Event\Telemetry\System;
use App\Http\Controllers\SystemController;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\AdminQuestionController;
use App\Http\Controllers\ChatbotController;
use App\Http\Controllers\CustomerController;

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

// Public routes - có thể truy cập không cần đăng nhập
Route::get('/search', [CustomerController::class, 'search'])->name('customer.search');

// Property listing routes
Route::get('/mua', [CustomerController::class, 'saleProperties'])->name('properties.sale');
Route::get('/mua/{propertyID}', [CustomerController::class, 'propertyDetail'])->name('properties.sale.detail');
Route::get('/cho-thue', [CustomerController::class, 'rentProperties'])->name('properties.rent');
Route::get('/cho-thue/{propertyID}', [CustomerController::class, 'propertyDetail'])->name('properties.rent.detail');

// Trang chủ - cho phép tất cả người dùng truy cập (đã đăng nhập hoặc chưa)
Route::get('/', [HomeController::class, 'index'])->name('home');

//Đăng Nhập
Route::get('/login',[LoginController::class,"login"])->name('login');
Route::post('/login', [LoginController::class, "authenticate"])->name('login.authenticate');

//Đăng Ký
Route::get('/register',[RegisterController::class,"register"])->name('register');
Route::post('/register',[RegisterController::class,"formRegister"])->name('register.submit');

// Reset Password Routes
Route::prefix('password')->name('password.')->group(function () {
    // Trang chọn phương thức reset
    Route::get('/reset', function() {
        return view('auth.resetpass');
    })->name('reset');

    // Form yêu cầu reset password (email)
    Route::get('/request', [OtpPassController::class, 'showResetRequestForm'])->name('request');
    Route::post('/send-otp-email', [OtpPassController::class, 'sendOtpEmail'])->name('send-otp-email');
    Route::post('/send-otp-sms', [OtpPassController::class, 'sendOtpSms'])->name('send-otp-sms');

    // Form xác thực OTP
    Route::get('/verify-otp', [OtpPassController::class, 'showVerifyOtpForm'])->name('verify-otp-form');
    Route::post('/verify-otp', [OtpPassController::class, 'verifyOtp'])->name('verify-otp');

    // Form đặt lại mật khẩu
    Route::get('/reset-password', [OtpPassController::class, 'showResetForm'])->name('reset-form');
    Route::post('/reset-password', [OtpPassController::class, 'resetPassword'])->name('password-reset');

    // API gửi lại OTP
    Route::post('/resend-otp', [OtpPassController::class, 'resendOtp'])->name('resend-otp');
});

Route::middleware(['auth'])->group(function()
    {
        // Routes dành cho Owner đã đăng nhập
        Route::middleware(['checkRole:Owner'])->prefix('owner')->name('owner.')->group(function () {
            Route::get('/dashboard', [OwnerController::class, 'dashboard'])->name('dashboard');
        });

        // Routes dành cho Agent đã đăng nhập
        Route::middleware(['checkRole:Agent'])->prefix('agent')->name('agent.')->group(function () {
            Route::get('/dashboard', [AgentController::class, 'dashboard'])->name('dashboard');
        });

        // Routes dành cho customer đã đăng nhập
        Route::middleware(['checkRole:Customer'])->prefix('customer')->name('customer.')->group(function () {
            Route::get('/', [CustomerController::class, 'index'])->name('home');
            // Profile routes
            Route::get('/profile', [CustomerController::class, 'showProfile'])->name('profile');
            Route::put('/profile', [CustomerController::class, 'updateProfile'])->name('profile.update');
            // Change password routes
            Route::get('/change-password', [CustomerController::class, 'changePassword'])->name('change-password');
            Route::post('/change-password', [CustomerController::class, 'updatePassword'])->name('updatePassword');
            Route::get('/appointments', [CustomerController::class, 'showAppointments'])->name('appointments.index');
            Route::post('/appointments/{id}/cancel', [CustomerController::class, 'cancelAppointment'])->name('appointments.cancel');

            // Transaction history routes
            Route::get('/transaction-history', [CustomerController::class, 'transactionHistory'])->name('transaction.history');
            Route::get('/transaction/{id}/detail', [CustomerController::class, 'transactionDetail'])->name('transaction.detail');
            Route::post('/payment/process', [CustomerController::class, 'processPayment'])->name('payment.process');

            // Customer document routes - secure access to own transaction documents
            Route::get('/document/view/{id}', [CustomerController::class, 'viewDocument'])->name('document.view');
            Route::get('/document/download/{id}', [CustomerController::class, 'downloadDocument'])->name('document.download');

            // Agent directory and feedback routes
            Route::get('/contact-agent', [CustomerController::class, 'contactAgent'])->name('contact-agent');
            Route::post('/submit-feedback', [CustomerController::class, 'submitFeedback'])->name('submit-feedback');

            // Notification routes
            Route::get('/notifications', [CustomerController::class, 'getNotifications'])->name('notifications');
            Route::post('/notifications/{id}/mark-as-read', [CustomerController::class, 'markNotificationAsRead'])->name('notifications.mark-read');
        });

        // Routes danh cho người dùng đã đăng nhập không cần phải liên quan đến quyền
        Route::get('/document/view/{id}', [SystemController::class, 'viewDocument'])->name('admin.document.view');
        Route::get('/document/download/{id}', [SystemController::class, 'downloadDocument'])->name('admin.document.download');

        // Routes dành cho người dùng đã đăng nhập có quyền là Admin


        Route::middleware(['checkRole:Admin'])->prefix('admin')->name('admin.')->group(function () {
            Route::get('/dashboard', [SystemController::class, 'admin'])->name('dashboard');

            // Route điều hướng đến trang quản lý bất động sản

            Route::get('/property', [SystemController::class, "getProperty"])->name('property'); //admin.property
            Route::get('/property/search', [SystemController::class, 'SearchProperty'])->name('property.search'); // admin.property.search #
            Route::get('/property/create', [SystemController::class, "createPropertyForm"])->name('property.create');
            Route::post('/property', [SystemController::class, 'createProperty'])->name('property.store');
            Route::post('/property/update-status', [SystemController::class, 'updatePropertyStatus'])->name('property.updateStatus');
            Route::post('/property/update-batch-status', [SystemController::class, 'updateBatchStatus'])->name('property.updateBatchStatus');

            // Routes với pattern cụ thể - phải đặt trước route với tham số động
            Route::get('/property/type/{type}', [SystemController::class, "getPropertyByTypeAndStatus"])->name('property.type.status');
            // Route::get('/property/type/{type}', [SystemController::class, "getPropertyByType"])->name('property.type');
            Route::get('/property/status/{status}', [SystemController::class, "getPropertyByStatus"])->name('property.status');
            Route::get('/property/{id}/edit', [SystemController::class, 'EditPropertyByStatus'])->name('property.edit');

            // Routes với tham số động - đặt sau các pattern cụ thể
            Route::delete('/property/{id}',[SystemController::class, 'deleteProperty'])->name('property.delete');
            Route::put('/property/{id}', [SystemController::class, 'EditPropertyByStatus'])->name('property.update');
            Route::get('/property/{id}', [SystemController::class, "getPropertyById"])->name('property.id');

            // Route cho việc gán và quản lý agent cho bất động sản
            Route::get('/assign-property', [SystemController::class, 'getAssignProperty'])->name('assign.property');
            Route::post('/assign-property', [SystemController::class, 'assignAgentToProperty'])->name('assign.property.store');
            Route::get('/check-agent-limit/{agentId}', [SystemController::class, 'checkAgentPropertyCount'])->name('check.agent.limit');
            Route::get('/agent/{id}/properties', [SystemController::class, 'getAgentProperties'])->name('agent.properties');
            Route::get('/available-properties', [SystemController::class, 'getAvailableProperties'])->name('available.properties');
            Route::post('/assign/property', [SystemController::class, 'assignProperties'])->name('assign.properties');
            Route::post('/unassign/property', [SystemController::class, 'unassignProperty'])->name('unassign.property');

            // Route điều hướng đến trang quản lý người dùng

            Route::get('/user', [SystemController::class, "getUser"])->name('users');
            Route::get('/user/create', [SystemController::class, "createUserForm"])->name('users.create');
            Route::post('/user/create', [SystemController::class, 'createUser'])->name('users.store');
            Route::delete('/user/{id}',[SystemController::class, 'deleteUser'])->name('users.delete');
            Route::get('/user/{id}/edit', [SystemController::class, 'editUserForm'])->name('users.edit');
            Route::put('/user/{id}', [SystemController::class, 'UpdateUser'])->name('users.update');
            Route::get('/user/role/{role}', [SystemController::class, 'getUserByRole'])->name('users.byRole');
            Route::get('/user/status/{status}', [SystemController::class, 'getUserByStatus'])->name('users.byStatus');
            Route::get('/user/role/{role}/search', [SystemController::class, 'SearchUser'])->name('users.search');

            // New enhanced user management routes
            Route::post('/users/{userId}/toggle-status', [SystemController::class, 'toggleUserStatus'])->name('users.toggleStatus');
            Route::get('/users/search', [SystemController::class, 'searchUsers'])->name('users.search.enhanced');

            // Profile management routes
            Route::post('/users/create-with-profile', [SystemController::class, 'createUserWithProfile'])->name('users.createWithProfile');
            Route::put('/users/{userId}/update-with-profile', [SystemController::class, 'updateUserWithProfile'])->name('users.updateWithProfile');
            Route::put('/users/{userId}/profile/{role}', [SystemController::class, 'updateRoleProfile'])->name('users.updateRoleProfile');
            Route::post('/users/{userId}/activate', [SystemController::class, 'activateUser'])->name('users.activate');

            // Route điều hướng đến trang quản lý lịch hẹn

            Route::get('/appointment', [SystemController::class, "getAppointment"])->name('appointment');
            Route::get('/appointment/search-by-date', [SystemController::class, "searchAppointmentByDate"])->name('appointment.search.date');
            Route::get('/appointment/search-by-range', [SystemController::class, "getAppointmentsByDateRange"])->name('appointment.search.range');
            Route::get('/appointment/agent/{agentId}', [SystemController::class, "getAppointmentsByAgent"])->name('appointment.byAgent');
            Route::get('/appointment/detail/{id}', [SystemController::class, "getAppointmentDetail"])->name('appointment.detail');
            Route::get('/appointment/{id}', [SystemController::class, "getAppointmentById"])->name('appointment.id');
            Route::delete('/appointment/{id}', [SystemController::class, "deleteAppointment"])->name('appointment.delete');


            // Transaction routes - ordered from most specific to general
            Route::put('/transaction/{transactionId}/payment-statuses', [SystemController::class, 'updatePaymentStatuses'])->name('transaction.updatePaymentStatuses');
            Route::post('/transaction/{transactionId}/add-payment', [SystemController::class, 'addPayment'])->name('transaction.addPayment');
            Route::post('/transaction/{id}/document', [SystemController::class, 'addDocument'])->name('transaction.addDocument');

            // New AJAX routes for enhanced transaction table
            Route::get('/transaction/{id}/details', [SystemController::class, 'getTransactionDetailsAjax'])->name('transaction.details.ajax');

            Route::get('/transaction/{id}', [SystemController::class, "getTransactionById"])->name('transaction.id');
            Route::delete('/transaction/{id}', [SystemController::class, 'deleteTransaction'])->name('transaction.delete');
            Route::get('/transaction', [SystemController::class, "getTransaction"])->name('transaction');

            // Contract template routes
            Route::get('/contract/preview/{filename}', [SystemController::class, 'previewContract'])->name('contract.preview');
            Route::get('/contract/print/{filename}', [SystemController::class, 'printContract'])->name('contract.print');
            Route::get('/contract/download/{filename}', [SystemController::class, 'downloadContract'])->name('contract.download');

            // Contract template management routes
            Route::post('/contracts/add', [SystemController::class, 'addContractTemplate'])->name('contracts.add');
            Route::post('/contracts/edit', [SystemController::class, 'editContractTemplate'])->name('contracts.edit');
            Route::post('/contracts/delete', [SystemController::class, 'deleteContractTemplateJson'])->name('contracts.delete');

            // Contract template upload routes
            Route::post('/contract/upload', [SystemController::class, 'uploadContractTemplate'])->name('contract.upload');
            Route::post('/contract/download-from-url', [SystemController::class, 'downloadContractFromUrl'])->name('contract.download-from-url');
            Route::delete('/contract/delete/{filename}', [SystemController::class, 'deleteContractTemplate'])->name('contract.delete');
            Route::get('/contract/templates/info', [SystemController::class, 'getContractTemplatesInfo'])->name('contract.templates.info');

            // Contract analysis route
            Route::post('/contract/analyze', [SystemController::class, 'analyzeContract'])->name('contract.analyze');

            // Document routes
            Route::delete('/document/{id}', [SystemController::class, 'deleteDocument'])->name('admin.document.delete');

            // Route điều hướng đến trang quản lý đánh giá khách hàng tới môi giới
            Route::get('/feedback', [SystemController::class, "getFeedback"])->name('feedback');
            Route::get('/feedback/filter', [SystemController::class, "getFeedbackByStatusRating"])->name('feedback.filter');
            Route::get('/feedback/search', [SystemController::class, "getFeedbackSearch"])->name('feedback.search');
            Route::get('/feedback/cancelled', [SystemController::class, "getCancelledFeedback"])->name('feedback.cancelled');



            Route::get('/feedback/{id}', [SystemController::class, "getFeedbackById"])->name('feedback.id');
            Route::patch('/feedback/{id}', [SystemController::class, "updateFeedback"])->name('feedback.update');
            Route::patch('/feedback/{id}/status', [SystemController::class, "updateFeedbackStatus"])->name('feedback.updateStatus');
            Route::delete('/feedback/{id}', [SystemController::class, "deleteFeedback"])->name('feedback.delete');

            // Route điều hướng đến trang quản lý hoa hồng
            Route::get('/commission', [SystemController::class, "getCommission"])->name('commission');

            // Routes cụ thể - phải đặt trước routes có tham số động
            Route::get('/commission/create', [SystemController::class, "createCommissionForm"])->name('commission.create');
            Route::post('/commission/create', [SystemController::class, 'createCommission'])->name('commission.store');
            Route::get('/commission/search', [SystemController::class, 'searchCommission'])->name('commission.search');
            Route::get('/commission/search-type', [SystemController::class, 'searchCommissionByDateAndType'])->name('commission.search.type');
            Route::get('/commission/view', [SystemController::class, 'viewCommissionModal'])->name('commission.view');
            Route::post('/commission/view-modal', [SystemController::class, 'viewCommissionModal'])->name('commission.view.modal');

            // Routes với tham số dynamic - đặt sau routes cụ thể
            Route::get('/commission/type/{type}', [SystemController::class, 'getCommissionByType'])->name('commission.type');
            Route::get('/commission/filter/{status}', [SystemController::class, 'getCommissionByStatus'])->name('commission.filter');
            Route::get('/commission/{id}', [SystemController::class, 'getCommissionById'])->name('commission.get');
            Route::get('/commission/{id}/edit', [SystemController::class, 'editCommissionForm'])->name('commission.edit');
            Route::put('/commission/{id}', [SystemController::class, 'updateCommission'])->name('commission.update');
            Route::delete('/commission/{id}', [SystemController::class, 'deleteCommission'])->name('commission.delete');

            // Route cho lấy dữ liệu thống kê Dashboard
            Route::get('/dashboard/monthly-stats', [SystemController::class, 'getMonthlyStats'])->name('dashboard.monthlyStats');

            // Routes cho tìm kiếm chủ sở hữu (autocomplete)
            Route::get('/owners/search', [SystemController::class, 'searchOwners'])->name('owners.search');
            Route::get('/owners/{id}', [SystemController::class, 'getOwnerDetails'])->name('owners.details');

            // Route quản lý chatbot
            Route::prefix('chatbot')->name('chatbot.')->group(function () {
                // Route demo
                Route::get('/demo', [ChatbotController::class, 'demo'])->name('demo');

                Route::prefix('questions')->name('questions.')->group(function () {
                    Route::get('/', [ChatbotController::class, 'index'])->name('index');
                    Route::get('/create', [ChatbotController::class, 'create'])->name('create');
                    Route::post('/store', [ChatbotController::class, 'store'])->name('store');
                    Route::get('/edit/{id}', [ChatbotController::class, 'edit'])->name('edit');
                    Route::put('/update/{id}', [ChatbotController::class, 'update'])->name('update');
                    Route::delete('/destroy/{id}', [ChatbotController::class, 'destroy'])->name('destroy');
                });

                // Route quản lý chat admin
                Route::get('/admin', [ChatbotController::class, 'index'])->name('admin');

                // Route gửi tin nhắn
                Route::post('/send-to-user', [ChatbotController::class, 'sendToUser'])->name('sendToUser');
                Route::post('/broadcast', [ChatbotController::class, 'sendBroadcast'])->name('broadcast');
                Route::get('/users-list', [ChatbotController::class, 'getUsersList'])->name('usersList');
                Route::get('/default-users', [ChatbotController::class, 'getDefaultUsers'])->name('defaultUsers');

                // Route quản lý FAQ
                Route::prefix('faq')->name('faq.')->group(function () {
                    Route::get('/', [ChatbotController::class, 'manageFAQ'])->name('index');
                    Route::post('/add', [ChatbotController::class, 'addFAQ'])->name('add');
                    Route::put('/update/{index}', [ChatbotController::class, 'updateFAQ'])->name('update');
                    Route::delete('/delete/{index}', [ChatbotController::class, 'deleteFAQ'])->name('delete');
                });
            });
        });



    // Route đăng xuất
    Route::post('/logout', function () {
        Auth::logout(); // Đăng xuất người dùng
        return redirect()->route('login'); // Chuyển hướng về trang chủ
    })->name('logout');

    Route::post('/chat/send', [ChatbotController::class, 'sendMessage']);

    // Route cho user chat (guest và user đã đăng nhập)
    Route::get('/chat', [ChatbotController::class, 'userChat'])->name('user.chat');            });

// Route API cho chatbot (sử dụng method answerChatbot)
Route::post('/api/chatbot', [ChatbotController::class, 'answerChatbot'])->name('chatbot.answer');

// Test routes cho Gemini AI - chỉ dùng trong development
Route::prefix('test')->name('test.')->group(function () {
    Route::get('/gemini', [ChatbotController::class, 'showTestPage'])->name('gemini');
    Route::post('/gemini/ask', [ChatbotController::class, 'testGemini'])->name('gemini.ask');
    Route::get('/gemini/faq', [ChatbotController::class, 'testFAQ'])->name('gemini.faq');
    Route::post('/gemini/full', [ChatbotController::class, 'testFullChatbot'])->name('gemini.full');

    // Database integration test routes
    Route::post('/gemini/database', [ChatbotController::class, 'testDatabaseSearch'])->name('gemini.database');
    Route::post('/gemini/smart-search', [ChatbotController::class, 'testSmartSearch'])->name('gemini.smartsearch');
    Route::get('/gemini/scenarios', [ChatbotController::class, 'testSearchScenarios'])->name('gemini.scenarios');
    Route::get('/gemini/db-connection', [ChatbotController::class, 'testDatabaseConnection'])->name('gemini.dbconnection');
    Route::get('/gemini/status', [ChatbotController::class, 'getDatabaseIntegrationStatus'])->name('gemini.status');
});

// Test route for commission modal
Route::get('/test-commission-modal', function () {
    return view('test_commission_modal');
});



