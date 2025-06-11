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

/*
|--------------------------------------------------------------------------
| Authenticated Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth'])->group(function () {

    // Common authenticated routes
    Route::post('/logout', function () {
        Auth::logout();
        return redirect()->route('login');
    })->name('logout');

    Route::post('/chat/send', [ChatbotController::class, 'sendMessage']);
    Route::get('/chat', [ChatbotController::class, 'userChat'])->name('user.chat');

    // Common document routes
    Route::get('/document/view/{id}', [SystemController::class, 'viewDocument'])->name('admin.document.view');
    Route::get('/document/download/{id}', [SystemController::class, 'downloadDocument'])->name('admin.document.download');

    /*
    |--------------------------------------------------------------------------
    | Customer Routes
    |--------------------------------------------------------------------------
    */
    Route::middleware(['checkRole:Customer'])->prefix('customer')->name('customer.')->group(function () {
        Route::get('/', [CustomerController::class, 'index'])->name('home');

        // Profile management
        Route::get('/profile', [CustomerController::class, 'showProfile'])->name('profile');
        Route::put('/profile', [CustomerController::class, 'updateProfile'])->name('profile.update');
        Route::get('/change-password', [CustomerController::class, 'changePassword'])->name('change-password');
        Route::post('/change-password', [CustomerController::class, 'updatePassword'])->name('updatePassword');

        // Appointments
        Route::get('/appointments', [CustomerController::class, 'showAppointments'])->name('appointments.index');
        Route::post('/appointments/{id}/cancel', [CustomerController::class, 'cancelAppointment'])->name('appointments.cancel');

        // Transaction history
        Route::get('/transaction-history', [CustomerController::class, 'transactionHistory'])->name('transaction.history');
        Route::get('/transaction/{id}/detail', [CustomerController::class, 'transactionDetail'])->name('transaction.detail');
        Route::post('/payment/process', [CustomerController::class, 'processPayment'])->name('payment.process');

        // Documents
        Route::get('/document/view/{id}', [CustomerController::class, 'viewDocument'])->name('document.view');
        Route::get('/document/download/{id}', [CustomerController::class, 'downloadDocument'])->name('document.download');

        // Agent interaction
        Route::get('/contact-agent', [CustomerController::class, 'contactAgent'])->name('contact-agent');
        Route::post('/submit-feedback', [CustomerController::class, 'submitFeedback'])->name('submit-feedback');

        // Notifications
        Route::get('/notifications', [CustomerController::class, 'getNotifications'])->name('notifications');
        Route::post('/notifications/{id}/mark-as-read', [CustomerController::class, 'markNotificationAsRead'])->name('notifications.mark-read');
    });

    /*
    |--------------------------------------------------------------------------
    | Owner Routes
    |--------------------------------------------------------------------------
    */
    Route::middleware(['checkRole:Owner'])->prefix('owner')->name('owner.')->group(function () {
        Route::get('/dashboard', [OwnerController::class, 'dashboard'])->name('dashboard');

        // Property management
        Route::get('/property', [OwnerController::class, 'listProperty'])->name('property.index');
        Route::post('/property/listings', [OwnerController::class, 'storePropertyListing'])->name('property.listings.store');

        // Appointments
        Route::get('/appointments', [OwnerController::class, 'appointments'])->name('appointments.index');
        Route::get('/appointments/filter/{status}', [OwnerController::class, 'getAppointmentsByStatus'])->name('appointments.filter');
        Route::get('/appointments/{id}/detail', [OwnerController::class, 'getAppointmentDetail'])->name('appointments.detail');
        Route::post('/appointments/{id}/confirm', [OwnerController::class, 'confirmAppointment'])->name('appointments.confirm');
        Route::post('/appointments/{id}/cancel', [OwnerController::class, 'cancelAppointment'])->name('appointments.cancel');
        Route::post('/appointments/{id}/finish', [OwnerController::class, 'finishAppointment'])->name('appointments.finish');
        Route::post('/appointments/update-status/{id}', [OwnerController::class, 'updateAppointmentStatus'])->name('appointment.updateStatus');

        // Transactions
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

        // Notifications
        Route::get('/notifications-owner', [OwnerController::class, 'getNotifications'])->name('notifications-owner');

        // Profile
        Route::get('/profile', [OwnerController::class, 'showProfile'])->name('profile');
        Route::post('/profile', [OwnerController::class, 'updateProfile'])->name('profile.update');
        Route::get('/change-password', [OwnerController::class, 'showChangePasswordForm'])->name('change-password');
        Route::post('/change-password', [OwnerController::class, 'changePassword'])->name('change-password.update');
    });

    /*
    |--------------------------------------------------------------------------
    | Agent Routes
    |--------------------------------------------------------------------------
    */
    Route::middleware(['checkRole:Agent'])->prefix('agent')->name('agent.')->group(function () {
        Route::get('/dashboard', [AgentController::class, 'dashboard'])->name('dashboard');
        Route::get('/brokers', [AgentController::class, 'brokers'])->name('brokers');

        // Property management
        Route::get('/property/{id}', [AgentController::class, 'propertyDetails'])->name('property.details');
        Route::get('/api/property/{id}', [AgentController::class, 'getPropertyDetails'])->name('api.property.details');
        Route::get('/properties', [AgentController::class, 'getProperties'])->name('properties.get');
        Route::get('/properties/assigned', [AgentController::class, 'getAssignedProperties'])->name('properties.assigned');
        Route::get('/api/properties/available', [AgentController::class, 'getAvailableProperties'])->name('api.properties.available');
        Route::get('/properties/{id}/customers', [AgentController::class, 'getRelatedCustomers'])->name('properties.customers');

        // Appointments
        Route::get('/appointments', [AgentController::class, 'appointments'])->name('appointments');
        Route::get('/appointments/filter/{status}', [AgentController::class, 'getAppointmentsByStatus'])->name('appointments.filter.agent');
        Route::post('/appointments/create', [AgentController::class, 'createAppointment'])->name('appointments.create');
        Route::put('/appointments/{id}/status', [AgentController::class, 'updateAppointmentStatus'])->name('appointments.update-status');

        // Transactions
        Route::get('/transactions', [AgentController::class, 'index'])->name('transactions');
        Route::post('/transactions', [AgentController::class, 'store'])->name('transactions.store');
        Route::get('/transactions/search', [AgentController::class, 'search'])->name('transactions.search');
        Route::get('/transactions/export', [AgentController::class, 'export'])->name('transactions.export');
        Route::get('/transactions/{id}', [AgentController::class, 'show'])->name('transactions.show');
        Route::get('/transactions/{id}/details', [AgentController::class, 'getTransactionDetails'])->name('transactions.details');
        Route::get('/transactions/{id}/edit-data', [AgentController::class, 'getTransactionForEdit'])->name('transactions.edit-data');
        Route::put('/transactions/{id}', [AgentController::class, 'update'])->name('transactions.update');
        Route::put('/transactions/{id}/status', [AgentController::class, 'updateTransactionStatus'])->name('transactions.update-status');
        Route::get('/transactions/analytics/dashboard', [AgentController::class, 'getTransactionAnalytics'])->name('transactions.analytics');

        // Contract templates
        Route::get('/contract-templates', [AgentController::class, 'getContractTemplates'])->name('contract.templates');
        Route::get('/contract-templates/{id}/download', [AgentController::class, 'downloadContractTemplate'])->name('contract.templates.download');
        Route::post('/template/create-copy', [AgentController::class, 'createTemplateCopy'])->name('template.create-copy');
        Route::get('/template/edit/{file}', [AgentController::class, 'editTemplate'])->name('template.edit');
        Route::get('/template/preview/{file}', [AgentController::class, 'previewTemplate'])->name('template.preview');
        Route::get('/template/download/{file}', [AgentController::class, 'downloadTemplate'])->name('template.download');
        Route::post('/template/save', [AgentController::class, 'saveTemplate'])->name('template.save');

        // Document management
        Route::get('/transactions/{id}/documents', [AgentController::class, 'getTransactionDocuments'])->name('transactions.documents');
        Route::post('/transactions/{id}/documents', [AgentController::class, 'uploadTransactionDocument'])->name('transactions.documents.upload');
        Route::get('/transactions/{transactionId}/documents/{documentId}/download', [AgentController::class, 'downloadTransactionDocument'])->name('transactions.documents.download');
        Route::get('/transactions/{transactionId}/documents/download-all', [AgentController::class, 'downloadAllTransactionDocuments'])->name('transactions.documents.download-all');
        Route::delete('/transactions/{transactionId}/documents/{documentId}', [AgentController::class, 'deleteTransactionDocument'])->name('transactions.documents.delete');
        Route::post('/upload-transaction-document', [AgentController::class, 'uploadTransactionDocumentForCreation'])->name('upload.transaction.document');

        // Search functions
        Route::get('/search-owners', [AgentController::class, 'searchOwners'])->name('search.owners');
        Route::get('/search-customers', [AgentController::class, 'searchCustomers'])->name('search.customers');
        Route::get('/search-properties', [AgentController::class, 'searchProperties'])->name('search.properties');
        Route::get('/api/customers', [AgentController::class, 'getAvailableCustomers'])->name('api.customers.available');

        // Profile
        Route::get('/profile', [AgentController::class, 'profile'])->name('profile');
        Route::post('/profile/update', [AgentController::class, 'updateProfile'])->name('profile.update');

        // Notifications
        Route::get('/notifications-agent', [AgentController::class, 'getNotifications'])->name('notifications.agent');
    });

    /*
    |--------------------------------------------------------------------------
    | Admin Routes
    |--------------------------------------------------------------------------
    */
    Route::middleware(['checkRole:Admin'])->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [SystemController::class, 'admin'])->name('dashboard');

        // Property management
        Route::get('/property', [SystemController::class, 'getProperty'])->name('property');
        Route::get('/property/search', [SystemController::class, 'SearchProperty'])->name('property.search');
        Route::get('/property/create', [SystemController::class, 'createPropertyForm'])->name('property.create');
        Route::post('/property', [SystemController::class, 'createProperty'])->name('property.store');
        Route::post('/property/update-status', [SystemController::class, 'updatePropertyStatus'])->name('property.updateStatus');
        Route::post('/property/update-batch-status', [SystemController::class, 'updateBatchStatus'])->name('property.updateBatchStatus');
        Route::get('/property/type/{type}', [SystemController::class, 'getPropertyByTypeAndStatus'])->name('property.type.status');
        Route::get('/property/status/{status}', [SystemController::class, 'getPropertyByStatus'])->name('property.status');
        Route::get('/property/{id}/edit', [SystemController::class, 'EditPropertyByStatus'])->name('property.edit');
        Route::delete('/property/{id}', [SystemController::class, 'deleteProperty'])->name('property.delete');
        Route::put('/property/{id}', [SystemController::class, 'EditPropertyByStatus'])->name('property.update');
        Route::get('/property/{id}', [SystemController::class, 'getPropertyById'])->name('property.id');

        // Property assignment
        Route::get('/assign-property', [SystemController::class, 'getAssignProperty'])->name('assign.property');
        Route::post('/assign-property', [SystemController::class, 'assignAgentToProperty'])->name('assign.property.store');
        Route::get('/check-agent-limit/{agentId}', [SystemController::class, 'checkAgentPropertyCount'])->name('check.agent.limit');
        Route::get('/agent/{id}/properties', [SystemController::class, 'getAgentProperties'])->name('agent.properties');
        Route::get('/available-properties', [SystemController::class, 'getAvailableProperties'])->name('available.properties');
        Route::post('/assign/property', [SystemController::class, 'assignProperties'])->name('assign.properties');
        Route::post('/unassign/property', [SystemController::class, 'unassignProperty'])->name('unassign.property');

        // User management
        Route::get('/user', [SystemController::class, 'getUser'])->name('users');
        Route::get('/user/create', [SystemController::class, 'createUserForm'])->name('users.create');
        Route::post('/user/create', [SystemController::class, 'createUser'])->name('users.store');
        Route::delete('/user/{id}', [SystemController::class, 'deleteUser'])->name('users.delete');
        Route::get('/user/{id}/edit', [SystemController::class, 'editUserForm'])->name('users.edit');
        Route::put('/user/{id}', [SystemController::class, 'UpdateUser'])->name('users.update');
        Route::get('/user/role/{role}', [SystemController::class, 'getUserByRole'])->name('users.byRole');
        Route::get('/user/status/{status}', [SystemController::class, 'getUserByStatus'])->name('users.byStatus');
        Route::get('/user/role/{role}/search', [SystemController::class, 'SearchUser'])->name('users.search');
        Route::post('/users/{userId}/toggle-status', [SystemController::class, 'toggleUserStatus'])->name('users.toggleStatus');
        Route::get('/users/search', [SystemController::class, 'searchUsers'])->name('users.search.enhanced');
        Route::post('/users/create-with-profile', [SystemController::class, 'createUserWithProfile'])->name('users.createWithProfile');
        Route::put('/users/{userId}/update-with-profile', [SystemController::class, 'updateUserWithProfile'])->name('users.updateWithProfile');
        Route::put('/users/{userId}/profile/{role}', [SystemController::class, 'updateRoleProfile'])->name('users.updateRoleProfile');
        Route::post('/users/{userId}/activate', [SystemController::class, 'activateUser'])->name('users.activate');

        // Appointment management
        Route::get('/appointment', [SystemController::class, 'getAppointment'])->name('appointment');
        Route::get('/appointment/search-by-date', [SystemController::class, 'searchAppointmentByDate'])->name('appointment.search.date');
        Route::get('/appointment/search-by-range', [SystemController::class, 'getAppointmentsByDateRange'])->name('appointment.search.range');
        Route::get('/appointment/agent/{agentId}', [SystemController::class, 'getAppointmentsByAgent'])->name('appointment.byAgent');
        Route::get('/appointment/detail/{id}', [SystemController::class, 'getAppointmentDetail'])->name('appointment.detail');
        Route::get('/appointment/{id}', [SystemController::class, 'getAppointmentById'])->name('appointment.id');
        Route::delete('/appointment/{id}', [SystemController::class, 'deleteAppointment'])->name('appointment.delete');

        // Transaction management
        Route::get('/transaction', [SystemController::class, 'getTransaction'])->name('transaction');
        Route::put('/transaction/{transactionId}/payment-statuses', [SystemController::class, 'updatePaymentStatuses'])->name('transaction.updatePaymentStatuses');
        Route::post('/transaction/{transactionId}/add-payment', [SystemController::class, 'addPayment'])->name('transaction.addPayment');
        Route::post('/transaction/{id}/document', [SystemController::class, 'addDocument'])->name('transaction.addDocument');
        Route::get('/transaction/{id}/details', [SystemController::class, 'getTransactionDetailsAjax'])->name('transaction.details.ajax');
        Route::get('/transaction/{id}', [SystemController::class, 'getTransactionById'])->name('transaction.id');
        Route::delete('/transaction/{id}', [SystemController::class, 'deleteTransaction'])->name('transaction.delete');

        // Contract template management
        Route::get('/contract/preview/{filename}', [SystemController::class, 'previewContract'])->name('contract.preview');
        Route::get('/contract/print/{filename}', [SystemController::class, 'printContract'])->name('contract.print');
        Route::get('/contract/download/{filename}', [SystemController::class, 'downloadContract'])->name('contract.download');
        Route::post('/contracts/add', [SystemController::class, 'addContractTemplate'])->name('contracts.add');
        Route::post('/contracts/edit', [SystemController::class, 'editContractTemplate'])->name('contracts.edit');
        Route::post('/contracts/delete', [SystemController::class, 'deleteContractTemplateJson'])->name('contracts.delete');
        Route::post('/contract/upload', [SystemController::class, 'uploadContractTemplate'])->name('contract.upload');
        Route::post('/contract/download-from-url', [SystemController::class, 'downloadContractFromUrl'])->name('contract.download-from-url');
        Route::delete('/contract/delete/{filename}', [SystemController::class, 'deleteContractTemplate'])->name('contract.delete');
        Route::get('/contract/templates/info', [SystemController::class, 'getContractTemplatesInfo'])->name('contract.templates.info');
        Route::post('/contract/analyze', [SystemController::class, 'analyzeContract'])->name('contract.analyze');

        // Document management
        Route::delete('/document/{id}', [SystemController::class, 'deleteDocument'])->name('admin.document.delete');

        // Feedback management
        Route::get('/feedback', [SystemController::class, 'getFeedback'])->name('feedback');
        Route::get('/feedback/filter', [SystemController::class, 'getFeedbackByStatusRating'])->name('feedback.filter');
        Route::get('/feedback/search', [SystemController::class, 'getFeedbackSearch'])->name('feedback.search');
        Route::get('/feedback/cancelled', [SystemController::class, 'getCancelledFeedback'])->name('feedback.cancelled');
        Route::get('/feedback/{id}', [SystemController::class, 'getFeedbackById'])->name('feedback.id');
        Route::patch('/feedback/{id}', [SystemController::class, 'updateFeedback'])->name('feedback.update');
        Route::patch('/feedback/{id}/status', [SystemController::class, 'updateFeedbackStatus'])->name('feedback.updateStatus');
        Route::delete('/feedback/{id}', [SystemController::class, 'deleteFeedback'])->name('feedback.delete');

        // Commission management
        Route::get('/commission', [SystemController::class, 'getCommission'])->name('commission');
        Route::get('/commission/create', [SystemController::class, 'createCommissionForm'])->name('commission.create');
        Route::post('/commission/create', [SystemController::class, 'createCommission'])->name('commission.store');
        Route::get('/commission/search', [SystemController::class, 'searchCommission'])->name('commission.search');
        Route::get('/commission/search-type', [SystemController::class, 'searchCommissionByDateAndType'])->name('commission.search.type');
        Route::get('/commission/view', [SystemController::class, 'viewCommissionModal'])->name('commission.view');
        Route::post('/commission/view-modal', [SystemController::class, 'viewCommissionModal'])->name('commission.view.modal');
        Route::get('/commission/type/{type}', [SystemController::class, 'getCommissionByType'])->name('commission.type');
        Route::get('/commission/filter/{status}', [SystemController::class, 'getCommissionByStatus'])->name('commission.filter');
        Route::get('/commission/{id}', [SystemController::class, 'getCommissionById'])->name('commission.get');
        Route::get('/commission/{id}/edit', [SystemController::class, 'editCommissionForm'])->name('commission.edit');
        Route::put('/commission/{id}', [SystemController::class, 'updateCommission'])->name('commission.update');
        Route::delete('/commission/{id}', [SystemController::class, 'deleteCommission'])->name('commission.delete');

        // Dashboard analytics
        Route::get('/dashboard/monthly-stats', [SystemController::class, 'getMonthlyStats'])->name('dashboard.monthlyStats');

        // Owner search
        Route::get('/owners/search', [SystemController::class, 'searchOwners'])->name('owners.search');
        Route::get('/owners/{id}', [SystemController::class, 'getOwnerDetails'])->name('owners.details');

        // Chatbot management
        Route::prefix('chatbot')->name('chatbot.')->group(function () {
            Route::get('/demo', [ChatbotController::class, 'demo'])->name('demo');
            Route::get('/admin', [ChatbotController::class, 'index'])->name('admin');

            // Question management
            Route::prefix('questions')->name('questions.')->group(function () {
                Route::get('/', [ChatbotController::class, 'index'])->name('index');
                Route::get('/create', [ChatbotController::class, 'create'])->name('create');
                Route::post('/store', [ChatbotController::class, 'store'])->name('store');
                Route::get('/edit/{id}', [ChatbotController::class, 'edit'])->name('edit');
                Route::put('/update/{id}', [ChatbotController::class, 'update'])->name('update');
                Route::delete('/destroy/{id}', [ChatbotController::class, 'destroy'])->name('destroy');
            });

            // Chat management
            Route::post('/send-to-user', [ChatbotController::class, 'sendToUser'])->name('sendToUser');
            Route::post('/broadcast', [ChatbotController::class, 'sendBroadcast'])->name('broadcast');
            Route::get('/users-list', [ChatbotController::class, 'getUsersList'])->name('usersList');
            Route::get('/default-users', [ChatbotController::class, 'getDefaultUsers'])->name('defaultUsers');

            // FAQ management
            Route::prefix('faq')->name('faq.')->group(function () {
                Route::get('/', [ChatbotController::class, 'manageFAQ'])->name('index');
                Route::post('/add', [ChatbotController::class, 'addFAQ'])->name('add');
                Route::put('/update/{index}', [ChatbotController::class, 'updateFAQ'])->name('update');
                Route::delete('/delete/{index}', [ChatbotController::class, 'deleteFAQ'])->name('delete');
            });
        });
    });

    // Fallback routes for property access based on role
    Route::get('/property/{id}', function($id) {
        if (Auth::user()->Role === 'Agent') {
            return redirect()->route('agent.property.details', ['id' => $id]);
        } elseif (Auth::user()->Role === 'Owner') {
            return redirect('/'); // Temporary redirect for owner
        }
        return redirect('/');
    });

    // Common transaction routes
    Route::get('/transaction/{id}', [OwnerController::class, 'showTransaction'])->name('transaction.view');
    Route::get('/transaction/{id}/print', [OwnerController::class, 'printInvoice'])->name('transaction.print');
    Route::get('/export/transactions', [OwnerController::class, 'exportTransactions'])->name('export.transactions');
});

