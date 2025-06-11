<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\OtpPassController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\OwnerController;
use App\Http\Controllers\AgentController;
use App\Http\Controllers\SystemController;
use App\Http\Controllers\ChatbotController;

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

/*
|--------------------------------------------------------------------------
| Public Routes (No Authentication Required)
|--------------------------------------------------------------------------
*/

// Home page
Route::get('/', [HomeController::class, 'index'])->name('home');

// Property listing routes for public
Route::get('/search', [CustomerController::class, 'search'])->name('customer.search');
Route::get('/mua', [CustomerController::class, 'saleProperties'])->name('properties.sale');
Route::get('/mua/{propertyID}', [CustomerController::class, 'propertyDetail'])->name('properties.sale.detail');
Route::get('/cho-thue', [CustomerController::class, 'rentProperties'])->name('properties.rent');
Route::get('/cho-thue/{propertyID}', [CustomerController::class, 'propertyDetail'])->name('properties.rent.detail');

// Authentication routes
Route::get('/login', [LoginController::class, 'login'])->name('login');
Route::post('/login', [LoginController::class, 'authenticate'])->name('login.authenticate');
Route::get('/register', [RegisterController::class, 'register'])->name('register');
Route::post('/register', [RegisterController::class, 'formRegister'])->name('register.submit');

// Password reset routes
Route::prefix('password')->name('password.')->group(function () {
    Route::get('/reset', function() {
        return view('auth.resetpass');
    })->name('reset');
    Route::get('/request', [OtpPassController::class, 'showResetRequestForm'])->name('request');
    Route::post('/send-otp-email', [OtpPassController::class, 'sendOtpEmail'])->name('send-otp-email');
    Route::post('/send-otp-sms', [OtpPassController::class, 'sendOtpSms'])->name('send-otp-sms');
    Route::get('/verify-otp', [OtpPassController::class, 'showVerifyOtpForm'])->name('verify-otp-form');
    Route::post('/verify-otp', [OtpPassController::class, 'verifyOtp'])->name('verify-otp');
    Route::get('/reset-password', [OtpPassController::class, 'showResetForm'])->name('reset-form');
    Route::post('/reset-password', [OtpPassController::class, 'resetPassword'])->name('password-reset');
    Route::post('/resend-otp', [OtpPassController::class, 'resendOtp'])->name('resend-otp');
});

// Chatbot API routes (public)
Route::post('/api/chatbot', [ChatbotController::class, 'answerChatbot'])->name('chatbot.answer');

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



