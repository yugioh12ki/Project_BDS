<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\Transaction;
use App\Models\User;
use App\Http\Controllers\ChatbotController;

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
