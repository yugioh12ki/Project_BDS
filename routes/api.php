<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\Transaction;
use App\Models\User;
use App\Http\Controllers\ChatbotController;
use App\Http\Controllers\HomeController;

// API Controllers for Mobile App
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\PropertyController;
use App\Http\Controllers\API\AppointmentController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// ========================
// MOBILE APP API ROUTES
// ========================

// Test endpoint để kiểm tra kết nối từ mobile
Route::get('/test', function () {
    return response()->json([
        'status' => 'success',
        'message' => 'API connection successful',
        'server_time' => now(),
        'server_ip' => request()->server('SERVER_ADDR'),
        'client_ip' => request()->ip()
    ]);
});

// Public routes (no authentication required)
Route::prefix('v1')->group(function () {
    // Test endpoint trong v1
    Route::get('/test', function () {
        return response()->json([
            'status' => 'success',
            'message' => 'API v1 connection successful',
            'version' => 'v1',
            'server_time' => now()
        ]);
    });

    // Authentication routes
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);

    // Public property routes
    Route::get('/properties', [PropertyController::class, 'index']);
    Route::get('/properties/{id}', [PropertyController::class, 'show']);
    Route::get('/properties/search', [PropertyController::class, 'search']);
    Route::get('/properties/featured', [PropertyController::class, 'featured']);
    Route::get('/categories', [PropertyController::class, 'categories']);

    // Chatbot (public access)
    Route::post('/chatbot/answer', [ChatbotController::class, 'answerChatbot']);
});

// ========================
// CHATBOX PUBLIC ROUTES
// ========================

// Route cho chatbox component (sử dụng ChatbotController đã có sẵn)
Route::post('/chatbot', [ChatbotController::class, 'answerChatbot']);
Route::post('/chatbot/advanced', [ChatbotController::class, 'answerChatbot']);

// Route để lấy thống kê chatbox (public)
Route::get('/chatbot/stats', function() {
    return response()->json([
        'status' => 'online',
        'response_time' => 'Tức thời',
        'availability' => '24/7',
        'topics' => [
            'Mua bán bất động sản',
            'Thuê nhà',
            'Tư vấn giá cả',
            'Thủ tục pháp lý',
            'Đánh giá dự án'
        ]
    ]);
});

// Route để lấy các câu hỏi gợi ý từ FAQ
Route::get('/chatbot/suggestions', [ChatbotController::class, 'getSuggestions']);

// Protected routes (authentication required) - Using custom simple token auth
Route::prefix('v1')->middleware('simpleauth')->group(function () {
    // User profile routes
    Route::get('/profile', [AuthController::class, 'profile']);
    Route::put('/profile', [AuthController::class, 'updateProfile']);
    Route::post('/logout', [AuthController::class, 'logout']);

    // New enhanced profile and transaction routes
    Route::get('/user/profile', [AuthController::class, 'getProfile']);
    Route::put('/user/profile', [AuthController::class, 'updateUserProfile']);
    Route::get('/user/transactions', [AuthController::class, 'getTransactions']);
    Route::get('/user/transactions/{id}', [AuthController::class, 'getTransactionDetail']);
    Route::get('/user/commissions', [AuthController::class, 'getCommissions']); // New commission API
    Route::get('/user/statistics', [AuthController::class, 'getTransactionStatistics']);

    // Appointment routes
    Route::get('/appointments', [AppointmentController::class, 'index']);
    Route::post('/appointments', [AppointmentController::class, 'store']);
    Route::get('/appointments/{id}', [AppointmentController::class, 'show']);
    Route::put('/appointments/{id}/status', [AppointmentController::class, 'updateStatus']);
    Route::delete('/appointments/{id}', [AppointmentController::class, 'cancel']);

    // Chat routes (authenticated)
    Route::post('/chat/send', [ChatbotController::class, 'sendMessage']);
    Route::get('/chat/conversations', [ChatbotController::class, 'getConversations']);
    Route::get('/chat/messages/{conversationId}', [ChatbotController::class, 'getMessages']);
});

// ========================
// LEGACY API ROUTES
// ========================

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// API lấy môi giới theo TransactionID
Route::get('/agents-by-transaction/{transactionId}', function ($transactionId) {
    $transaction = Transaction::find($transactionId);
    if (!$transaction) {
        return response()->json([], 404);
    }
    $agent = User::find($transaction->AgentID);
    if ($agent) {
        return response()->json([
            'AgentID' => $agent->UserID,
            'Name' => $agent->Name
        ]);
    } else {
        return response()->json([], 404);
    }
});

// API nhận câu hỏi và trả về câu trả lời từ chatbot
Route::post('/chatbot/answer', [ChatbotController::class, 'answerChatbot']);

// Chat API routes
Route::post('/chat/send', [ChatbotController::class, 'sendMessage']);
Route::get('/chat/conversations', [ChatbotController::class, 'getConversations']);
Route::get('/chat/messages/{conversationId}', [ChatbotController::class, 'getMessages']);
Route::post('/chat/admin-reply', [ChatbotController::class, 'adminReply']);

// Admin routes - for backward compatibility
Route::get('/chat/pending-conversations', [ChatbotController::class, 'getConversations']);
Route::get('/chat/conversation/{conversation_id}/messages', [ChatbotController::class, 'getMessages']);