// Demo route - không cần auth
Route::get('/property-cards-demo', function () {
    // Tạo dữ liệu mẫu cho card BĐS
    $properties = collect([
        (object)[
            'PropertyID' => 'P001',
            'Title' => 'Căn hộ cao cấp, Trung tâm',
            'Address' => '123 Nguyễn Huệ',
            'Ward' => 'Quận 1',
            'District' => 'Quận 1',
            'Province' => 'TP.HCM',
            'Price' => 5200000000,
            'TypePro' => 'Sale',
            'images' => collect([
                (object)['ImageURL' => 'https://via.placeholder.com/600x400', 'IsThumbnail' => 1]
            ]),
            'danhMuc' => (object)['ten_pro' => 'Căn hộ'],
            'chiTiet' => (object)['Area' => 85, 'Bedroom' => 2, 'Bath_WC' => 2]
        ],
        (object)[
            'PropertyID' => 'P002',
            'Title' => 'Nhà phố liền kề',
            'Address' => '456 Lê Văn Lương',
            'Ward' => 'Quận 7',
            'District' => 'Quận 7',
            'Province' => 'TP.HCM',
            'Price' => 35000000,
            'TypePro' => 'Rent',
            'images' => collect([
                (object)['ImageURL' => 'https://via.placeholder.com/600x400', 'IsThumbnail' => 1]
            ]),
            'danhMuc' => (object)['ten_pro' => 'Nhà phố'],
            'chiTiet' => (object)['Area' => 120, 'Bedroom' => 3, 'Bath_WC' => 3]
        ],
        (object)[
            'PropertyID' => 'P003',
            'Title' => 'Biệt thự view sông',
            'Address' => '789 Nguyễn Văn Linh',
            'Ward' => 'Quận 7',
            'District' => 'Quận 7',
            'Province' => 'TP.HCM',
            'Price' => 25000000000,
            'TypePro' => 'Sale',
            'images' => collect([
                (object)['ImageURL' => 'https://via.placeholder.com/600x400', 'IsThumbnail' => 1]
            ]),
            'danhMuc' => (object)['ten_pro' => 'Biệt thự'],
            'chiTiet' => (object)['Area' => 350, 'Bedroom' => 5, 'Bath_WC' => 6]
        ]
    ]);

    $categories = collect([
        (object)['Protype_ID' => 1, 'ten_pro' => 'Căn hộ'],
        (object)['Protype_ID' => 2, 'ten_pro' => 'Nhà phố'],
        (object)['Protype_ID' => 3, 'ten_pro' => 'Biệt thự']
    ]);

    $owners = collect([]);

    return view('owners.property.index', compact('properties', 'categories', 'owners'));
});

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
        Route::get('/dashboard', [OwnerController::class, 'dashboard'])->name('dashboard');
        Route::get('/property', [OwnerController::class, 'listProperty'])->name('property.index');
        // Route::get('/property/create', [OwnerController::class, 'createPropertyForm'])->name('property.create');
        // Route::post('/property', [OwnerController::class, 'createProperty'])->name('property.store');
        // Route::get('/property/get-for-listing', [OwnerController::class, 'getPropertiesForListing'])->name('property.get-for-listing');
        Route::post('/property/listings', [OwnerController::class, 'storePropertyListing'])->name('property.listings.store');
        Route::get('/appointments', [OwnerController::class, 'appointments'])->name('appointments.index');
        Route::get('/appointments/filter/{status}', [OwnerController::class, 'getAppointmentsByStatus'])->name('appointments.filter');
        Route::get('/appointments/{id}/detail', [OwnerController::class, 'getAppointmentDetail'])->name('appointments.detail');
        Route::post('/appointments/{id}/confirm', [OwnerController::class, 'confirmAppointment'])->name('appointments.confirm');
        Route::post('/appointments/{id}/cancel', [OwnerController::class, 'cancelAppointment'])->name('appointments.cancel');
        Route::post('/appointments/{id}/finish', [OwnerController::class, 'finishAppointment'])->name('appointments.finish');

        // Transaction routes
        Route::get('/transactions', [OwnerController::class, 'transactions'])->name('transactions.index');
        Route::get('/transactions/overview', [OwnerController::class, 'transactionsOverview'])->name('transactions.overview');
        Route::get('/transactions/history', [OwnerController::class, 'transactionHistory'])->name('transactions.history');
        Route::get('/transactions/revenue', [OwnerController::class, 'revenueManagement'])->name('transactions.revenue');
        Route::get('/transactions/commissions', [OwnerController::class, 'commissionManagement'])->name('transactions.commissions');
        Route::post('/transactions/commission/{id}', [OwnerController::class, 'updateCommissionStatus'])->name('transactions.commission.update');
        Route::post('/transactions/create-commission', [OwnerController::class, 'createCommission'])->name('transactions.create-commission');
        Route::post('/transactions/pay-commission', [OwnerController::class, 'payCommission'])->name('transactions.pay-commission');
        Route::get('/transactions/commission-invoice/{id}', [OwnerController::class, 'commissionInvoice'])->name('transactions.commission-invoice');
        Route::get('/transactions/export', [OwnerController::class, 'exportTransactions'])->name('transactions.export');
        Route::get('/transactions/{id}/detail', [OwnerController::class, 'transactionDetail'])->name('transactions.detail');
        Route::get('/transactions/{id}', [OwnerController::class, 'showTransaction'])->name('transactions.show');
        Route::get('/transactions/{id}/print', [OwnerController::class, 'printInvoice'])->name('transactions.print');

        // Notifications route
        Route::post('/appointments/update-status/{id}', [OwnerController::class, 'updateAppointmentStatus'])->name('appointment.updateStatus');
        Route::get('/notifications-agent', [OwnerController::class, 'getNotifications'])->name('notifications-agent');
        Route::get('/notifications-owner', [OwnerController::class, 'getNotifications'])->name('notifications-owner');

        // Profile and Password routes
        Route::get('/profile', [OwnerController::class, 'showProfile'])->name('profile');
        Route::post('/profile', [OwnerController::class, 'updateProfile'])->name('profile.update');
        Route::get('/change-password', [OwnerController::class, 'showChangePasswordForm'])->name('change-password');
        Route::post('/change-password', [OwnerController::class, 'changePassword'])->name('change-password.update');
    });

    // Agent routes
    Route::middleware(['checkRole:Agent'])->prefix('agent')->name('agent.')->group(function () {
        Route::get('/dashboard', [AgentController::class, 'dashboard'])->name('dashboard');
        Route::get('/brokers', [AgentController::class, 'brokers'])->name('brokers');
        Route::get('/property/{id}', [AgentController::class, 'propertyDetails'])->name('property.details');
        Route::get('/api/property/{id}', [AgentController::class, 'getPropertyDetails'])->name('api.property.details');
        Route::get('/appointments', [AgentController::class, 'appointments'])->name('appointments');
        Route::get('/appointments/filter/{status}', [AgentController::class, 'getAppointmentsByStatus'])->name('appointments.filter.agent');
        Route::get('/profile', [AgentController::class, 'profile'])->name('profile');
        Route::post('/profile/update', [AgentController::class, 'updateProfile'])->name('profile.update');
        Route::post('/appointments/create', [AgentController::class, 'createAppointment'])->name('appointments.create');
        Route::put('/appointments/{id}/status', [AgentController::class, 'updateAppointmentStatus'])->name('appointments.update-status');
        Route::get('/transactions', [AgentController::class, 'index'])->name('transactions');
        Route::post('/transactions', [AgentController::class, 'store'])->name('transactions.store');
        Route::get('/transactions/search', [AgentController::class, 'search'])->name('transactions.search');
        Route::get('/transactions/export', [AgentController::class, 'export'])->name('transactions.export');
        Route::get('/transactions/{id}', [AgentController::class, 'show'])->name('transactions.show');
        Route::get('/transactions/{id}/details', [AgentController::class, 'getTransactionDetails'])->name('transactions.details');
        Route::get('/transactions/{id}/edit-data', [AgentController::class, 'getTransactionForEdit'])->name('transactions.edit-data');
        Route::put('/transactions/{id}', [AgentController::class, 'update'])->name('transactions.update');
        Route::put('/transactions/{id}/status', [AgentController::class, 'updateTransactionStatus'])->name('transactions.update-status');

        // New 4-step transaction modal API endpoints
        Route::get('/properties', [AgentController::class, 'getProperties'])->name('properties.get');
        Route::get('/properties/assigned', [AgentController::class, 'getAssignedProperties'])->name('properties.assigned');
        Route::get('/contract-templates', [AgentController::class, 'getContractTemplates'])->name('contract.templates');
        Route::get('/contract-templates/{id}/download', [AgentController::class, 'downloadContractTemplate'])->name('contract.templates.download');

        // Template editing endpoints
        Route::post('/template/create-copy', [AgentController::class, 'createTemplateCopy'])->name('template.create-copy');
        Route::get('/template/edit/{file}', [AgentController::class, 'editTemplate'])->name('template.edit');
        Route::get('/template/preview/{file}', [AgentController::class, 'previewTemplate'])->name('template.preview');
        Route::post('/template/save', [AgentController::class, 'saveTemplate'])->name('template.save');

        // Upload document for creating transaction
        Route::post('/upload-transaction-document', [AgentController::class, 'uploadTransactionDocumentForCreation'])->name('upload.transaction.document');

        // Debug endpoint to check current user
        Route::get('/debug/user', function() {
            $user = Auth::user();
            return response()->json([
                'authenticated' => Auth::check(),
                'user_id' => $user ? $user->UserID : null,
                'user_role' => $user ? $user->Role : null,
                'user_name' => $user ? $user->FullName : null,
                'user_email' => $user ? $user->Email : null
            ]);
        })->name('debug.user');

        // Document management routes
        Route::get('/transactions/{id}/documents', [AgentController::class, 'getTransactionDocuments'])->name('transactions.documents');
        Route::post('/transactions/{id}/documents', [AgentController::class, 'uploadTransactionDocument'])->name('transactions.documents.upload');
        Route::get('/transactions/{transactionId}/documents/{documentId}/download', [AgentController::class, 'downloadTransactionDocument'])->name('transactions.documents.download');
        Route::get('/transactions/{transactionId}/documents/download-all', [AgentController::class, 'downloadAllTransactionDocuments'])->name('transactions.documents.download-all');
        Route::delete('/transactions/{transactionId}/documents/{documentId}', [AgentController::class, 'deleteTransactionDocument'])->name('transactions.documents.delete');
        Route::get('/transactions/analytics/dashboard', [AgentController::class, 'getTransactionAnalytics'])->name('transactions.analytics');
        Route::get('/properties/{id}/customers', [AgentController::class, 'getRelatedCustomers'])->name('properties.customers');
        Route::get('/search-owners', [AgentController::class, 'searchOwners'])->name('search.owners');
        Route::get('/search-customers', [AgentController::class, 'searchCustomers'])->name('search.customers');
        Route::get('/search-properties', [AgentController::class, 'searchProperties'])->name('search.properties');
        Route::get('/api/properties/available', [AgentController::class, 'getAvailableProperties'])->name('api.properties.available');
        Route::get('/api/customers', [AgentController::class, 'getAvailableCustomers'])->name('api.customers.available');

        Route::get('/notifications-agent', [AgentController::class, 'getNotifications'])->name('notifications.agent');

        // Test route for modal
        Route::get('/test-modal', function() {
            return view('test-modal');
        })->name('test.modal');

        // Test route for transaction creation
        Route::post('/test-transaction', function(Request $request) {
            $controller = new App\Http\Controllers\AgentController();

            // Create test data
            $testData = [
                'property_id' => 'PS00001',
                'customer_id' => 'UID00004',
                'transaction_type' => 'sell',
                'price' => 5000000000,
                'payment_method' => 'cash',
                'payment_schedule' => 'one_time',
                'commission_rate' => 2.5,
                'contract_start_date' => '2025-06-07',
                'contract_end_date' => '2025-07-07',
                'notes' => 'Test transaction from automated testing'
            ];

            $request->merge($testData);

            try {
                $response = $controller->store($request);
                return response()->json([
                    'test_status' => 'success',
                    'result' => $response->getData()
                ]);
            } catch (Exception $e) {
                return response()->json([
                    'test_status' => 'error',
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
            }
        })->name('test.transaction');
    });

    // Route đăng xuất (áp dụng chung cho tất cả quyền)
    Route::post('/logout', function () {
        Auth::logout();
        return redirect()->route('login');
    })->name('logout');

    // Transaction Route trong OwnerController
    Route::get('/transaction/{id}', [OwnerController::class, 'showTransaction'])->name('transaction.view');
    Route::get('/transaction/{id}/print', [OwnerController::class, 'printInvoice'])->name('transaction.print');
    Route::get('/export/transactions', [OwnerController::class, 'exportTransactions'])->name('export.transactions');

    // Route dự phòng để đảm bảo không bị 404 khi truy cập /property/{id}
    // Kiểm tra quyền của người dùng và chuyển hướng phù hợp
    Route::get('/property/{id}', function($id) {
        if (Auth::check() && Auth::user()->Role === 'Agent') {
            return redirect()->route('agent.property.details', ['id' => $id]);
        } elseif (Auth::check() && Auth::user()->Role === 'Owner') {
            // return redirect()->route('owner.property.view', ['id' => $id]);
            return redirect('/'); // Tạm thời chuyển về trang chủ nếu chưa có route cho owner
        } else {
            // Chuyển về trang chủ hoặc thông báo lỗi cho người dùng không xác thực
            return redirect('/');
        }
    });
});

