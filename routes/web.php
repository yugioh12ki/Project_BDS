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

// Public routes - đặt trước middleware auth
Route::get('/search', [CustomerController::class, 'search'])->name('customer.search');
Route::get('/property/{id}', [CustomerController::class, 'propertyDetail'])->name('property.detail');

// Đặt các route này ở đầu file, sau route trang chủ
Route::get('/', [HomeController::class, 'index'])->name('home');

//Đăng Nhập
Route::get('/login',[LoginController::class,"login"])->name('login');
Route::post('/login', [LoginController::class, "authenticate"])->name('login.authenticate');

//Đăng Ký
Route::get('/register',[RegisterController::class,"register"])->name('register');

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

        // Routes danh cho người dùng đã đăng nhập không cần phải liên quan đến quyền
        Route::get('/document/view/{id}', [SystemController::class, 'viewDocument'])->name('admin.document.view');
        Route::get('/document/download/{id}', [SystemController::class, 'downloadDocument'])->name('admin.document.download');
        Route::delete('/document/delete/{id}', [SystemController::class, 'deleteDocument'])->name('admin.document.delete');

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
            Route::get('/transaction/{id}', [SystemController::class, "getTransactionById"])->name('transaction.id');
            Route::delete('/transaction/{id}', [SystemController::class, 'deleteTransaction'])->name('transaction.delete');
            Route::get('/transaction', [SystemController::class, "getTransaction"])->name('transaction');

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
            Route::get('/commission/filter/{status}', [SystemController::class, 'getCommissionByStatus'])->name('commission.filter');
            Route::get('/commission/create', [SystemController::class, "createCommissionForm"])->name('commission.create');
            Route::post('/commission/create', [SystemController::class, 'createCommission'])->name('commission.store');
            Route::get('/commission/search', [SystemController::class, 'searchCommission'])->name('commission.search');
            Route::post('/commission/view', [SystemController::class, 'viewCommissionModal'])->name('commission.view');
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
    Route::get('/chat', [ChatbotController::class, 'userChat'])->name('user.chat');

});

// Route API cho chatbot
Route::post('/api/chatbot', [ChatbotController::class, 'answer'])->name('chatbot.answer');

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
