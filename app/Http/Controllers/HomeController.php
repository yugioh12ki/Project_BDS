<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Models\Property;
use App\Models\DanhMucBDS;
use App\Services\GeminiAIService;
use Illuminate\Support\Facades\Log;

class HomeController extends Controller
{
    public function index()
    {
        // Lấy danh sách danh mục
        $danhmucs = DanhMucBDS::all();

        // Lấy BĐS Bán nổi bật (4 properties with TypePro = 'Sale')
        $saleProperties = Property::with(['danhMuc', 'chiTiet', 'images'])
            ->where('Status', 'active')
            ->where('TypePro', 'Sale')
            ->orderBy('Price', 'desc')
            ->limit(4)
            ->get();

        // Lấy BĐS Thuê nổi bật (4 properties with TypePro = 'Rent')
        $rentProperties = Property::with(['danhMuc', 'chiTiet', 'images'])
            ->where('Status', 'active')
            ->where('TypePro', 'Rent')
            ->orderBy('Price', 'desc')
            ->limit(4)
            ->get();

        return view('trangchu.index', compact('danhmucs', 'saleProperties', 'rentProperties'));
    }

    public function getUser($id)
    {
        return response()->json(User::find($id));
    }

    // Method mới cho chatbox component với FAQ integration
    public function chatbot(Request $request)
    {
        try {
            $request->validate([
                'question' => 'required|string|max:500'
            ]);

            $question = $request->input('question');

            // Kiểm tra FAQ trước
            $faqAnswer = $this->checkFAQ($question);
            if ($faqAnswer) {
                return response()->json([
                    'status' => 'success',
                    'answer' => $faqAnswer,
                    'source' => 'faq',
                    'timestamp' => now()->format('H:i')
                ]);
            }

            // Nếu không có trong FAQ, dùng AI
            $context = "Bạn là trợ lý ảo chuyên về bất động sản. Hãy trả lời câu hỏi một cách thân thiện và hữu ích về các vấn đề liên quan đến mua bán, thuê nhà, giá cả, thủ tục pháp lý bất động sản tại Việt Nam.";

            $fullPrompt = $context . "\n\nCâu hỏi: " . $question;

            // Khởi tạo GeminiAIService
            $geminiService = new GeminiAIService();
            $response = $geminiService->askGemini($fullPrompt);

            return response()->json([
                'status' => 'success',
                'answer' => $response,
                'source' => 'ai',
                'timestamp' => now()->format('H:i')
            ]);

        } catch (\Exception $e) {
            Log::error('Chatbot error: ' . $e->getMessage());

            return response()->json([
                'status' => 'error',
                'answer' => 'Xin lỗi, có lỗi xảy ra. Vui lòng thử lại sau.',
                'timestamp' => now()->format('H:i')
            ], 500);
        }
    }

    // Method nâng cao kết hợp Firebase và Gemini AI
    public function chatbotAdvanced(Request $request)
    {
        try {
            $request->validate([
                'question' => 'required|string|max:500',
                'session_id' => 'nullable|string'
            ]);

            $question = $request->input('question');
            $sessionId = $request->input('session_id', 'guest_' . uniqid());

            // Khởi tạo Firebase và Gemini services
            $firebaseService = new \App\Services\FirebaseServices();
            $geminiService = new GeminiAIService();
            $firebase = $firebaseService->getDatabase();

            // Lấy lịch sử chat từ Firebase để có context
            $chatHistory = [];
            try {
                $messagesRef = $firebase->getReference('chatbox_conversations/' . $sessionId . '/messages');
                $snapshot = $messagesRef->getValue();
                if ($snapshot) {
                    $chatHistory = array_slice($snapshot, -5); // Lấy 5 tin nhắn gần nhất
                }
            } catch (\Exception $e) {
                Log::warning('Firebase read error: ' . $e->getMessage());
            }

            // Tạo context từ lịch sử chat
            $contextHistory = '';
            if (!empty($chatHistory)) {
                $contextHistory = "\n\nLịch sử hội thoại gần đây:\n";
                foreach ($chatHistory as $msg) {
                    if (isset($msg['sender']) && isset($msg['message'])) {
                        $sender = $msg['sender'] === 'user' ? 'Người dùng' : 'Trợ lý';
                        $contextHistory .= "- {$sender}: {$msg['message']}\n";
                    }
                }
            }

            // Tạo context cho AI
            $context = "Bạn là trợ lý ảo chuyên về bất động sản. Hãy trả lời câu hỏi một cách thân thiện và hữu ích về các vấn đề liên quan đến mua bán, thuê nhà, giá cả, thủ tục pháp lý bất động sản tại Việt Nam.";

            $fullPrompt = $context . $contextHistory . "\n\nCâu hỏi hiện tại: " . $question;

            // Gọi Gemini AI
            $response = $geminiService->askGemini($fullPrompt);

            // Lưu tin nhắn user và bot vào Firebase
            try {
                $timestamp = now()->toISOString();

                // Lưu tin nhắn của user
                $userMessage = [
                    'sender' => 'user',
                    'message' => $question,
                    'timestamp' => $timestamp,
                    'type' => 'text'
                ];

                // Lưu tin nhắn của bot
                $botMessage = [
                    'sender' => 'bot',
                    'message' => $response,
                    'timestamp' => $timestamp,
                    'type' => 'text'
                ];

                // Lưu vào Firebase
                $messagesRef = $firebase->getReference('chatbox_conversations/' . $sessionId . '/messages');
                $messagesRef->push($userMessage);
                $messagesRef->push($botMessage);

                // Cập nhật thông tin session
                $sessionRef = $firebase->getReference('chatbox_conversations/' . $sessionId . '/info');
                $sessionRef->update([
                    'last_activity' => $timestamp,
                    'total_messages' => $firebase->getReference('chatbox_conversations/' . $sessionId . '/messages')->getValue() ? count($firebase->getReference('chatbox_conversations/' . $sessionId . '/messages')->getValue()) : 2
                ]);

            } catch (\Exception $e) {
                Log::warning('Firebase save error: ' . $e->getMessage());
            }

            return response()->json([
                'status' => 'success',
                'answer' => $response,
                'session_id' => $sessionId,
                'timestamp' => now()->format('H:i')
            ]);

        } catch (\Exception $e) {
            Log::error('Advanced Chatbot error: ' . $e->getMessage());

            return response()->json([
                'status' => 'error',
                'answer' => 'Xin lỗi, có lỗi xảy ra. Vui lòng thử lại sau.',
                'timestamp' => now()->format('H:i')
            ], 500);
        }
    }