// Debug route for properties
Route::get('/debug/properties', function() {
    $agent = \App\Models\User::where('UserID', 'UID00003')->first();
    if (!$agent) {
        return response()->json(['error' => 'Agent not found']);
    }

    $properties = \App\Models\Property::with(['danhMuc', 'owner', 'chiTiet', 'images'])
        ->where('AgentID', $agent->UserID)
        ->get();

    return response()->json([
        'agent' => $agent->toArray(),
        'total_properties' => $properties->count(),
        'active_properties' => $properties->where('Status', 'active')->count(),
        'properties' => $properties->map(function($p) {
            return [
                'id' => $p->PropertyID,
                'title' => $p->Title,
                'status' => $p->Status,
                'agent_id' => $p->AgentID,
                'has_detail' => !!$p->chiTiet,
                'images_count' => $p->images->count()
            ];
        })
    ]);
});

// Debug route to test property detail API response
Route::get('/debug/property/{id}', function($id) {
    $property = \App\Models\Property::with(['danhMuc', 'owner', 'chiTiet', 'images'])
        ->where('PropertyID', $id)
        ->first();

    if (!$property) {
        return response()->json(['error' => 'Property not found'], 404);
    }

    // Format response similar to AgentController::getProperty
    $response = [
        'success' => true,
        'property' => [
            'PropertyID' => $property->PropertyID,
            'Title' => $property->Title,
            'Description' => $property->Description,
            'Address' => $property->Address,
            'Price' => $property->Price,
            'TypePro' => $property->TypePro,
            'Status' => $property->Status,
            'Latitude' => $property->Latitude,
            'Longitude' => $property->Longitude,
            'images' => $property->images,
            'danhMuc' => $property->danhMuc,
            'owner' => $property->owner,
            'chiTiet' => $property->chiTiet
        ]
    ];

    return response()->json($response);
});

