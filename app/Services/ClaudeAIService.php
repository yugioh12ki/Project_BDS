<?php

namespace App\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Facades\Log;

class ClaudeAIService
{
    private $client;
    private $apiKey;
    private $baseUrl = 'https://api.anthropic.com/v1/messages';

    public function __construct()
    {
        $this->client = new Client();
        $this->apiKey = env('CLAUDE_API_KEY');
    }

    /**
     * Gọi Claude API để xử lý câu hỏi
     */
    public function askClaude($userMessage, $faqContext = null, $conversationHistory = [])
    {
        try {
            // Tạo system prompt với FAQ context
            $systemPrompt = $this->buildSystemPrompt($faqContext);

            // Tạo conversation messages
            $messages = $this->buildMessages($userMessage, $conversationHistory);

            $response = $this->client->post($this->baseUrl, [
                'headers' => [
                    'Content-Type' => 'application/json',
                    'x-api-key' => $this->apiKey,
                    'anthropic-version' => '2023-06-01'
                ],
                'json' => [
                    'model' => 'claude-3-5-sonnet-20241022',
                    'max_tokens' => 1000,
                    'temperature' => 0.7,
                    'system' => $systemPrompt,
                    'messages' => $messages
                ]
            ]);

            $data = json_decode($response->getBody()->getContents(), true);

            if (isset($data['content'][0]['text'])) {
                return [
                    'success' => true,
                    'answer' => $data['content'][0]['text'],
                    'confidence' => $this->calculateConfidence($data)
                ];
            }

            return [
                'success' => false,
                'answer' => 'Xin lỗi, tôi không thể xử lý câu hỏi này. Vui lòng liên hệ admin để được hỗ trợ.',
                'confidence' => 0
            ];

        } catch (RequestException $e) {
            Log::error('Claude API Error: ' . $e->getMessage());

            return [
                'success' => false,
                'answer' => 'Hệ thống AI tạm thời gặp sự cố. Vui lòng thử lại sau hoặc liên hệ admin.',
                'confidence' => 0,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Xây dựng system prompt với FAQ context
     */
    private function buildSystemPrompt($faqContext)
    {
        $basePrompt = "Bạn là một chatbot hỗ trợ khách hàng cho công ty bất động sản. Hãy trả lời một cách thân thiện, chuyên nghiệp và hữu ích.

Quy tắc quan trọng:
1. Chỉ trả lời các câu hỏi liên quan đến bất động sản, dịch vụ công ty, hoặc các chủ đề được cung cấp trong FAQ
2. Nếu câu hỏi nằm ngoài phạm vi kiến thức, hãy lịch sự từ chối và đề xuất liên hệ admin
3. Luôn trả lời bằng tiếng Việt
4. Giữ câu trả lời ngắn gọn nhưng đầy đủ thông tin
5. Có thể sử dụng emoji phù hợp để tạo sự thân thiện

Thông tin công ty:
- Công ty: Bất động sản BDS
- Email hỗ trợ: info.real_eslate@gmail.co.uk
- Hotline: 0123456789
- Giờ làm việc: 8h-17h, Thứ 2 đến Thứ 7";

        if ($faqContext) {
            $basePrompt .= "\n\nKiến thức FAQ hiện có:\n";
            foreach ($faqContext as $key => $faq) {
                $basePrompt .= "\nChủ đề: " . str_replace('key_', '', $key) . "\n";
                $basePrompt .= "Câu hỏi thường gặp: " . implode(', ', $faq['questions']) . "\n";
                $basePrompt .= "Trả lời: " . $faq['answer'] . "\n";
            }
        }

        return $basePrompt;
    }

    /**
     * Xây dựng messages cho conversation
     */
    private function buildMessages($userMessage, $conversationHistory = [])
    {
        $messages = [];

        // Thêm conversation history (nếu có)
        foreach ($conversationHistory as $msg) {
            $messages[] = [
                'role' => $msg['role'] ?? 'user',
                'content' => $msg['content']
            ];
        }

        // Thêm câu hỏi hiện tại
        $messages[] = [
            'role' => 'user',
            'content' => $userMessage
        ];

        return $messages;
    }

    /**
     * Tính toán độ tin cậy của câu trả lời
     */
    private function calculateConfidence($apiResponse)
    {
        // Logic đơn giản để tính confidence
        // Có thể cải thiện dựa trên response length, keywords, etc.

        if (!isset($apiResponse['content'][0]['text'])) {
            return 0;
        }

        $text = $apiResponse['content'][0]['text'];
        $length = strlen($text);

        // Confidence cao nếu:
        // - Câu trả lời không quá ngắn (>50 chars)
        // - Không chứa từ "không biết", "không rõ"
        // - Chứa thông tin cụ thể

        $confidence = 0.7; // Base confidence

        if ($length > 50) {
            $confidence += 0.1;
        }

        if ($length > 200) {
            $confidence += 0.1;
        }

        // Giảm confidence nếu có từ không chắc chắn
        $uncertainWords = ['không biết', 'không rõ', 'có thể', 'tôi nghĩ', 'không chắc'];
        foreach ($uncertainWords as $word) {
            if (stripos($text, $word) !== false) {
                $confidence -= 0.2;
                break;
            }
        }

        // Tăng confidence nếu có thông tin liên hệ
        $contactWords = ['email', 'phone', 'hotline', '0123456789', 'info.real_eslate'];
        foreach ($contactWords as $word) {
            if (stripos($text, $word) !== false) {
                $confidence += 0.1;
                break;
            }
        }

        return max(0, min(1, $confidence));
    }

    /**
     * Kiểm tra xem có nên escalate cho admin không
     */
    public function shouldEscalateToAdmin($confidence, $userType = 'guest')
    {
        // Guest users: không escalate, chỉ trả lời AI
        if ($userType === 'guest') {
            return false;
        }

        // Logged-in users: escalate nếu confidence thấp
        if ($userType === 'user' && $confidence < 0.6) {
            return true;
        }

        return false;
    }
}
