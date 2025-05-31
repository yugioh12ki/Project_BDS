<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\Transaction;
use App\Models\User;
use App\Http\Controllers\ChatbotController;

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

// Public routes (no authentication required)
Route::prefix('v1')->group(function () {
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

// Protected routes (authentication required)
Route::prefix('v1')->middleware('auth:sanctum')->group(function () {
    // User profile routes
    Route::get('/profile', [AuthController::class, 'profile']);
    Route::put('/profile', [AuthController::class, 'updateProfile']);
    Route::post('/logout', [AuthController::class, 'logout']);
    
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