// Test API endpoint for modal (matches agent API format)
Route::get('/test/api/property/{id}', function($id) {
    // Mock property data that matches the AgentController::getProperty response format
    $mockProperties = [
        'PR00001' => [
            'success' => true,
            'property' => [
                'PropertyID' => 'PR00001',
                'Title' => 'Cho Thuê Chung Cư Mini Sactaim',
                'Description' => 'Chung cư sẽ có các điều hòa, wifi miễn phí, gần trường học và chợ',
                'Address' => '12 Ca Văn Thỉnh, Phường 8, Vũng Tàu',
                'Price' => 12000000,
                'TypePro' => 'Rent',
                'Status' => 'active',
                'Latitude' => 10.3414,
                'Longitude' => 107.0839,
                'images' => [
                    [
                        'ImageID' => 3,
                        'PropertyID' => 'PR00001',
                        'ImagePath' => 'public\\storage\\properties\\images\\anh-1.jpg',
                        'Caption' => 'Sảnh phòng trọ'
                    ],
                    [
                        'ImageID' => 4,
                        'PropertyID' => 'PR00001',
                        'ImagePath' => 'public\\storage\\properties\\images\\anh-2.jpg',
                        'Caption' => 'Phòng của nhà thuê'
                    ],
                    [
                        'ImageID' => 5,
                        'PropertyID' => 'PR00001',
                        'ImagePath' => 'public\\storage\\properties\\images\\anh-3.jpg',
                        'Caption' => 'Phòng bếp hiện đại'
                    ],
                    [
                        'ImageID' => 6,
                        'PropertyID' => 'PR00001',
                        'ImagePath' => 'public\\storage\\properties\\images\\anh-4.jpg',
                        'Caption' => 'Ban công view đẹp'
                    ]
                ],
                'danhMuc' => [
                    'Protype_ID' => 3,
                    'ten_pro' => 'Chung cư mini',
                    'Type' => 'Chung cư'
                ],
                'owner' => [
                    'UserID' => 'UID00007',
                    'Name' => 'Nguyễn Hoàng Phát',
                    'Email' => 'nguyenphat241203@gmail.com',
                    'Phone' => '0855542696',
                    'Address' => '65/20 Nguyen Do Cung, Phường Tây Thạnh, Quận Tân Phú, TP.HCM'
                ],
                'chiTiet' => [
                    'IdDetail' => 1,
                    'PropertyID' => 'PR00001',
                    'Floor' => 2,
                    'HouseLength' => 12,
                    'HouseWidth' => 15,
                    'Bedroom' => 4,
                    'Balcony' => 1,
                    'Bath_WC' => 3,
                    'legal' => 'Sổ Đỏ/ Sổ Hồng',
                    'view' => 'Tây Bắc',
                    'near' => 'Gần Chợ Bách Hóa Xanh, GS25',
                    'Interior' => 'Cơ Bản',
                    'WaterPrice' => 'Thỏa thuận',
                    'PowerPrice' => 'Thỏa thuận',
                    'Utilities' => 'Thỏa thuận'
                ]
            ]
        ],
        'PS00002' => [
            'success' => true,
            'property' => [
                'PropertyID' => 'PS00002',
                'Title' => 'Cho Bán Phòng 2 phòng của Sactaim',
                'Description' => 'Phòng cho thuê giá rẻ, vị trí đẹp, gần trung tâm',
                'Address' => '123 Nguyễn Văn Linh, Quận 7, TP.HCM',
                'Price' => 25000000,
                'TypePro' => 'Sale',
                'Status' => 'active',
                'Latitude' => 10.7308,
                'Longitude' => 106.7185,
                'images' => [],
                'danhMuc' => [
                    'Protype_ID' => 2,
                    'ten_pro' => 'Nhà phố',
                    'Type' => 'Nhà phố'
                ],
                'owner' => [
                    'UserID' => 'UID00007',
                    'Name' => 'Nguyễn Hoàng Phát',
                    'Email' => 'nguyenphat241203@gmail.com',
                    'Phone' => '0855542696',
                    'Address' => '65/20 Nguyen Do Cung, Phường Tây Thạnh, Quận Tân Phú, TP.HCM'
                ],
                'chiTiet' => [
                    'IdDetail' => 2,
                    'PropertyID' => 'PS00002',
                    'Floor' => 3,
                    'HouseLength' => 8,
                    'HouseWidth' => 20,
                    'Bedroom' => 3,
                    'Balcony' => 2,
                    'Bath_WC' => 2,
                    'legal' => 'Sổ Hồng',
                    'view' => 'Đông Nam',
                    'near' => 'Gần siêu thị, trường học',
                    'Interior' => 'Đầy đủ',
                    'WaterPrice' => '50.000 VNĐ/tháng',
                    'PowerPrice' => '3.500 VNĐ/kwh',
                    'Utilities' => 'Điện, nước, internet'
                ]
            ]
        ]
    ];

    if (!isset($mockProperties[$id])) {
        return response()->json(['success' => false, 'message' => 'Property not found'], 404);
    }

    return response()->json($mockProperties[$id]);
});