/*
|--------------------------------------------------------------------------
| Development/Testing Routes
|--------------------------------------------------------------------------
| These routes should be removed or protected in production
*/

if (app()->environment(['local', 'testing'])) {
    // Test routes for Gemini AI
    Route::prefix('test')->name('test.')->group(function () {
        Route::get('/gemini', [ChatbotController::class, 'showTestPage'])->name('gemini');
        Route::post('/gemini/ask', [ChatbotController::class, 'testGemini'])->name('gemini.ask');
        Route::get('/gemini/faq', [ChatbotController::class, 'testFAQ'])->name('gemini.faq');
        Route::post('/gemini/full', [ChatbotController::class, 'testFullChatbot'])->name('gemini.full');
        Route::post('/gemini/database', [ChatbotController::class, 'testDatabaseSearch'])->name('gemini.database');
        Route::post('/gemini/smart-search', [ChatbotController::class, 'testSmartSearch'])->name('gemini.smartsearch');
        Route::get('/gemini/scenarios', [ChatbotController::class, 'testSearchScenarios'])->name('gemini.scenarios');
        Route::get('/gemini/db-connection', [ChatbotController::class, 'testDatabaseConnection'])->name('gemini.dbconnection');
        Route::get('/gemini/status', [ChatbotController::class, 'getDatabaseIntegrationStatus'])->name('gemini.status');

        // Test transactions
        Route::get('/transactions', function () {
            $agent = App\Models\User::where('Role', 'Agent')->first();
            if (!$agent) {
                return 'No agent found in database';
            }
            Auth::login($agent);
            $controller = new AgentController();
            return $controller->index();
        })->name('transactions');

        // Test commission modal
        Route::get('/commission-modal', function () {
            return view('test_commission_modal');
        });

        // Demo property cards
        Route::get('/property-cards-demo', function () {
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
                ]
            ]);
            $categories = collect([]);
            $owners = collect([]);
            return view('owners.property.index', compact('properties', 'categories', 'owners'));
        });

        // Debug routes
        Route::get('/properties', function() {
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

        // Test modal routes
        Route::get('/modal', function() {
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
                ]
            ]);
            $categories = collect([]);
            $owners = collect([]);
            return view('agents.brokers', compact('properties', 'categories', 'owners'));
        });

        Route::get('/complete', function() {
            return view('test-complete');
        })->name('complete');

        // API test endpoints
        Route::get('/api/property/{id}', function($id) {
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
                        'Status' => 'active'
                    ]
                ]
            ];

            if (!isset($mockProperties[$id])) {
                return response()->json(['success' => false, 'message' => 'Property not found'], 404);
            }

            return response()->json($mockProperties[$id]);
        });

        Route::post('/transaction', function(Request $request) {
            $controller = new App\Http\Controllers\AgentController();
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
        })->name('transaction');
    });
}