    // Lấy thống kê chatbox cho public
    public function chatbotStats()
    {
        try {
            // Trả về thống kê đơn giản, không cần authentication
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
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Không thể lấy thông tin'
            ], 500);
        }
    }

    // Lấy các câu hỏi gợi ý
    public function chatbotSuggestions()
    {
        $suggestions = [
            'Tôi muốn mua nhà, cần tư vấn gì?',
            'Giá nhà đất hiện tại như thế nào?',
            'Thủ tục mua bán nhà cần những gì?',
            'Tôi muốn thuê căn hộ chung cư',
            'Làm sao để đánh giá một dự án BĐS?',
            'Có nên mua nhà trả góp không?',
            'Những khu vực nào đang phát triển?',
            'Chi phí làm sổ đỏ là bao nhiêu?'
        ];

        return response()->json([
            'suggestions' => $suggestions,
            'status' => 'success'
        ]);
    }

    // Method kiểm tra FAQ từ file JSON
    private function checkFAQ($question)
    {
        try {
            $faqPath = storage_path('app/faq.json');

            if (!file_exists($faqPath)) {
                Log::warning('FAQ file not found: ' . $faqPath);
                return null;
            }

            $faqContent = file_get_contents($faqPath);
            $faqData = json_decode($faqContent, true);

            if (!$faqData) {
                Log::warning('Invalid FAQ JSON format');
                return null;
            }

            // Chuyển câu hỏi về chữ thường để so sánh
            $questionLower = strtolower(trim($question));

            // Tìm kiếm trong từng category FAQ
            foreach ($faqData as $category => $data) {
                if (isset($data['questions']) && isset($data['answer'])) {
                    foreach ($data['questions'] as $faqQuestion) {
                        $faqQuestionLower = strtolower(trim($faqQuestion));

                        // Kiểm tra có chứa từ khóa không
                        if ($this->containsKeywords($questionLower, $faqQuestionLower)) {
                            return $data['answer'];
                        }
                    }
                }
            }

            return null;

        } catch (\Exception $e) {
            Log::error('FAQ check error: ' . $e->getMessage());
            return null;
        }
    }

    // Method hỗ trợ kiểm tra từ khóa
    private function containsKeywords($userQuestion, $faqQuestion)
    {
        // Nếu câu hỏi user chứa ít nhất 60% từ trong FAQ question
        $userWords = explode(' ', $userQuestion);
        $faqWords = explode(' ', $faqQuestion);

        // Loại bỏ các từ ngắn (dưới 3 ký tự)
        $userWords = array_filter($userWords, function($word) {
            return strlen($word) >= 3;
        });

        $faqWords = array_filter($faqWords, function($word) {
            return strlen($word) >= 3;
        });

        if (empty($userWords) || empty($faqWords)) {
            return false;
        }

        $matchCount = 0;
        foreach ($userWords as $userWord) {
            foreach ($faqWords as $faqWord) {
                // Kiểm tra chứa từ hoặc tương tự
                if (strpos($faqWord, $userWord) !== false || strpos($userWord, $faqWord) !== false) {
                    $matchCount++;
                    break;
                }
            }
        }

        // Nếu ít nhất 40% từ khớp thì coi là match
        $threshold = max(1, floor(count($userWords) * 0.4));
        return $matchCount >= $threshold;
    }
}