// Test route for modal without authentication
Route::get('/test/modal', function() {
    // Create fake properties data for testing
    $properties = collect([
        (object)[
            'PropertyID' => 'PR00001',
            'Title' => 'Cho Thuê Chung Cư Mini Sactaim',
            'Description' => 'Chung cư sẽ có các điều hòa',
            'Address' => '12 Ca Văn Thỉnh',
            'Price' => 12000000,
            'TypePro' => 'Rent',
            'Status' => 'active',
            'images' => collect([
                (object)['ImagePath' => 'public\\storage\\properties\\images\\anh-1.jpg', 'Caption' => 'Sảnh phòng trọ']
            ]),
            'danhMuc' => (object)['ten_pro' => 'Chung cư mini'],
            'owner' => (object)[
                'Name' => 'Nguyễn Hoàng Phát',
                'Phone' => '0855542696',
                'Email' => 'nguyenphat241203@gmail.com'
            ]
        ],
        (object)[
            'PropertyID' => 'PS00002',
            'Title' => 'Cho Bán Phòng 2 phòng của Sactaim',
            'Description' => 'Phòng cho thuê giá rẻ',
            'Address' => '123 Nguyễn Văn Linh',
            'Price' => 25000000,
            'TypePro' => 'Sale',
            'Status' => 'active',
            'images' => collect([]),
            'danhMuc' => (object)['ten_pro' => 'Nhà phố'],
            'owner' => (object)[
                'Name' => 'Nguyễn Hoàng Phát',
                'Phone' => '0855542696',
                'Email' => 'nguyenphat241203@gmail.com'
            ]
        ]
    ]);

    $categories = collect([]);
    $owners = collect([]);

    return view('agents.brokers', compact('properties', 'categories', 'owners'));
});

