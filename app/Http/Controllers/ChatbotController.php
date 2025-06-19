<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Services\FirebaseServices;
use App\Services\GeminiAIService;
use App\Models\User;
use Carbon\Exceptions\Exception;
use Kreait\Firebase\Contract\Database;
use Kreait\Firebase\Exception\FirebaseException;

class ChatbotController extends Controller
{
    protected $firebase;
    protected $geminiAI;
    protected $table = 'chats';

    public function __construct(FirebaseServices $firebase, GeminiAIService $geminiAI)
    {
        $this->firebase = $firebase->getDatabase();
        $this->geminiAI = $geminiAI;
    }

    // Hiển thị trang admin chat
    public function index()
    {
        return view('_system.chatboxai.admin');
    }

    // Hiển thị trang user chat
    public function userChat()
    {
        return view('_system.chatboxai.user_chat');
    }

    // Hiển thị trang demo
    public function demo()
    {
        return view('_system.chatboxai.demo');
    }

    // Gửi tin nhắn vào Firebase
    public function sendMessage(Request $request)
    {
        $data = $request->validate([
            'conversation_id' => 'required',
            'sender_id' => 'required',
            'content' => 'required',
        ]);

        $ref = "chats/{$data['conversation_id']}/messages";

        try {
            $newMsg = [
                'sender_id' => $data['sender_id'],
                'content' => $data['content'],
                'timestamp' => now()->timestamp,
                'is_read' => false
            ];

            $this->firebase->getReference($ref)->push($newMsg);

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            Log::error('Firebase error: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    // API để AI chatbot trả lời (tương thích với cả mobile app và chatbox)
    public function answerChatbot(Request $request)
    {
        $data = $request->validate([
            'message' => 'nullable|string',
            'question' => 'nullable|string', // Hỗ trợ cả 2 format
            'conversation_id' => 'nullable|string',
            'session_id' => 'nullable|string', // Hỗ trợ session_id cho chatbox
            'user_type' => 'nullable|string' // 'guest' hoặc 'user'
        ]);

        // Xử lý message/question compatibility
        $message = $data['message'] ?? $data['question'] ?? '';
        if (empty($message)) {
            return response()->json([
                'success' => false,
                'error' => 'Message hoặc question là bắt buộc'
            ], 400);
        }

        // Xử lý conversation_id/session_id compatibility
        $conversationId = $data['conversation_id'] ?? $data['session_id'] ?? 'guest_' . uniqid();
        $userType = $data['user_type'] ?? (Auth::check() ? 'user' : 'guest');

        try {
            // Lấy thông tin user nếu có
            $user = null;
            if ($userType === 'user' && Auth::check()) {
                $user = Auth::user();
            }

            // Kiểm tra xem conversation đã được escalate hay chưa
            $conversationData = $this->firebase->getReference("chats/{$conversationId}")->getValue();
            $isEscalated = isset($conversationData['needs_admin']) && $conversationData['needs_admin'] === true;

            // Nếu đã escalate, chỉ thông báo chờ admin
            if ($isEscalated) {
                $waitingMessage = "⏳ **Đang chờ Admin phản hồi**\n\n" .
                    "Cuộc trò chuyện này đã được chuyển cho Admin. " .
                    "Vui lòng chờ Admin trả lời hoặc liên hệ trực tiếp:\n\n" .
                    "📧 **Email**: info.real_eslate@gmail.co.uk\n" .
                    "📞 **Hotline**: 0123456789\n\n" .
                    "💡 Bạn có thể bắt đầu cuộc trò chuyện mới bằng cách nhấn nút '+' trên chatbox.";

                // Lưu tin nhắn user
                $this->saveMessageToFirebase($conversationId, 'user', $message);

                // Response format cho chatbox
                if ($data['question'] ?? false || $data['session_id'] ?? false) {
                    return response()->json([
                        'status' => 'success',
                        'answer' => $waitingMessage,
                        'session_id' => $conversationId,
                        'source' => 'escalated_waiting',
                        'escalated' => true,
                        'timestamp' => now()->format('H:i')
                    ]);
                }

                return response()->json([
                    'success' => true,
                    'type' => 'escalated_waiting',
                    'response' => $waitingMessage,
                    'escalated' => true
                ]);
            }

            // Kiểm tra xem có yêu cầu liên hệ admin không
            if ($this->isAdminContactRequest($message)) {
                // Lưu tin nhắn user trước
                $this->saveMessageToFirebase($conversationId, 'user', $message);

                // Tạo phản hồi escalation
                $escalationMessage = "📞 **Đã chuyển cho Admin**\n\n" .
                    "Yêu cầu của bạn đã được chuyển đến bộ phận quản lý. " .
                    "Admin sẽ liên hệ và hỗ trợ bạn trong thời gian sớm nhất.\n\n" .
                    "🕒 **Thời gian phản hồi**: Trong vòng 30 phút (giờ hành chính)\n" .
                    "📧 **Hoặc liên hệ trực tiếp**: info.real_eslate@gmail.co.uk\n" .
                    "📞 **Hotline**: 0123456789\n\n" .
                    "Bạn có thể tiếp tục chat tại đây và Admin sẽ trả lời trực tiếp.";

                // Lưu tin nhắn escalation
                $this->saveMessageToFirebase($conversationId, 'bot', $escalationMessage);

                // Đánh dấu conversation cần admin
                $this->markConversationForAdmin($conversationId, $user, $message);

                // Trả về response cho chatbox
                if ($data['question'] ?? false || $data['session_id'] ?? false) {
                    return response()->json([
                        'status' => 'success',
                        'answer' => $escalationMessage,
                        'session_id' => $conversationId,
                        'source' => 'admin_escalated',
                        'escalated' => true,
                        'timestamp' => now()->format('H:i')
                    ]);
                }

                // Trả về response cho mobile app
                return response()->json([
                    'success' => true,
                    'type' => 'bot',
                    'response' => $escalationMessage,
                    'escalated' => true
                ]);
            }
            $user = null;
            if ($userType === 'user' && Auth::check()) {
                $user = Auth::user();
            }

            // Lấy lịch sử cuộc trò chuyện để cung cấp context
            $conversationHistory = $this->getConversationHistory($conversationId);

            // Sử dụng Gemini AI để trả lời
            $geminiResponse = $this->geminiAI->askGemini(
                $message,
                $this->loadFAQContext(),
                $conversationHistory
            );

            if ($geminiResponse && $geminiResponse['success'] && isset($geminiResponse['answer'])) {
                $aiResponse = $geminiResponse['answer'];
                $confidence = $geminiResponse['confidence'] ?? 0.8;

                // Lưu tin nhắn AI vào Firebase
                $this->saveMessageToFirebase($conversationId, 'bot', $aiResponse);

                // Kiểm tra nếu confidence thấp và user đã đăng nhập
                if ($confidence < 0.6 && $userType === 'user' && $user) {
                    // Thêm thông báo escalation
                    $escalationNote = "\n\n---\nLưu ý: Nếu câu trả lời trên không hữu ích, câu hỏi của bạn sẽ được chuyển đến admin để hỗ trợ tốt hơn.";
                    $fullResponse = $aiResponse . $escalationNote;

                    // Đánh dấu conversation để có thể escalate sau
                    $this->firebase->getReference("chats/{$conversationId}")->update([
                        'low_confidence_response' => true,
                        'last_confidence' => $confidence
                    ]);

                    // Response format cho chatbox
                    if (isset($data['question'])) {
                        return response()->json([
                            'status' => 'success',
                            'answer' => $fullResponse,
                            'session_id' => $conversationId,
                            'source' => 'ai_low_confidence',
                            'timestamp' => now()->format('H:i')
                        ]);
                    }

                    return response()->json([
                        'success' => true,
                        'type' => 'bot_low_confidence',
                        'response' => $fullResponse,
                        'confidence' => $confidence
                    ]);
                }

                // Response format cho chatbox
                if (isset($data['question'])) {
                    return response()->json([
                        'status' => 'success',
                        'answer' => $aiResponse,
                        'session_id' => $conversationId,
                        'source' => 'ai',
                        'timestamp' => now()->format('H:i')
                    ]);
                }

                return response()->json([
                    'success' => true,
                    'type' => 'bot',
                    'response' => $aiResponse,
                    'confidence' => $confidence
                ]);

            } else {
                // Gemini AI không thể trả lời
                if ($userType === 'guest') {
                    // Guest user: Trả lời mặc định
                    $defaultResponse = "Xin lỗi, tôi không hiểu câu hỏi của bạn. Bạn có thể đặt câu hỏi khác hoặc liên hệ qua email info.real_eslate@gmail.co.uk để được hỗ trợ tốt hơn.";
                    $this->saveMessageToFirebase($conversationId, 'bot', $defaultResponse);

                    // Response format cho chatbox
                    if (isset($data['question'])) {
                        return response()->json([
                            'status' => 'success',
                            'answer' => $defaultResponse,
                            'session_id' => $conversationId,
                            'source' => 'fallback',
                            'timestamp' => now()->format('H:i')
                        ]);
                    }

                    return response()->json([
                        'success' => true,
                        'type' => 'bot',
                        'response' => $defaultResponse
                    ]);
                } else {
                    // Logged-in user: Escalate to admin
                    $escalationMessage = "Câu hỏi của bạn khá phức tạp và đã được chuyển đến admin. Chúng tôi sẽ phản hồi sớm nhất có thể.";
                    $this->saveMessageToFirebase($conversationId, 'bot', $escalationMessage);

                    // Đánh dấu conversation cần admin
                    $this->markConversationForAdmin($conversationId, $user, $message);

                    // Response format cho chatbox
                    if (isset($data['question'])) {
                        return response()->json([
                            'status' => 'success',
                            'answer' => $escalationMessage,
                            'session_id' => $conversationId,
                            'source' => 'escalated',
                            'timestamp' => now()->format('H:i')
                        ]);
                    }

                    return response()->json([
                        'success' => true,
                        'type' => 'escalated',
                        'response' => $escalationMessage
                    ]);
                }
            }
        } catch (\Exception $e) {
            Log::error('Chatbot answer error: ' . $e->getMessage());

            // Fallback response
            $fallbackResponse = "Xin lỗi, hệ thống đang gặp sự cố tạm thời. Vui lòng thử lại sau hoặc liên hệ hỗ trợ.";
            $this->saveMessageToFirebase($conversationId, 'bot', $fallbackResponse);

            // Response format cho chatbox
            if (isset($data['question'])) {
                return response()->json([
                    'status' => 'error',
                    'answer' => $fallbackResponse,
                    'timestamp' => now()->format('H:i')
                ], 500);
            }

            return response()->json([
                'success' => true,
                'type' => 'bot',
                'response' => $fallbackResponse
            ]);
        }
    }

    // Tải FAQ context cho Claude AI
    private function loadFAQContext()
    {
        try {
            $faqPath = storage_path('app/faq.json');

            if (!file_exists($faqPath)) {
                return null;
            }

            $faqContent = file_get_contents($faqPath);
            return json_decode($faqContent, true) ?? [];

        } catch (\Exception $e) {
            Log::error('Load FAQ context error: ' . $e->getMessage());
            return null;
        }
    }

    // Lấy lịch sử cuộc trò chuyện để cung cấp context cho Claude
    private function getConversationHistory($conversationId, $limit = 10)
    {
        try {
            $messages = $this->firebase->getReference("chats/{$conversationId}/messages")->getValue() ?? [];

            $history = [];
            $messageCount = 0;

            // Sắp xếp messages theo timestamp
            uasort($messages, function($a, $b) {
                return ($a['timestamp'] ?? 0) - ($b['timestamp'] ?? 0);
            });

            // Lấy những tin nhắn gần nhất (không bao gồm tin nhắn hiện tại)
            $recentMessages = array_slice($messages, -($limit + 1), $limit);

            foreach ($recentMessages as $message) {
                if ($messageCount >= $limit) break;

                $role = 'user';
                if (isset($message['sender_id'])) {
                    if ($message['sender_id'] === 'bot') {
                        $role = 'assistant';
                    } elseif ($message['sender_id'] === 'admin') {
                        $role = 'assistant'; // Treat admin messages as assistant for context
                    }
                }

                $history[] = [
                    'role' => $role,
                    'content' => $message['content'] ?? ''
                ];

                $messageCount++;
            }

            return $history;

        } catch (\Exception $e) {
            Log::error('Get conversation history error: ' . $e->getMessage());
            return [];
        }
    }

    // Lưu tin nhắn vào Firebase
    private function saveMessageToFirebase($conversationId, $senderId, $content, $senderName = null)
    {
        try {
            $ref = "chats/{$conversationId}/messages";

            $message = [
                'sender_id' => $senderId,
                'content' => $content,
                'timestamp' => now()->timestamp,
                'is_read' => false
            ];

            if ($senderName) {
                $message['sender_name'] = $senderName;
            }

            $this->firebase->getReference($ref)->push($message);

            // Cập nhật conversation info
            $this->firebase->getReference("chats/{$conversationId}")->update([
                'last_message' => $content,
                'last_message_time' => now()->timestamp,
                'has_new_message' => true
            ]);

        } catch (\Exception $e) {
            Log::error('Save message to Firebase error: ' . $e->getMessage());
        }
    }

    /**
     * Kiểm tra xem tin nhắn có yêu cầu liên hệ admin không
     */
    private function isAdminContactRequest($message)
    {
        $message = mb_strtolower($message);

        // Danh sách từ khóa liên quan đến liên hệ admin
        $adminKeywords = [
            // Tiếng Việt - Liên hệ trực tiếp
            'liên hệ admin',
            'liên hệ với admin',
            'liên hệ quản trị',
            'liên hệ quản lý',
            'gặp admin',
            'gặp quản trị',
            'gặp quản lý',
            'nói chuyện với admin',
            'nói chuyện với quản trị',
            'tôi muốn gặp admin',
            'tôi muốn liên hệ admin',
            'tôi muốn nói chuyện với admin',
            'cho tôi liên hệ admin',
            'có admin không',
            'admin ở đâu',
            'kết nối admin',
            'chuyển cho admin',
            'chuyển đến admin',
            'cần admin',
            'cần quản trị',
            'cần quản lý',

            // Tiếng Việt - Hỗ trợ chuyên sâu
            'hỗ trợ trực tiếp',
            'hỗ trợ cá nhân',
            'tư vấn trực tiếp',
            'tư vấn cá nhân',
            'hỗ trợ chuyên viên',
            'nói chuyện với người thật',
            'gặp người thật',
            'không muốn chat với bot',
            'tôi cần người thật',

            // Tiếng Anh
            'escalate',
            'escalate to admin',
            'contact admin',
            'speak to admin',
            'talk to admin',
            'admin help',
            'human support',
            'human agent',
            'real person',
            'live chat',
            'live support',
            'transfer to admin',
            'need admin',
            'want admin',

            // Cụm từ khác
            'không giải quyết được',
            'cần hỗ trợ thêm',
            'vấn đề phức tạp',
            'cần giải quyết gấp',
            'khẩn cấp',
            'urgent',
            'emergency'
        ];

        // Kiểm tra xem message có chứa từ khóa nào không
        foreach ($adminKeywords as $keyword) {
            if (str_contains($message, $keyword)) {
                return true;
            }
        }

        return false;
    }

    // Đánh dấu conversation cần admin
    private function markConversationForAdmin($conversationId, $user, $originalMessage)
    {
        try {
            $this->firebase->getReference("chats/{$conversationId}")->update([
                'needs_admin' => true,
                'user_id' => $user->UserID,
                'user_name' => $user->Name,
                'user_email' => $user->Email,
                'escalated_at' => now()->timestamp,
                'original_question' => $originalMessage,
                'status' => 'pending_admin'
            ]);
        } catch (\Exception $e) {
            Log::error('Mark conversation for admin error: ' . $e->getMessage());
        }
    }

    // Lấy danh sách conversations
    public function getConversations(Request $request)
    {
        try {
            $type = $request->get('type', 'all'); // 'pending' or 'all'

            $conversations = $this->firebase->getReference('chats')->getValue() ?? [];

            $result = [];
            foreach ($conversations as $id => $conversation) {
                if ($type === 'pending' && (!isset($conversation['needs_admin']) || !$conversation['needs_admin'])) {
                    continue;
                }

                $result[] = [
                    'id' => $id,
                    'user_name' => $conversation['user_name'] ?? 'Guest',
                    'user_email' => $conversation['user_email'] ?? '',
                    'last_message' => $conversation['last_message'] ?? '',
                    'last_message_time' => $conversation['last_message_time'] ?? 0,
                    'needs_admin' => $conversation['needs_admin'] ?? false,
                    'has_new_message' => $conversation['has_new_message'] ?? false,
                    'status' => $conversation['status'] ?? 'active'
                ];
            }

            // Sắp xếp theo thời gian mới nhất
            usort($result, function($a, $b) {
                return $b['last_message_time'] - $a['last_message_time'];
            });

            return response()->json([
                'success' => true,
                'conversations' => $result
            ]);

        } catch (\Exception $e) {
            Log::error('Get conversations error: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    // Lấy tin nhắn của một conversation
    public function getMessages($conversationId)
    {
        try {
            $messages = $this->firebase->getReference("chats/{$conversationId}/messages")->getValue() ?? [];

            $result = [];
            foreach ($messages as $id => $message) {
                $result[] = [
                    'id' => $id,
                    'sender_id' => $message['sender_id'],
                    'sender_name' => $message['sender_name'] ?? '',
                    'content' => $message['content'],
                    'timestamp' => $message['timestamp'],
                    'is_read' => $message['is_read'] ?? false
                ];
            }

            // Sắp xếp theo thời gian
            usort($result, function($a, $b) {
                return $a['timestamp'] - $b['timestamp'];
            });

            return response()->json([
                'success' => true,
                'messages' => $result
            ]);

        } catch (\Exception $e) {
            Log::error('Get messages error: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    // Admin gửi tin nhắn trong conversation
    public function adminReply(Request $request)
    {
        $data = $request->validate([
            'conversation_id' => 'required|string',
            'message' => 'required|string'
        ]);

        try {
            $adminName = Auth::user()->Name ?? 'Admin';

            // Lưu tin nhắn admin
            $this->saveMessageToFirebase(
                $data['conversation_id'],
                'admin',
                $data['message'],
                $adminName
            );

            // Cập nhật status conversation
            $this->firebase->getReference("chats/{$data['conversation_id']}")->update([
                'needs_admin' => false,
                'status' => 'admin_replied',
                'admin_replied_at' => now()->timestamp,
                'admin_name' => $adminName
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Tin nhắn đã được gửi'
            ]);

        } catch (\Exception $e) {
            Log::error('Admin reply error: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Lấy danh sách người dùng cho admin messaging
     */
    public function getUsersList(Request $request)
    {
        try {
            $limit = $request->get('limit', 100); // Default 100, có thể truyền limit khác
            $limit = min($limit, 200); // Giới hạn tối đa 200

            $users = User::select('UserID', 'Name', 'Email', 'Role')
                ->orderBy('Name', 'asc')
                ->limit($limit)
                ->get();

            return response()->json([
                'success' => true,
                'users' => $users,
                'total' => $users->count()
            ]);
        } catch (\Exception $e) {
            Log::error('Error getting users list: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Lỗi khi lấy danh sách người dùng'
            ], 500);
        }
    }

    /**
     * Gửi tin nhắn đến người dùng cụ thể qua Firebase
     */
    public function sendToUser(Request $request)
    {
        try {
            $data = $request->validate([
                'user_id' => 'required',
                'user_name' => 'required',
                'message' => 'required|string|max:500'
            ]);

            // Tạo conversation ID duy nhất
            $conversationId = 'admin_to_' . $data['user_id'] . '_' . time();

            // Tin nhắn gửi đến Firebase
            $messageData = [
                'content' => $data['message'],
                'sender_id' => 'admin',
                'sender_name' => 'Admin',
                'recipient_id' => $data['user_id'],
                'recipient_name' => $data['user_name'],
                'timestamp' => time(),
                'type' => 'admin_message',
                'read' => false
            ];

            // Lưu vào Firebase Realtime Database
            $messagePath = 'chats/' . $conversationId . '/messages';
            $messageRef = $this->firebase->getReference($messagePath)->push($messageData);

            // Lưu thông tin conversation
            $conversationData = [
                'conversation_id' => $conversationId,
                'type' => 'admin_to_user',
                'admin_id' => 'admin',
                'user_id' => $data['user_id'],
                'user_name' => $data['user_name'],
                'last_message' => $data['message'],
                'last_message_time' => time(),
                'created_at' => time(),
                'status' => 'active'
            ];

            $this->firebase->getReference('conversations/' . $conversationId)->set($conversationData);

            // Tạo notification cho user
            $notificationData = [
                'title' => 'Tin nhắn từ Admin',
                'body' => substr($data['message'], 0, 100) . (strlen($data['message']) > 100 ? '...' : ''),
                'type' => 'admin_message',
                'conversation_id' => $conversationId,
                'timestamp' => time(),
                'read' => false
            ];

            $this->firebase->getReference('notifications/' . $data['user_id'])->push($notificationData);

            Log::info('Admin message sent to user', [
                'user_id' => $data['user_id'],
                'conversation_id' => $conversationId,
                'message_id' => $messageRef->getKey()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Tin nhắn đã được gửi thành công',
                'conversation_id' => $conversationId,
                'message_id' => $messageRef->getKey()
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Dữ liệu không hợp lệ',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Error sending message to user: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Lỗi khi gửi tin nhắn: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Gửi thông báo broadcast đến nhiều người dùng
     */
    public function sendBroadcast(Request $request)
    {
        try {
            $data = $request->validate([
                'message' => 'required|string|max:500',
                'target_roles' => 'required|array',
                'target_roles.*' => 'in:Customer,Agent,Owner,Admin'
            ]);

            // Lấy danh sách users theo roles
            $users = User::whereIn('Role', $data['target_roles'])
                ->select('UserID', 'Name', 'Email', 'Role')
                ->get();

            if ($users->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không tìm thấy người dùng phù hợp'
                ]);
            }

            $successCount = 0;
            $errorCount = 0;
            $broadcastId = 'broadcast_' . time();

            foreach ($users as $user) {
                try {
                    // Tạo conversation cho từng user
                    $conversationId = $broadcastId . '_to_' . $user->UserID;

                    $messageData = [
                        'content' => $data['message'],
                        'sender_id' => 'admin',
                        'sender_name' => 'Admin System',
                        'recipient_id' => $user->UserID,
                        'recipient_name' => $user->Name,
                        'timestamp' => time(),
                        'type' => 'broadcast',
                        'broadcast_id' => $broadcastId,
                        'read' => false
                    ];

                    // Lưu vào Firebase
                    $messagePath = 'chats/' . $conversationId . '/messages';
                    $this->firebase->getReference($messagePath)->push($messageData);

                    // Tạo notification
                    $notificationData = [
                        'title' => 'Thông báo từ Admin',
                        'body' => substr($data['message'], 0, 100) . (strlen($data['message']) > 100 ? '...' : ''),
                        'type' => 'broadcast',
                        'conversation_id' => $conversationId,
                        'broadcast_id' => $broadcastId,
                        'timestamp' => time(),
                        'read' => false
                    ];

                    $this->firebase->getReference('notifications/' . $user->UserID)->push($notificationData);
                    $successCount++;

                } catch (\Exception $e) {
                    Log::error('Error sending broadcast to user ' . $user->UserID . ': ' . $e->getMessage());
                    $errorCount++;
                }
            }

            Log::info('Broadcast message sent', [
                'broadcast_id' => $broadcastId,
                'total_users' => $users->count(),
                'success_count' => $successCount,
                'error_count' => $errorCount,
                'target_roles' => $data['target_roles']
            ]);

            return response()->json([
                'success' => true,
                'message' => "Thông báo đã được gửi thành công đến {$successCount} người dùng" .
                            ($errorCount > 0 ? " (Lỗi: {$errorCount})" : ''),
                'broadcast_id' => $broadcastId,
                'success_count' => $successCount,
                'error_count' => $errorCount,
                'total_users' => $users->count()
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Dữ liệu không hợp lệ',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Error sending broadcast message: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Lỗi khi gửi thông báo: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Lấy danh sách 5 users mặc định khi load messaging tab
     */
    public function getDefaultUsers()
    {
        try {
            $users = User::select('UserID', 'Name', 'Email', 'Role')
                ->orderBy('Name', 'asc')
                ->limit(5)
                ->get();

            return response()->json([
                'success' => true,
                'users' => $users
            ]);
        } catch (\Exception $e) {
            Log::error('Error getting default users: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Lỗi khi lấy danh sách người dùng mặc định'
            ], 500);
        }
    }

    // Quản lý FAQ - Hiển thị trang quản lý
    public function manageFAQ()
    {
        try {
            $faqPath = storage_path('app/faq.json');
            $faqs = [];

            if (file_exists($faqPath)) {
                $faqContent = file_get_contents($faqPath);
                $faqData = json_decode($faqContent, true) ?? [];

                // Chuyển đổi từ cấu trúc hiện tại sang dạng array để hiển thị
                foreach ($faqData as $key => $faq) {
                    if (isset($faq['questions']) && isset($faq['answer'])) {
                        $faqs[] = [
                            'key' => $key,
                            'question' => $faq['questions'][0] ?? '', // Lấy câu hỏi đầu tiên
                            'answer' => $faq['answer'],
                            'keywords' => $faq['questions'] ?? [], // Sử dụng tất cả questions làm keywords
                            'created_at' => now()->toISOString()
                        ];
                    }
                }
            }

            return view('_system.chatboxai.faq_manage', compact('faqs'));

        } catch (\Exception $e) {
            Log::error('Manage FAQ error: ' . $e->getMessage());
            return back()->with('error', 'Lỗi khi tải dữ liệu FAQ: ' . $e->getMessage());
        }
    }

    // Thêm FAQ mới
    public function addFAQ(Request $request)
    {
        $data = $request->validate([
            'question' => 'required|string|max:500',
            'answer' => 'required|string|max:2000',
            'keywords' => 'nullable|string|max:200'
        ]);

        try {
            $faqPath = storage_path('app/faq.json');
            $faqs = [];

            if (file_exists($faqPath)) {
                $faqContent = file_get_contents($faqPath);
                $faqs = json_decode($faqContent, true) ?? [];
            }

            // Tạo key cho FAQ mới
            $newKey = 'key_custom_' . time();

            // Xử lý keywords thành array questions
            $questions = [$data['question']];
            if (!empty($data['keywords'])) {
                $additionalQuestions = array_map('trim', explode(',', $data['keywords']));
                $questions = array_merge($questions, $additionalQuestions);
            }

            // Thêm FAQ mới với format đúng
            $faqs[$newKey] = [
                'questions' => $questions,
                'answer' => $data['answer']
            ];

            // Lưu lại file
            file_put_contents($faqPath, json_encode($faqs, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

            return response()->json([
                'success' => true,
                'message' => 'FAQ đã được thêm thành công',
                'faq' => $faqs[$newKey],
                'total_faqs' => count($faqs)
            ]);

        } catch (\Exception $e) {
            Log::error('Add FAQ error: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    // Cập nhật FAQ
    public function updateFAQ(Request $request, $index)
    {
        $data = $request->validate([
            'question' => 'required|string|max:500',
            'answer' => 'required|string|max:2000',
            'keywords' => 'nullable|string|max:200'
        ]);

        try {
            $faqPath = storage_path('app/faq.json');

            if (!file_exists($faqPath)) {
                return response()->json(['error' => 'File FAQ không tồn tại'], 404);
            }

            $faqContent = file_get_contents($faqPath);
            $faqs = json_decode($faqContent, true) ?? [];

            if (!isset($faqs[$index])) {
                return response()->json(['error' => 'FAQ không tồn tại'], 404);
            }

            // Cập nhật FAQ
            $faqs[$index] = [
                'question' => $data['question'],
                'answer' => $data['answer'],
                'keywords' => $data['keywords'] ? explode(',', $data['keywords']) : [],
                'created_at' => $faqs[$index]['created_at'] ?? now()->toISOString(),
                'updated_at' => now()->toISOString()
            ];

            // Lưu lại file
            file_put_contents($faqPath, json_encode($faqs, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

            return response()->json([
                'success' => true,
                'message' => 'FAQ đã được cập nhật thành công',
                'faq' => $faqs[$index]
            ]);

        } catch (\Exception $e) {
            Log::error('Update FAQ error: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    // Xóa FAQ
    public function deleteFAQ($index)
    {
        try {
            $faqPath = storage_path('app/faq.json');

            if (!file_exists($faqPath)) {
                return response()->json(['error' => 'File FAQ không tồn tại'], 404);
            }

            $faqContent = file_get_contents($faqPath);
            $faqs = json_decode($faqContent, true) ?? [];

            if (!isset($faqs[$index])) {
                return response()->json(['error' => 'FAQ không tồn tại'], 404);
            }

            // Xóa FAQ
            $deletedFAQ = $faqs[$index];
            array_splice($faqs, $index, 1);

            // Lưu lại file
            file_put_contents($faqPath, json_encode($faqs, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

            return response()->json([
                'success' => true,
                'message' => 'FAQ đã được xóa thành công',
                'deleted_faq' => $deletedFAQ,
                'total_faqs' => count($faqs)
            ]);

        } catch (\Exception $e) {
            Log::error('Delete FAQ error: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    // Test methods cho Gemini AI
    public function showTestPage()
    {
        return view('test.gemini');
    }

    /**
     * Test Gemini AI integration
     */
    public function testGemini(Request $request)
    {
        try {
            $message = $request->input('message', 'Xin chào');

            // Test basic Gemini AI call
            $faqContext = $this->loadFAQContext();
            $response = $this->geminiAI->askGemini($message, $faqContext, []);

            return response()->json([
                'success' => true,
                'message' => $message,
                'gemini_response' => $response,
                'faq_loaded' => !empty($faqContext),
                'timestamp' => now()
            ]);

        } catch (\Exception $e) {
            Log::error('Gemini test error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Test FAQ loading and Gemini integration
     */
    public function testFAQ()
    {
        try {
            // Test FAQ loading
            $faqContext = $this->loadFAQContext();

            // Test Gemini with FAQ context
            $testQuestion = "Công ty mở cửa lúc mấy giờ?";
            $response = $this->geminiAI->askGemini($testQuestion, $faqContext, []);

            return response()->json([
                'success' => true,
                'faq_loaded' => !empty($faqContext),
                'faq_count' => count($faqContext ?? []),
                'test_question' => $testQuestion,
                'gemini_response' => $response,
                'faq_keys' => array_keys($faqContext ?? [])
            ]);

        } catch (\Exception $e) {
            Log::error('FAQ test error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Test database search capabilities
     */
    public function testDatabaseSearch(Request $request)
    {
        try {
            $startTime = microtime(true);
            $message = $request->input('message', 'Tìm bất động sản ở Hà Nội');

            // Test database search với Gemini AI
            $faqContext = $this->loadFAQContext();
            $response = $this->geminiAI->askGemini($message, $faqContext, []);

            $endTime = microtime(true);
            $responseTime = round(($endTime - $startTime) * 1000, 2);

            // Get database stats and metadata
            $stats = $this->geminiAI->getDatabaseStats();

            // Check if database was actually searched
            $databaseSearched = $response['database_searched'] ?? false;
            $searchResultsCount = $response['search_results_count'] ?? 0;

            return response()->json([
                'success' => true,
                'message' => $message,
                'gemini_response' => $response,
                'database_searched' => $databaseSearched,
                'search_results_count' => $searchResultsCount,
                'response_time' => $responseTime,
                'search_metadata' => [
                    'database_searched' => $databaseSearched,
                    'search_results_count' => $searchResultsCount
                ],
                'database_stats' => $stats,
                'test_type' => 'database_search',
                'timestamp' => now()
            ]);

        } catch (\Exception $e) {
            Log::error('Database search test error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Test smart search functionality
     */
    public function testSmartSearch(Request $request)
    {
        try {
            $startTime = microtime(true);
            $query = $request->input('query', 'BDS');
            $limit = $request->input('limit', 20);

            // Test smart search
            $results = $this->geminiAI->smartSearch($query, $limit);

            $endTime = microtime(true);
            $searchTime = round(($endTime - $startTime) * 1000, 2);

            // Đếm tổng kết quả
            $totalResults = 0;
            $summary = [];
            foreach ($results as $type => $items) {
                $count = count($items);
                $summary[$type] = $count;
                $totalResults += $count;
            }

            return response()->json([
                'success' => true,
                'query' => $query,
                'limit' => $limit,
                'results' => $results,
                'summary' => $summary,
                'total_results' => $totalResults,
                'search_time' => $searchTime,
                'test_type' => 'smart_search',
                'timestamp' => now()
            ]);

        } catch (\Exception $e) {
            Log::error('Smart search test error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Test various search scenarios
     */
    public function testSearchScenarios(Request $request)
    {
        try {
            $testStartTime = microtime(true);

            $scenarios = [
                ['query' => 'Tìm bất động sản ở Hà Nội', 'description' => 'Tìm kiếm BĐS theo địa điểm'],
                ['query' => 'Có nhà nào cho thuê không?', 'description' => 'Tìm kiếm BĐS cho thuê'],
                ['query' => 'Thống kê tổng quan hệ thống', 'description' => 'Lấy thống kê database'],
                ['query' => 'Tìm môi giới tên Nam', 'description' => 'Tìm kiếm agent theo tên'],
                ['query' => 'Có giao dịch nào đang pending không?', 'description' => 'Kiểm tra giao dịch pending'],
                ['query' => 'Danh sách loại bất động sản', 'description' => 'Lấy danh mục BĐS'],
                ['query' => 'Hoa hồng chưa thanh toán', 'description' => 'Tìm hoa hồng chưa thanh toán'],
                ['query' => 'Feedback từ khách hàng', 'description' => 'Lấy feedback mới nhất']
            ];

            $scenarioResults = [];
            $faqContext = $this->loadFAQContext();
            $totalResponseTime = 0;
            $successfulScenarios = 0;
            $detailedResults = [];

            foreach ($scenarios as $scenario) {
                $startTime = microtime(true);

                try {
                    $response = $this->geminiAI->askGemini($scenario['query'], $faqContext, []);
                    $endTime = microtime(true);
                    $responseTime = round(($endTime - $startTime) * 1000, 2);
                    $totalResponseTime += $responseTime;

                    $databaseUsed = isset($response['metadata']['found_data']) ? $response['metadata']['found_data'] : false;
                    $resultsCount = isset($response['metadata']['search_results_count']) ? $response['metadata']['search_results_count'] : 0;

                    $scenarioResults[] = [
                        'description' => $scenario['description'],
                        'query' => $scenario['query'],
                        'success' => true,
                        'response_time' => $responseTime,
                        'database_used' => $databaseUsed,
                        'results_count' => $resultsCount,
                        'confidence' => $response['confidence'] ?? 0
                    ];

                    $detailedResults[] = [
                        'scenario' => $scenario,
                        'response' => $response,
                        'processing_time_ms' => $responseTime,
                        'database_used' => $databaseUsed,
                        'results_count' => $resultsCount
                    ];

                    $successfulScenarios++;

                } catch (\Exception $e) {
                    $endTime = microtime(true);
                    $responseTime = round(($endTime - $startTime) * 1000, 2);

                    $scenarioResults[] = [
                        'description' => $scenario['description'],
                        'query' => $scenario['query'],
                        'success' => false,
                        'response_time' => $responseTime,
                        'database_used' => false,
                        'results_count' => 0,
                        'error' => $e->getMessage()
                    ];

                    $detailedResults[] = [
                        'scenario' => $scenario,
                        'error' => $e->getMessage(),
                        'processing_time_ms' => $responseTime
                    ];
                }
            }

            $testEndTime = microtime(true);
            $totalTime = round(($testEndTime - $testStartTime) * 1000, 2);
            $averageResponseTime = $successfulScenarios > 0 ? round($totalResponseTime / $successfulScenarios, 2) : 0;

            return response()->json([
                'success' => true,
                'total_scenarios' => count($scenarios),
                'successful_scenarios' => $successfulScenarios,
                'total_time' => $totalTime,
                'average_response_time' => $averageResponseTime,
                'scenario_results' => $scenarioResults,
                'detailed_results' => $detailedResults,
                'test_type' => 'search_scenarios',
                'timestamp' => now()
            ]);

        } catch (\Exception $e) {
            Log::error('Search scenarios test error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Test full chatbot functionality
     */
    public function testFullChatbot(Request $request)
    {
        try {
            $data = $request->validate([
                'message' => 'required|string',
                'user_type' => 'required|string' // 'guest' hoặc 'user'
            ]);

            // Tạo conversation_id test
            $conversationId = 'test_' . time();

            // Gọi method answerChatbot để test toàn bộ logic
            $testRequest = Request::create('/api/chatbot', 'POST', [
                'message' => $data['message'],
                'conversation_id' => $conversationId,
                'user_type' => $data['user_type']
            ]);

            $response = $this->answerChatbot($testRequest);

            return response()->json([
                'success' => true,
                'test_type' => 'full_chatbot',
                'input' => $data,
                'conversation_id' => $conversationId,
                'chatbot_response' => $response->getData()
            ]);

        } catch (\Exception $e) {
            Log::error('Full chatbot test error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Test database connection and performance
     */
    public function testDatabaseConnection()
    {
        try {
            $startTime = microtime(true);

            // Test database connection
            $stats = $this->geminiAI->getDatabaseStats();

            $endTime = microtime(true);
            $responseTime = round(($endTime - $startTime) * 1000, 2);

            return response()->json([
                'success' => true,
                'database_connected' => true,
                'response_time' => $responseTime,
                'stats' => $stats,
                'test_type' => 'database_connection',
                'timestamp' => now()
            ]);

        } catch (\Exception $e) {
            Log::error('Database connection test error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'database_connected' => false,
                'error' => $e->getMessage(),
                'test_type' => 'database_connection',
                'timestamp' => now()
            ], 500);
        }
    }

    /**
     * Get comprehensive database integration status
     */
    public function getDatabaseIntegrationStatus()
    {
        try {
            // Test various components
            $status = [
                'gemini_api' => [
                    'configured' => !empty(env('GEMINI_API_KEY')),
                    'status' => 'unknown'
                ],
                'database' => [
                    'connected' => false,
                    'status' => 'unknown'
                ],
                'faq' => [
                    'loaded' => false,
                    'count' => 0
                ],
                'integration_features' => [
                    'search_properties' => false,
                    'search_users' => false,
                    'search_transactions' => false,
                    'smart_search' => false
                ]
            ];

            // Test Gemini API
            try {
                $this->geminiAI->askGemini('test', [], []);
                $status['gemini_api']['status'] = 'working';
            } catch (\Exception $e) {
                $status['gemini_api']['status'] = 'error: ' . substr($e->getMessage(), 0, 50);
            }

            // Test database
            try {
                $stats = $this->geminiAI->getDatabaseStats();
                $status['database']['connected'] = true;
                $status['database']['status'] = 'connected';
                $status['database']['stats'] = $stats;
            } catch (\Exception $e) {
                $status['database']['status'] = 'error: ' . substr($e->getMessage(), 0, 50);
            }

            // Test FAQ
            try {
                $faqContext = $this->loadFAQContext();
                $status['faq']['loaded'] = !empty($faqContext);
                $status['faq']['count'] = count($faqContext ?? []);
            } catch (\Exception $e) {
                $status['faq']['error'] = substr($e->getMessage(), 0, 50);
            }

            // Test integration features
            try {
                $searchResults = $this->geminiAI->smartSearch('test', 1);
                $status['integration_features']['smart_search'] = true;

                if (isset($searchResults['properties'])) {
                    $status['integration_features']['search_properties'] = true;
                }
                if (isset($searchResults['users'])) {
                    $status['integration_features']['search_users'] = true;
                }
                if (isset($searchResults['transactions'])) {
                    $status['integration_features']['search_transactions'] = true;
                }
            } catch (\Exception $e) {
                $status['integration_features']['error'] = substr($e->getMessage(), 0, 50);
            }

            return response()->json([
                'success' => true,
                'status' => $status,
                'overall_health' => $this->calculateOverallHealth($status),
                'timestamp' => now()
            ]);

        } catch (\Exception $e) {
            Log::error('Database integration status error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Calculate overall system health score
     */
    private function calculateOverallHealth($status)
    {
        $score = 0;
        $maxScore = 0;

        // Gemini API (25 points)
        $maxScore += 25;
        if ($status['gemini_api']['configured']) $score += 10;
        if ($status['gemini_api']['status'] === 'working') $score += 15;

        // Database (25 points)
        $maxScore += 25;
        if ($status['database']['connected']) $score += 25;

        // FAQ (20 points)
        $maxScore += 20;
        if ($status['faq']['loaded']) $score += 20;

        // Integration features (30 points)
        $maxScore += 30;
        if ($status['integration_features']['smart_search']) $score += 10;
        if ($status['integration_features']['search_properties']) $score += 7;
        if ($status['integration_features']['search_users']) $score += 7;
        if ($status['integration_features']['search_transactions']) $score += 6;

        $percentage = $maxScore > 0 ? round(($score / $maxScore) * 100) : 0;

        $health = 'Poor';
        if ($percentage >= 90) $health = 'Excellent';
        elseif ($percentage >= 75) $health = 'Good';
        elseif ($percentage >= 50) $health = 'Fair';

        return [
            'score' => $score,
            'max_score' => $maxScore,
            'percentage' => $percentage,
            'health' => $health
        ];
    }

    /**
     * Lấy danh sách câu hỏi gợi ý từ file FAQ
     */
    public function getSuggestions()
    {
        try {
            // Đọc file FAQ
            $faqPath = storage_path('app/faq.json');

            if (!file_exists($faqPath)) {
                // Fallback nếu file không tồn tại
                $suggestions = [
                    'Tôi muốn mua nhà, cần tư vấn gì?',
                    'Giá nhà đất hiện tại như thế nào?',
                    'Thủ tục mua bán nhà cần những gì?',
                    'Cách liên hệ hỗ trợ',
                    'Giờ làm việc của công ty'
                ];
            } else {
                $faqContent = file_get_contents($faqPath);
                $faqData = json_decode($faqContent, true);

                $suggestions = [];

                if ($faqData) {
                    // Lấy 1-2 câu hỏi từ mỗi category FAQ để làm suggestions
                    foreach ($faqData as $category => $data) {
                        if (isset($data['questions']) && is_array($data['questions'])) {
                            // Lấy tối đa 2 câu hỏi đầu tiên từ mỗi category
                            $categoryQuestions = array_slice($data['questions'], 0, 2);
                            $suggestions = array_merge($suggestions, $categoryQuestions);
                        }
                    }

                    // Giới hạn tổng số suggestions về 8-10 câu
                    $suggestions = array_slice($suggestions, 0, 10);
                }

                // Nếu không có suggestions từ FAQ, dùng fallback
                if (empty($suggestions)) {
                    $suggestions = [
                        'Tôi muốn mua nhà, cần tư vấn gì?',
                        'Giá nhà đất hiện tại như thế nào?',
                        'Thủ tục mua bán nhà cần những gì?',
                        'Cách liên hệ hỗ trợ',
                        'Giờ làm việc của công ty'
                    ];
                }
            }

            return response()->json([
                'suggestions' => $suggestions,
                'status' => 'success',
                'total' => count($suggestions)
            ]);

        } catch (Exception $e) {
            // Fallback response nếu có lỗi
            return response()->json([
                'suggestions' => [
                    'Tôi muốn mua nhà, cần tư vấn gì?',
                    'Giá nhà đất hiện tại như thế nào?',
                    'Thủ tục mua bán nhà cần những gì?',
                    'Cách liên hệ hỗ trợ',
                    'Giờ làm việc của công ty'
                ],
                'status' => 'success',
                'note' => 'Using fallback suggestions due to error'
            ]);
        }
    }
}