// Test route for transaction creation
Route::post('/test-transaction', function(Request $request) {
    $controller = new App\Http\Controllers\AgentController();

    // Create test data
    $testData = [
        'property_id' => 'PS00001',
        'customer_id' => 'UID00004',
        'transaction_type' => 'sell',
        'price' => 5000000000,
        'payment_method' => 'cash',
        'payment_schedule' => 'one_time',
        'commission_rate' => 2.5,
        'contract_start_date' => '2025-06-07',
        'contract_end_date' => '2025-07-07',
        'notes' => 'Test transaction from automated testing'
    ];

    $request->merge($testData);

    try {
        $response = $controller->store($request);
        return response()->json([
            'test_status' => 'success',
            'result' => $response->getData()
        ]);
    } catch (Exception $e) {
        return response()->json([
            'test_status' => 'error',
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);
    }
})->name('test.transaction');

// Complete test page
Route::get('/test-complete', function() {
    return view('test-complete');
})->name('test.complete');

// Template routes for agents
Route::middleware(['auth'])->prefix('agent')->group(function () {
    Route::get('/contract-templates', [AgentController::class, 'getContractTemplates'])->name('agent.contract.templates');
    Route::post('/template/create-copy', [AgentController::class, 'createTemplateCopy'])->name('agent.template.create-copy');
    Route::get('/template/edit/{file}', [AgentController::class, 'editTemplate'])->name('agent.template.edit');
    Route::get('/template/download/{file}', [AgentController::class, 'downloadTemplate'])->name('agent.template.download');
    Route::post('/template/save', [AgentController::class, 'saveTemplate'])->name('agent.template.save');
    Route::get('/properties/assigned', [AgentController::class, 'getAssignedProperties'])->name('agent.properties.assigned');
});


