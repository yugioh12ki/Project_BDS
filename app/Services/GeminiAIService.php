<?php

namespace App\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Models\Property;
use App\Models\User;
use App\Models\Transaction;
use App\Models\Appointment;
use App\Models\Commission;
use App\Models\feedback;
use App\Models\DanhMucBDS;
use App\Models\DetailProperty;
use App\Models\Image;
use App\Models\Video;

class GeminiAIService
{
    private $client;
    private $apiKey;
    private $baseUrl = 'https://generativelanguage.googleapis.com';

    public function __construct()
    {
        $this->client = new Client();
        $this->apiKey = env('GEMINI_API_KEY');
    }

    /**
     * Gọi Gemini API để xử lý câu hỏi với khả năng tìm kiếm database
     */
    public function askGemini($userMessage, $faqContext = null, $conversationHistory = [])
    {
        try {
            // Phân tích câu hỏi để xác định xem có cần tìm kiếm database không
            $searchResults = $this->analyzeAndSearchDatabase($userMessage);

            // Tạo system prompt với FAQ context và database results
            $systemPrompt = $this->buildSystemPrompt($faqContext, $searchResults);

            // Tạo conversation content
            $content = $this->buildContent($userMessage, $conversationHistory, $systemPrompt, $searchResults);

            $endpoint = "/v1beta/models/gemini-1.5-flash:generateContent?key={$this->apiKey}";

            $response = $this->client->post($this->baseUrl . $endpoint, [
                'headers' => [
                    'Content-Type' => 'application/json',
                ],
                'json' => [
                    'contents' => [
                        [
                            'role' => 'user',
                            'parts' => [
                                ['text' => $content]
                            ]
                        ]
                    ],
                    'systemInstruction' => [
                        'parts' => [
                            ['text' => $systemPrompt]
                        ]
                    ],
                    'generationConfig' => [
                        'maxOutputTokens' => 1000,
                        'temperature' => 0.3,
                        'topP' => 0.8,
                        'topK' => 40
                    ]
                ]
            ]);

            $data = json_decode($response->getBody()->getContents(), true);

            if (isset($data['candidates'][0]['content']['parts'][0]['text'])) {
                return [
                    'success' => true,
                    'answer' => $data['candidates'][0]['content']['parts'][0]['text'],
                    'confidence' => $this->calculateConfidence($data, $searchResults),
                    'database_searched' => $searchResults['found_data'] ?? false,
                    'search_results_count' => [
                        'properties' => count($searchResults['properties'] ?? []),
                        'users' => count($searchResults['users'] ?? []),
                        'transactions' => count($searchResults['transactions'] ?? []),
                        'appointments' => count($searchResults['appointments'] ?? []),
                        'commissions' => count($searchResults['commissions'] ?? []),
                        'feedback' => count($searchResults['feedback'] ?? [])
                    ]
                ];
            } else {
                return [
                    'success' => false,
                    'answer' => 'Xin lỗi, tôi không thể xử lý câu hỏi này. Vui lòng liên hệ admin để được hỗ trợ.',
                    'confidence' => 0
                ];
            }
        } catch (RequestException $e) {
            Log::error('Gemini API Error: ' . $e->getMessage());
            return [
                'success' => false,
                'answer' => 'Hệ thống AI tạm thời gặp sự cố. Vui lòng thử lại sau hoặc liên hệ admin.',
                'confidence' => 0,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Phân tích câu hỏi và tìm kiếm trong database nếu cần
     */
    private function analyzeAndSearchDatabase($userMessage)
    {
        $searchResults = [
            'properties' => [],
            'users' => [],
            'transactions' => [],
            'appointments' => [],
            'commissions' => [],
            'feedback' => [],
            'categories' => [],
            'found_data' => false
        ];

        // Từ khóa để xác định loại tìm kiếm
        $searchKeywords = $this->extractSearchKeywords($userMessage);

        if (!empty($searchKeywords)) {
            // Tìm kiếm bất động sản
            if ($this->isPropertySearch($userMessage)) {
                $searchResults['properties'] = $this->searchProperties($searchKeywords);
                $searchResults['found_data'] = !empty($searchResults['properties']);
            }

            // Tìm kiếm người dùng (agent, owner, customer)
            if ($this->isUserSearch($userMessage)) {
                $searchResults['users'] = $this->searchUsers($searchKeywords);
                $searchResults['found_data'] = $searchResults['found_data'] || !empty($searchResults['users']);
            }

            // Tìm kiếm giao dịch
            if ($this->isTransactionSearch($userMessage)) {
                $searchResults['transactions'] = $this->searchTransactions($searchKeywords);
                $searchResults['found_data'] = $searchResults['found_data'] || !empty($searchResults['transactions']);
            }

            // Tìm kiếm cuộc hẹn
            if ($this->isAppointmentSearch($userMessage)) {
                $searchResults['appointments'] = $this->searchAppointments($searchKeywords);
                $searchResults['found_data'] = $searchResults['found_data'] || !empty($searchResults['appointments']);
            }

            // Tìm kiếm hoa hồng
            if ($this->isCommissionSearch($userMessage)) {
                $searchResults['commissions'] = $this->searchCommissions($searchKeywords);
                $searchResults['found_data'] = $searchResults['found_data'] || !empty($searchResults['commissions']);
            }

            // Tìm kiếm feedback
            if ($this->isFeedbackSearch($userMessage)) {
                $searchResults['feedback'] = $this->searchFeedback($searchKeywords);
                $searchResults['found_data'] = $searchResults['found_data'] || !empty($searchResults['feedback']);
            }

            // Thêm thông tin danh mục
            if ($this->isCategorySearch($userMessage)) {
                $searchResults['categories'] = $this->getCategories();
                $searchResults['found_data'] = $searchResults['found_data'] || !empty($searchResults['categories']);
            }
        }

        return $searchResults;
    }

    /**
     * Trích xuất từ khóa tìm kiếm từ câu hỏi
     */
    private function extractSearchKeywords($userMessage)
    {
        $keywords = [];

        // Chuyển về chữ thường để dễ tìm kiếm
        $message = strtolower($userMessage);

        // Trích xuất địa điểm
        $locations = ['hà nội', 'hồ chí minh', 'đà nẵng', 'cần thơ', 'nha trang', 'vũng tàu', 'hải phòng', 'quận', 'huyện', 'phường', 'xã'];
        foreach ($locations as $location) {
            if (strpos($message, $location) !== false) {
                $keywords['location'] = $location;
                break;
            }
        }

        // Trích xuất loại bất động sản
        $propertyTypes = ['nhà', 'căn hộ', 'chung cư', 'biệt thự', 'đất', 'kho', 'văn phòng', 'mặt bằng'];
        foreach ($propertyTypes as $type) {
            if (strpos($message, $type) !== false) {
                $keywords['property_type'] = $type;
                break;
            }
        }

        // Trích xuất mức giá
        preg_match('/(\d+(?:\.\d+)?)\s*(triệu|tỷ|nghìn)/i', $userMessage, $priceMatches);
        if (!empty($priceMatches)) {
            $keywords['price'] = $priceMatches[0];
        }

        // Trích xuất số ID
        preg_match('/\b([A-Z]{2,4}\d{4,}|\d{6,})\b/i', $userMessage, $idMatches);
        if (!empty($idMatches)) {
            $keywords['id'] = $idMatches[1];
        }

        return $keywords;
    }

    /**
     * Kiểm tra xem có phải tìm kiếm bất động sản không
     */
    private function isPropertySearch($message)
    {
        $propertyKeywords = ['bất động sản', 'nhà', 'căn hộ', 'chung cư', 'biệt thự', 'đất', 'property', 'tìm', 'tìm kiếm', 'có', 'ở đâu', 'giá', 'cho thuê', 'cho bán'];

        foreach ($propertyKeywords as $keyword) {
            if (stripos($message, $keyword) !== false) {
                return true;
            }
        }

        return false;
    }

    /**
     * Kiểm tra xem có phải tìm kiếm người dùng không
     */
    private function isUserSearch($message)
    {
        $userKeywords = ['agent', 'môi giới', 'chủ sở hữu', 'khách hàng', 'user', 'người dùng', 'staff', 'nhân viên'];

        foreach ($userKeywords as $keyword) {
            if (stripos($message, $keyword) !== false) {
                return true;
            }
        }

        return false;
    }

    /**
     * Kiểm tra xem có phải tìm kiếm giao dịch không
     */
    private function isTransactionSearch($message)
    {
        $transactionKeywords = ['giao dịch', 'transaction', 'hợp đồng', 'mua bán', 'thuê', 'thanh toán'];

        foreach ($transactionKeywords as $keyword) {
            if (stripos($message, $keyword) !== false) {
                return true;
            }
        }

        return false;
    }

    /**
     * Kiểm tra xem có phải tìm kiếm cuộc hẹn không
     */
    private function isAppointmentSearch($message)
    {
        $appointmentKeywords = ['cuộc hẹn', 'appointment', 'lịch hẹn', 'hẹn xem', 'xem nhà'];

        foreach ($appointmentKeywords as $keyword) {
            if (stripos($message, $keyword) !== false) {
                return true;
            }
        }

        return false;
    }

    /**
     * Kiểm tra xem có phải tìm kiếm hoa hồng không
     */
    private function isCommissionSearch($message)
    {
        $commissionKeywords = ['hoa hồng', 'commission', 'tiền hoa hồng', 'phần trăm'];

        foreach ($commissionKeywords as $keyword) {
            if (stripos($message, $keyword) !== false) {
                return true;
            }
        }

        return false;
    }

    /**
     * Kiểm tra xem có phải tìm kiếm feedback không
     */
    private function isFeedbackSearch($message)
    {
        $feedbackKeywords = ['feedback', 'đánh giá', 'phản hồi', 'review', 'nhận xét'];

        foreach ($feedbackKeywords as $keyword) {
            if (stripos($message, $keyword) !== false) {
                return true;
            }
        }

        return false;
    }

    /**
     * Kiểm tra xem có phải tìm kiếm danh mục không
     */
    private function isCategorySearch($message)
    {
        $categoryKeywords = ['danh mục', 'loại', 'category', 'phân loại'];

        foreach ($categoryKeywords as $keyword) {
            if (stripos($message, $keyword) !== false) {
                return true;
            }
        }

        return false;
    }

    /**
     * Tìm kiếm bất động sản
     */
    private function searchProperties($keywords)
    {
        try {
            $query = Property::with(['danhMuc', 'chusohuu', 'moigioi', 'chiTiet', 'images', 'videos']);

            // Tìm theo ID nếu có
            if (isset($keywords['id'])) {
                $query->where('PropertyID', 'LIKE', '%' . $keywords['id'] . '%');
            }

            // Tìm theo địa điểm
            if (isset($keywords['location'])) {
                $query->where(function($q) use ($keywords) {
                    $q->where('Province', 'LIKE', '%' . $keywords['location'] . '%')
                      ->orWhere('District', 'LIKE', '%' . $keywords['location'] . '%')
                      ->orWhere('Ward', 'LIKE', '%' . $keywords['location'] . '%')
                      ->orWhere('Address', 'LIKE', '%' . $keywords['location'] . '%');
                });
            }

            // Tìm theo loại bất động sản
            if (isset($keywords['property_type'])) {
                $query->where('Title', 'LIKE', '%' . $keywords['property_type'] . '%');
            }

            // Tìm theo giá (nếu có thể parse được)
            if (isset($keywords['price'])) {
                // Logic để parse giá và tìm kiếm (đơn giản hóa)
                $query->whereNotNull('Price');
            }

            return $query->limit(10)->get()->toArray();

        } catch (\Exception $e) {
            Log::error('Error searching properties: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Tìm kiếm người dùng
     */
    private function searchUsers($keywords)
    {
        try {
            $query = User::with(['profile_agent', 'profile_customer', 'profile_owner']);

            // Tìm theo ID nếu có
            if (isset($keywords['id'])) {
                $query->where('UserID', 'LIKE', '%' . $keywords['id'] . '%');
            }

            // Tìm theo địa điểm
            if (isset($keywords['location'])) {
                $query->where(function($q) use ($keywords) {
                    $q->where('Province', 'LIKE', '%' . $keywords['location'] . '%')
                      ->orWhere('District', 'LIKE', '%' . $keywords['location'] . '%')
                      ->orWhere('Address', 'LIKE', '%' . $keywords['location'] . '%');
                });
            }

            return $query->limit(10)->get()->toArray();

        } catch (\Exception $e) {
            Log::error('Error searching users: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Tìm kiếm giao dịch
     */
    private function searchTransactions($keywords)
    {
        try {
            $query = Transaction::with(['trans_owner', 'trans_agent', 'trans_cus', 'trans_property']);

            // Tìm theo ID nếu có
            if (isset($keywords['id'])) {
                $query->where('TransactionID', 'LIKE', '%' . $keywords['id'] . '%');
            }

            return $query->limit(10)->get()->toArray();

        } catch (\Exception $e) {
            Log::error('Error searching transactions: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Tìm kiếm cuộc hẹn
     */
    private function searchAppointments($keywords)
    {
        try {
            $query = Appointment::with(['user_owner', 'user_agent', 'user_customer', 'property']);

            // Tìm theo ID nếu có
            if (isset($keywords['id'])) {
                $query->where('AppointmentID', 'LIKE', '%' . $keywords['id'] . '%');
            }

            return $query->limit(10)->get()->toArray();

        } catch (\Exception $e) {
            Log::error('Error searching appointments: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Tìm kiếm hoa hồng
     */
    private function searchCommissions($keywords)
    {
        try {
            $query = Commission::with(['comm_agent', 'comm_trans']);

            // Tìm theo ID nếu có
            if (isset($keywords['id'])) {
                $query->where('CommissionID', 'LIKE', '%' . $keywords['id'] . '%');
            }

            return $query->limit(10)->get()->toArray();

        } catch (\Exception $e) {
            Log::error('Error searching commissions: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Tìm kiếm feedback
     */
    private function searchFeedback($keywords)
    {
        try {
            $query = feedback::with(['user_Cus', 'user_Agent']);

            // Tìm theo ID nếu có
            if (isset($keywords['id'])) {
                $query->where('FeedbackID', 'LIKE', '%' . $keywords['id'] . '%');
            }

            return $query->limit(10)->get()->toArray();

        } catch (\Exception $e) {
            Log::error('Error searching feedback: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Lấy danh mục bất động sản
     */
    private function getCategories()
    {
        try {
            return DanhMucBDS::all()->toArray();
        } catch (\Exception $e) {
            Log::error('Error getting categories: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Xây dựng system prompt với FAQ context và database results
     */
    private function buildSystemPrompt($faqContext, $searchResults = null)
    {
        $basePrompt = "Bạn là một chatbot hỗ trợ khách hàng cho công ty bất động sản BDS. Bạn có khả năng truy cập vào cơ sở dữ liệu thực tế của công ty để cung cấp thông tin chính xác về bất động sản, người dùng, giao dịch và các dịch vụ.

Quy tắc quan trọng:
1. Ưu tiên sử dụng thông tin từ cơ sở dữ liệu thực tế khi có
2. Trả lời các câu hỏi liên quan đến bất động sản, dịch vụ công ty một cách chính xác
3. Nếu không tìm thấy dữ liệu phù hợp, hãy thông báo và đề xuất liên hệ admin
4. Luôn trả lời bằng tiếng Việt, thân thiện và chuyên nghiệp
5. Cung cấp thông tin chi tiết nhưng dễ hiểu
6. Có thể sử dụng emoji phù hợp

Thông tin công ty:
- Công ty: Bất động sản BDS
- Email hỗ trợ: info.real_eslate@gmail.co.uk
- Hotline: 0123456789
- Giờ làm việc: 8h-17h, Thứ 2 đến Thứ 7";

        // Thêm thông tin từ database nếu có
        if ($searchResults && $searchResults['found_data']) {
            $basePrompt .= "\n\n=== THÔNG TIN TỪ CƠ SỞ DỮ LIỆU ===\n";

            if (!empty($searchResults['properties'])) {
                $basePrompt .= "\nBẤT ĐỘNG SẢN LIÊN QUAN:\n";
                foreach ($searchResults['properties'] as $property) {
                    $basePrompt .= sprintf(
                        "- Mã BĐS: %s, Tiêu đề: %s, Giá: %s, Địa chỉ: %s %s %s %s, Trạng thái: %s\n",
                        $property['PropertyID'] ?? 'N/A',
                        $property['Title'] ?? 'N/A',
                        isset($property['Price']) ? number_format($property['Price']) . ' VND' : 'N/A',
                        $property['Address'] ?? '',
                        $property['Ward'] ?? '',
                        $property['District'] ?? '',
                        $property['Province'] ?? '',
                        $property['Status'] ?? 'N/A'
                    );
                }
            }

            if (!empty($searchResults['users'])) {
                $basePrompt .= "\nNGƯỜI DÙNG LIÊN QUAN:\n";
                foreach ($searchResults['users'] as $user) {
                    $basePrompt .= sprintf(
                        "- ID: %s, Tên: %s, Vai trò: %s, Email: %s, SĐT: %s\n",
                        $user['UserID'] ?? 'N/A',
                        $user['Name'] ?? 'N/A',
                        $user['Role'] ?? 'N/A',
                        $user['Email'] ?? 'N/A',
                        $user['Phone'] ?? 'N/A'
                    );
                }
            }

            if (!empty($searchResults['transactions'])) {
                $basePrompt .= "\nGIAO DỊCH LIÊN QUAN:\n";
                foreach ($searchResults['transactions'] as $transaction) {
                    $basePrompt .= sprintf(
                        "- Mã GD: %s, Loại: %s, Giá trị: %s, Trạng thái: %s, Ngày: %s\n",
                        $transaction['TransactionID'] ?? 'N/A',
                        $transaction['TypeTrans'] ?? 'N/A',
                        isset($transaction['TotalPrice']) ? number_format($transaction['TotalPrice']) . ' VND' : 'N/A',
                        $transaction['TranStatus'] ?? 'N/A',
                        $transaction['TransactionDate'] ?? 'N/A'
                    );
                }
            }

            if (!empty($searchResults['appointments'])) {
                $basePrompt .= "\nCUỘC HẸN LIÊN QUAN:\n";
                foreach ($searchResults['appointments'] as $appointment) {
                    $basePrompt .= sprintf(
                        "- Mã cuộc hẹn: %s, Ngày: %s, Giờ: %s, Địa điểm: %s, Trạng thái: %s\n",
                        $appointment['AppointmentID'] ?? 'N/A',
                        $appointment['DateAppoint'] ?? 'N/A',
                        $appointment['TimeAppoint'] ?? 'N/A',
                        $appointment['LocationAppoint'] ?? 'N/A',
                        $appointment['StatusAppoint'] ?? 'N/A'
                    );
                }
            }

            if (!empty($searchResults['commissions'])) {
                $basePrompt .= "\nHOA HỒNG LIÊN QUAN:\n";
                foreach ($searchResults['commissions'] as $commission) {
                    $basePrompt .= sprintf(
                        "- Mã HH: %s, Phần trăm: %s%%, Số tiền: %s, Trạng thái: %s\n",
                        $commission['CommissionID'] ?? 'N/A',
                        $commission['Percentage'] ?? 'N/A',
                        isset($commission['CommissionAmount']) ? number_format($commission['CommissionAmount']) . ' VND' : 'N/A',
                        $commission['StatusCommission'] ?? 'N/A'
                    );
                }
            }

            if (!empty($searchResults['feedback'])) {
                $basePrompt .= "\nPHẢN HỒI LIÊN QUAN:\n";
                foreach ($searchResults['feedback'] as $fb) {
                    $basePrompt .= sprintf(
                        "- Mã FB: %s, Đánh giá: %s sao, Nội dung: %s\n",
                        $fb['FeedbackID'] ?? 'N/A',
                        $fb['Rating'] ?? 'N/A',
                        $fb['Comment'] ?? 'N/A'
                    );
                }
            }

            if (!empty($searchResults['categories'])) {
                $basePrompt .= "\nDANH MỤC BẤT ĐỘNG SẢN:\n";
                foreach ($searchResults['categories'] as $category) {
                    $basePrompt .= sprintf(
                        "- Mã: %s, Tên: %s\n",
                        $category['Protype_ID'] ?? 'N/A',
                        $category['ten_pro'] ?? 'N/A'
                    );
                }
            }
        }

        // Thêm FAQ context nếu có
        if ($faqContext) {
            $basePrompt .= "\n\n=== KIẾN THỨC FAQ ===\n";
            foreach ($faqContext as $key => $faq) {
                if (is_array($faq) && isset($faq['questions']) && isset($faq['answer'])) {
                    $basePrompt .= "\nChủ đề: " . str_replace('key_', '', $key) . "\n";
                    $basePrompt .= "Câu hỏi thường gặp: " . implode(', ', $faq['questions']) . "\n";
                    $basePrompt .= "Trả lời: " . $faq['answer'] . "\n";
                }
            }
        }

        return $basePrompt;
    }

    /**
     * Xây dựng content cho conversation với database context
     */
    private function buildContent($userMessage, $conversationHistory = [], $systemPrompt = '', $searchResults = null)
    {
        $content = "";

        // Thêm thông tin về việc tìm kiếm database nếu có
        if ($searchResults && $searchResults['found_data']) {
            $content .= "📊 Tôi đã tìm kiếm trong cơ sở dữ liệu và tìm thấy thông tin liên quan.\n\n";
        }

        // Thêm conversation history (nếu có)
        if (!empty($conversationHistory)) {
            $content .= "Lịch sử cuộc trò chuyện:\n";
            foreach ($conversationHistory as $msg) {
                $role = $msg['role'] === 'assistant' ? 'Bot' : 'User';
                $content .= $role . ": " . $msg['content'] . "\n";
            }
            $content .= "\n";
        }

        // Thêm câu hỏi hiện tại
        $content .= "Câu hỏi hiện tại: " . $userMessage;

        return $content;
    }

    /**
     * Tính toán độ tin cậy của câu trả lời với database context
     */
    private function calculateConfidence($apiResponse, $searchResults = null)
    {
        if (!isset($apiResponse['candidates'][0]['content']['parts'][0]['text'])) {
            return 0;
        }

        $text = $apiResponse['candidates'][0]['content']['parts'][0]['text'];
        $length = strlen($text);

        // Base confidence
        $confidence = 0.7;

        // Tăng confidence nếu có dữ liệu từ database
        if ($searchResults && $searchResults['found_data']) {
            $confidence += 0.2;
        }

        // Confidence cao nếu câu trả lời không quá ngắn
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

        // Tăng confidence nếu có thông tin cụ thể từ database
        $dataWords = ['mã', 'id', 'bđs', 'bất động sản', 'giao dịch', 'hoa hồng', 'cuộc hẹn'];
        foreach ($dataWords as $word) {
            if (stripos($text, $word) !== false) {
                $confidence += 0.05;
            }
        }

        return max(0, min(1, $confidence));
    }

    /**
     * Lấy thống kê tổng quan từ database
     */
    public function getDatabaseStats()
    {
        try {
            return [
                'total_properties' => Property::count(),
                'active_properties' => Property::where('Status', 'active')->count(),
                'pending_properties' => Property::where('Status', 'pending')->count(),
                'total_users' => User::count(),
                'total_agents' => User::where('Role', 'Agent')->count(),
                'total_owners' => User::where('Role', 'Owner')->count(),
                'total_customers' => User::where('Role', 'Customer')->count(),
                'total_transactions' => Transaction::count(),
                'pending_transactions' => Transaction::where('TranStatus', 'Pending')->count(),
                'completed_transactions' => Transaction::where('TranStatus', 'Paid')->count(),
                'total_appointments' => Appointment::count(),
                'total_commissions' => Commission::count(),
                'pending_commissions' => Commission::where('StatusCommission', 'Pending')->count(),
                'total_feedback' => feedback::count()
            ];
        } catch (\Exception $e) {
            Log::error('Error getting database stats: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Tìm kiếm thông minh toàn bộ hệ thống
     */
    public function smartSearch($query, $limit = 20)
    {
        $results = [
            'properties' => [],
            'users' => [],
            'transactions' => [],
            'appointments' => [],
            'commissions' => [],
            'feedback' => []
        ];

        try {
            // Tìm kiếm bất động sản
            $properties = Property::with(['danhMuc', 'chusohuu', 'moigioi', 'chiTiet'])
                ->where(function($q) use ($query) {
                    $q->where('Title', 'LIKE', "%{$query}%")
                      ->orWhere('PropertyID', 'LIKE', "%{$query}%")
                      ->orWhere('Address', 'LIKE', "%{$query}%")
                      ->orWhere('District', 'LIKE', "%{$query}%")
                      ->orWhere('Province', 'LIKE', "%{$query}%")
                      ->orWhere('Description', 'LIKE', "%{$query}%");
                })
                ->limit($limit/6)
                ->get();
            $results['properties'] = $properties->toArray();

            // Tìm kiếm người dùng
            $users = User::where(function($q) use ($query) {
                    $q->where('Name', 'LIKE', "%{$query}%")
                      ->orWhere('UserID', 'LIKE', "%{$query}%")
                      ->orWhere('Email', 'LIKE', "%{$query}%")
                      ->orWhere('Phone', 'LIKE', "%{$query}%");
                })
                ->limit($limit/6)
                ->get();
            $results['users'] = $users->toArray();

            // Tìm kiếm giao dịch
            $transactions = Transaction::where(function($q) use ($query) {
                    $q->where('TransactionID', 'LIKE', "%{$query}%")
                      ->orWhere('PropertyID', 'LIKE', "%{$query}%");
                })
                ->limit($limit/6)
                ->get();
            $results['transactions'] = $transactions->toArray();

            // Tìm kiếm cuộc hẹn
            $appointments = Appointment::where(function($q) use ($query) {
                    $q->where('AppointmentID', 'LIKE', "%{$query}%")
                      ->orWhere('PropertyID', 'LIKE', "%{$query}%")
                      ->orWhere('LocationAppoint', 'LIKE', "%{$query}%");
                })
                ->limit($limit/6)
                ->get();
            $results['appointments'] = $appointments->toArray();

            // Tìm kiếm hoa hồng
            $commissions = Commission::where('CommissionID', 'LIKE', "%{$query}%")
                ->limit($limit/6)
                ->get();
            $results['commissions'] = $commissions->toArray();

            // Tìm kiếm feedback
            $feedbacks = feedback::where(function($q) use ($query) {
                    $q->where('FeedbackID', 'LIKE', "%{$query}%")
                      ->orWhere('Comment', 'LIKE', "%{$query}%");
                })
                ->limit($limit/6)
                ->get();
            $results['feedback'] = $feedbacks->toArray();

        } catch (\Exception $e) {
            Log::error('Error in smart search: ' . $e->getMessage());
        }

        return $results;
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

    /**
     * Phân tích hợp đồng bằng AI
     */
    public function analyzeContract($contractContent, $contractSource = 'template')
    {
        try {
            // Bước 1: Kiểm tra xem có phải hợp đồng bất động sản không
            $validationResult = $this->validateRealEstateContract($contractContent);

            if (!$validationResult['is_valid']) {
                return [
                    'success' => false,
                    'error' => $validationResult['reason'],
                    'suggestion' => $validationResult['suggestion']
                ];
            }

            // Bước 2: Phân tích hợp đồng nếu hợp lệ
            $analysisPrompt = $this->buildContractAnalysisPrompt($contractContent, $contractSource);

            $endpoint = "/v1beta/models/gemini-1.5-flash:generateContent?key={$this->apiKey}";

            $response = $this->client->post($this->baseUrl . $endpoint, [
                'headers' => [
                    'Content-Type' => 'application/json',
                ],
                'json' => [
                    'contents' => [
                        [
                            'role' => 'user',
                            'parts' => [
                                ['text' => $analysisPrompt]
                            ]
                        ]
                    ],
                    'generationConfig' => [
                        'maxOutputTokens' => 2500,
                        'temperature' => 0.1,
                        'topP' => 0.8,
                        'topK' => 40
                    ]
                ]
            ]);

            $data = json_decode($response->getBody()->getContents(), true);

            if (isset($data['candidates'][0]['content']['parts'][0]['text'])) {
                $analysisText = $data['candidates'][0]['content']['parts'][0]['text'];
                $result = $this->parseContractAnalysis($analysisText);

                // Thêm thông tin validation
                if ($result['success']) {
                    $result['validation'] = $validationResult;
                    $result['contract_type'] = $validationResult['contract_type'];
                }

                return $result;
            } else {
                return [
                    'success' => false,
                    'error' => 'Không thể nhận được phản hồi từ AI'
                ];
            }
        } catch (RequestException $e) {
            Log::error('Contract Analysis Error: ' . $e->getMessage());
            // Throw exception to trigger fallback in controller
            throw new \Exception('Lỗi kết nối AI: ' . $e->getMessage());
        }
    }

    /**
     * Kiểm tra tính hợp lệ của hợp đồng bất động sản
     */
    private function validateRealEstateContract($contractContent)
    {
        // Chuẩn hóa text tiếng Việt
        $content = $this->normalizeVietnameseText($contractContent);

        // Từ khóa bắt buộc cho hợp đồng bất động sản
        $requiredKeywords = [
            'real_estate' => ['bất động sản', 'real estate', 'property', 'bds'],
            'contract_type' => ['hợp đồng', 'contract', 'thỏa thuận', 'agreement'],
            'transaction' => ['mua bán', 'cho thuê', 'sell', 'buy', 'rent', 'lease', 'sale']
        ];

        // Từ khóa cụ thể cho bất động sản
        $propertyKeywords = [
            'căn hộ', 'apartment', 'chung cư', 'nhà phố', 'house', 'villa', 'biệt thự',
            'đất', 'land', 'lot', 'plot', 'khu đất', 'thửa đất', 'tòa nhà', 'building',
            'văn phòng', 'office', 'shophouse', 'mặt bằng', 'premises'
        ];

        // Từ khóa pháp lý bất động sản
        $legalKeywords = [
            'sổ đỏ', 'sổ hồng', 'giấy chứng nhận', 'certificate', 'quyền sở hữu', 'ownership',
            'quyền sử dụng đất', 'land use rights', 'pháp lý', 'legal', 'công chứng', 'notarization'
        ];

        // Kiểm tra từ khóa bắt buộc
        $hasRealEstate = false;
        $hasContract = false;
        $hasTransaction = false;

        foreach ($requiredKeywords['real_estate'] as $keyword) {
            if (strpos($content, $keyword) !== false) {
                $hasRealEstate = true;
                break;
            }
        }

        foreach ($requiredKeywords['contract_type'] as $keyword) {
            if (strpos($content, $keyword) !== false) {
                $hasContract = true;
                break;
            }
        }

        foreach ($requiredKeywords['transaction'] as $keyword) {
            if (strpos($content, $keyword) !== false) {
                $hasTransaction = true;
                break;
            }
        }

        // Kiểm tra từ khóa bất động sản cụ thể
        $hasPropertyType = false;
        foreach ($propertyKeywords as $keyword) {
            if (strpos($content, $keyword) !== false) {
                $hasPropertyType = true;
                break;
            }
        }

        // Kiểm tra từ khóa pháp lý
        $hasLegalTerms = false;
        foreach ($legalKeywords as $keyword) {
            if (strpos($content, $keyword) !== false) {
                $hasLegalTerms = true;
                break;
            }
        }

        // Xác định loại hợp đồng
        $contractType = 'unknown';
        if (strpos($content, 'mua bán') !== false || strpos($content, 'sale') !== false) {
            $contractType = 'sale';
        } elseif (strpos($content, 'cho thuê') !== false || strpos($content, 'rent') !== false || strpos($content, 'lease') !== false) {
            $contractType = 'rental';
        } elseif (strpos($content, 'môi giới') !== false || strpos($content, 'commission') !== false) {
            $contractType = 'brokerage';
        }

        // Logic xác định tính hợp lệ
        if (!$hasContract) {
            return [
                'is_valid' => false,
                'reason' => 'Tài liệu này không phải là hợp đồng',
                'suggestion' => 'Vui lòng tải lên file hợp đồng có định dạng chuẩn'
            ];
        }

        if (!$hasRealEstate && !$hasPropertyType) {
            return [
                'is_valid' => false,
                'reason' => 'Đây không phải là hợp đồng bất động sản',
                'suggestion' => 'Hệ thống chỉ hỗ trợ phân tích hợp đồng liên quan đến bất động sản (mua bán, cho thuê, môi giới)'
            ];
        }

        if (!$hasTransaction && $contractType === 'unknown') {
            return [
                'is_valid' => false,
                'reason' => 'Không xác định được loại giao dịch bất động sản',
                'suggestion' => 'Hợp đồng cần rõ ràng về loại giao dịch (mua bán, cho thuê, v.v.)'
            ];
        }

        // Tính điểm độ tin cậy
        $confidenceScore = 0;
        if ($hasRealEstate) $confidenceScore += 20;
        if ($hasContract) $confidenceScore += 20;
        if ($hasTransaction) $confidenceScore += 20;
        if ($hasPropertyType) $confidenceScore += 25;
        if ($hasLegalTerms) $confidenceScore += 15;

        if ($confidenceScore < 60) {
            return [
                'is_valid' => false,
                'reason' => 'Hợp đồng thiếu thông tin quan trọng về bất động sản',
                'suggestion' => 'Hợp đồng cần có đầy đủ thông tin về: loại bất động sản, điều khoản giao dịch, và các điều khoản pháp lý',
                'confidence_score' => $confidenceScore
            ];
        }

        return [
            'is_valid' => true,
            'contract_type' => $contractType,
            'confidence_score' => $confidenceScore,
            'detected_elements' => [
                'has_real_estate_terms' => $hasRealEstate,
                'has_contract_structure' => $hasContract,
                'has_transaction_terms' => $hasTransaction,
                'has_property_details' => $hasPropertyType,
                'has_legal_terms' => $hasLegalTerms
            ]
        ];
    }

    /**
     * Tạo prompt đặc biệt cho phân tích hợp đồng
     */
    private function buildContractAnalysisPrompt($contractContent, $contractSource)
    {
        $prompt = "Bạn là chuyên gia pháp lý về bất động sản tại Việt Nam với 15 năm kinh nghiệm. Nhiệm vụ của bạn là phân tích HỢP ĐỒNG BẤT ĐỘNG SẢN một cách chuyên sâu và khách quan.\n\n";

        $prompt .= "HỢP ĐỒNG CẦN PHÂN TÍCH:\n";
        $prompt .= "=".str_repeat("=", 50)."\n";
        $prompt .= "Nguồn: " . ($contractSource === 'template' ? 'Mẫu có sẵn trong hệ thống' : 'File được tải lên bởi người dùng') . "\n";
        $prompt .= "Nội dung hợp đồng:\n" . $contractContent . "\n";
        $prompt .= "=".str_repeat("=", 50)."\n\n";

        $prompt .= "YÊU CẦU PHÂN TÍCH:\n";
        $prompt .= "1. Đánh giá từ góc độ KHÁCH HÀNG (người mua/thuê)\n";
        $prompt .= "2. Phân tích dựa trên luật pháp Việt Nam hiện hành\n";
        $prompt .= "3. Tập trung vào tính công bằng và bảo vệ quyền lợi\n";
        $prompt .= "4. Đưa ra khuyến nghị cụ thể và thực tế\n\n";

        $prompt .= "Trả về kết quả theo định dạng JSON chính xác sau:\n\n";
        $prompt .= '```json
{
    "contract_analysis": {
        "contract_type": "sale|rental|brokerage|other",
        "property_type": "apartment|house|land|office|other",
        "analysis_summary": "Tóm tắt ngắn gọn về hợp đồng (2-3 câu)"
    },
    "benefits_analysis": {
        "for_buyer_renter": [
            "Lợi ích cụ thể 1 cho người mua/thuê",
            "Lợi ích cụ thể 2 cho người mua/thuê",
            "Lợi ích cụ thể 3 cho người mua/thuê"
        ],
        "for_seller_owner": [
            "Lợi ích cụ thể 1 cho người bán/cho thuê",
            "Lợi ích cụ thể 2 cho người bán/cho thuê"
        ]
    },
    "risk_assessment": {
        "high_risks": [
            "Rủi ro nghiêm trọng 1 đối với khách hàng",
            "Rủi ro nghiêm trọng 2 đối với khách hàng"
        ],
        "medium_risks": [
            "Rủi ro trung bình 1",
            "Rủi ro trung bình 2"
        ],
        "legal_compliance_issues": [
            "Vấn đề pháp lý 1 (nếu có)",
            "Vấn đề pháp lý 2 (nếu có)"
        ]
    },
    "detailed_ratings": {
        "customer_protection": 7,
        "legal_compliance": 8,
        "terms_fairness": 6,
        "contract_clarity": 9,
        "overall_score": 7.5
    },
    "key_terms_evaluation": {
        "payment_terms": {
            "rating": 8,
            "analysis": "Phân tích chi tiết điều khoản thanh toán"
        },
        "delivery_terms": {
            "rating": 7,
            "analysis": "Phân tích điều khoản bàn giao"
        },
        "liability_terms": {
            "rating": 6,
            "analysis": "Phân tích điều khoản trách nhiệm"
        },
        "termination_terms": {
            "rating": 7,
            "analysis": "Phân tích điều khoản chấm dứt"
        }
    },
    "recommendations": {
        "immediate_actions": [
            "Hành động cần thực hiện ngay 1",
            "Hành động cần thực hiện ngay 2"
        ],
        "contract_improvements": [
            "Cải thiện hợp đồng 1",
            "Cải thiện hợp đồng 2"
        ],
        "legal_consultation": [
            "Vấn đề cần tư vấn pháp lý 1",
            "Vấn đề cần tư vấn pháp lý 2"
        ]
    },
    "critical_points": [
        "Điểm quan trọng 1 khách hàng cần lưu ý",
        "Điểm quan trọng 2 khách hàng cần lưu ý",
        "Điểm quan trọng 3 khách hàng cần lưu ý"
    ]
}
```';

        $prompt .= "\n\nHƯỚNG DẪN CHI TIẾT:\n";
        $prompt .= "• Điểm đánh giá từ 1-10 (1=rất kém, 10=rất tốt)\n";
        $prompt .= "• Phân tích dựa trên Luật Nhà ở 2014, Luật Đất đai 2013, Luật Kinh doanh bất động sản 2014\n";
        $prompt .= "• Ưu tiên bảo vệ quyền lợi của khách hàng (người yếu thế hơn)\n";
        $prompt .= "• Chỉ ra các điều khoản có thể bất lợi hoặc không công bằng\n";
        $prompt .= "• Đưa ra khuyến nghị thiết thực và có thể thực hiện được\n";
        $prompt .= "• contract_type: xác định chính xác loại hợp đồng\n";
        $prompt .= "• property_type: xác định loại bất động sản\n\n";

        $prompt .= "LƯU Ý QUAN TRỌNG:\n";
        $prompt .= "- Chỉ trả về JSON hợp lệ, KHÔNG có markdown wrapper\n";
        $prompt .= "- Đảm bảo tất cả các trường đều có giá trị\n";
        $prompt .= "- Nếu không có thông tin, ghi 'Không rõ' thay vì bỏ trống\n";
        $prompt .= "- Tất cả ratings phải là số từ 1-10\n";
        $prompt .= "- Phân tích phải dựa trên nội dung thực tế của hợp đồng\n";

        return $prompt;
    }

    /**
     * Parse kết quả phân tích hợp đồng từ AI
     */
    private function parseContractAnalysis($analysisText)
    {
        try {
            // Loại bỏ markdown wrapper nếu có
            $cleanText = $analysisText;
            if (strpos($analysisText, '```json') !== false) {
                $start = strpos($analysisText, '```json') + 7;
                $end = strrpos($analysisText, '```');
                if ($end > $start) {
                    $cleanText = substr($analysisText, $start, $end - $start);
                }
            }

            // Tìm JSON trong text response
            $jsonStart = strpos($cleanText, '{');
            $jsonEnd = strrpos($cleanText, '}');

            if ($jsonStart !== false && $jsonEnd !== false) {
                $jsonText = substr($cleanText, $jsonStart, $jsonEnd - $jsonStart + 1);
                $analysis = json_decode($jsonText, true);

                if (json_last_error() === JSON_ERROR_NONE && $analysis) {
                    // Chuyển đổi format mới sang format cũ để tương thích với frontend
                    $compatibleAnalysis = $this->convertToCompatibleFormat($analysis);

                    return [
                        'success' => true,
                        'analysis' => $compatibleAnalysis,
                        'detailed_analysis' => $analysis, // Giữ lại format đầy đủ
                        'raw_response' => $analysisText
                    ];
                }
            }

            // Fallback: Thử parse toàn bộ text
            $analysis = json_decode($cleanText, true);
            if (json_last_error() === JSON_ERROR_NONE && $analysis) {
                $compatibleAnalysis = $this->convertToCompatibleFormat($analysis);
                return [
                    'success' => true,
                    'analysis' => $compatibleAnalysis,
                    'detailed_analysis' => $analysis,
                    'raw_response' => $analysisText
                ];
            }

            // Nếu không parse được JSON, fallback về manual parsing
            return $this->manualParseAnalysis($analysisText);

        } catch (\Exception $e) {
            Log::error('Contract analysis parsing error: ' . $e->getMessage());
            return [
                'success' => false,
                'error' => 'Lỗi phân tích kết quả: ' . $e->getMessage(),
                'raw_response' => $analysisText
            ];
        }
    }

    /**
     * Chuyển đổi format mới sang format tương thích với frontend hiện tại
     */
    private function convertToCompatibleFormat($newAnalysis)
    {
        $compatible = [
            'overall_rating' => 7.0,
            'benefits' => [],
            'risks' => [],
            'recommendations' => []
        ];

        // Tính overall rating từ detailed ratings
        if (isset($newAnalysis['detailed_ratings'])) {
            $ratings = $newAnalysis['detailed_ratings'];
            $compatible['overall_rating'] = $ratings['overall_score'] ??
                (($ratings['customer_protection'] + $ratings['legal_compliance'] +
                  $ratings['terms_fairness'] + $ratings['contract_clarity']) / 4);
        }

        // Chuyển đổi benefits
        if (isset($newAnalysis['benefits_analysis']['for_buyer_renter'])) {
            $compatible['benefits'] = array_merge(
                $compatible['benefits'],
                $newAnalysis['benefits_analysis']['for_buyer_renter']
            );
        }
        if (isset($newAnalysis['benefits_analysis']['for_seller_owner'])) {
            $compatible['benefits'] = array_merge(
                $compatible['benefits'],
                $newAnalysis['benefits_analysis']['for_seller_owner']
            );
        }

        // Chuyển đổi risks
        if (isset($newAnalysis['risk_assessment'])) {
            $risks = $newAnalysis['risk_assessment'];
            if (isset($risks['high_risks'])) {
                $compatible['risks'] = array_merge($compatible['risks'], $risks['high_risks']);
            }
            if (isset($risks['medium_risks'])) {
                $compatible['risks'] = array_merge($compatible['risks'], $risks['medium_risks']);
            }
            if (isset($risks['legal_compliance_issues'])) {
                $compatible['risks'] = array_merge($compatible['risks'], $risks['legal_compliance_issues']);
            }
        }

        // Chuyển đổi recommendations
        if (isset($newAnalysis['recommendations'])) {
            $recs = $newAnalysis['recommendations'];
            if (isset($recs['immediate_actions'])) {
                $compatible['recommendations'] = array_merge($compatible['recommendations'], $recs['immediate_actions']);
            }
            if (isset($recs['contract_improvements'])) {
                $compatible['recommendations'] = array_merge($compatible['recommendations'], $recs['contract_improvements']);
            }
            if (isset($recs['legal_consultation'])) {
                $compatible['recommendations'] = array_merge($compatible['recommendations'], $recs['legal_consultation']);
            }
        }

        // Thêm critical points vào recommendations nếu có
        if (isset($newAnalysis['critical_points'])) {
            $compatible['recommendations'] = array_merge($compatible['recommendations'], $newAnalysis['critical_points']);
        }

        // Đảm bảo có ít nhất một số item trong mỗi category
        if (empty($compatible['benefits'])) {
            $compatible['benefits'] = ['Cần phân tích thêm từ chuyên gia'];
        }
        if (empty($compatible['risks'])) {
            $compatible['risks'] = ['Cần đánh giá rủi ro thêm'];
        }
        if (empty($compatible['recommendations'])) {
            $compatible['recommendations'] = ['Nên tham khảo ý kiến chuyên gia pháp lý'];
        }

        return $compatible;
    }

    /**
     * Manual parsing nếu JSON parsing thất bại
     */
    private function manualParseAnalysis($analysisText)
    {
        // Tạo cấu trúc mặc định
        $analysis = [
            'overall_rating' => 7.0,
            'benefits' => ['Cần phân tích thêm từ chuyên gia'],
            'risks' => ['Cần đánh giá rủi ro thêm'],
            'recommendations' => ['Nên tham khảo ý kiến chuyên gia pháp lý']
        ];

        // Thử extract một số thông tin cơ bản từ text
        $lines = explode("\n", $analysisText);
        $currentSection = '';

        foreach ($lines as $line) {
            $line = trim($line);
            if (empty($line)) continue;

            // Tìm các section
            if (strpos($line, 'benefit') !== false || strpos($line, 'lợi ích') !== false) {
                $currentSection = 'benefits';
            } elseif (strpos($line, 'risk') !== false || strpos($line, 'rủi ro') !== false) {
                $currentSection = 'risks';
            } elseif (strpos($line, 'recommend') !== false || strpos($line, 'khuyến nghị') !== false) {
                $currentSection = 'recommendations';
            } elseif (strpos($line, 'rating') !== false || strpos($line, 'điểm') !== false) {
                // Tìm số trong dòng
                preg_match('/(\d+(?:\.\d+)?)/', $line, $matches);
                if (!empty($matches[1])) {
                    $analysis['overall_rating'] = floatval($matches[1]);
                }
            }

            // Thêm content vào section tương ứng
            if ($currentSection && strpos($line, '-') === 0) {
                $content = trim(substr($line, 1));
                if (!empty($content)) {
                    $analysis[$currentSection][] = $content;
                }
            }
        }

        return [
            'success' => true,
            'analysis' => $analysis,
            'note' => 'Phân tích tự động - cần xem xét thêm',
            'raw_response' => $analysisText
        ];
    }

    /**
     * Chuẩn hóa text tiếng Việt để xử lý chính xác
     */
    private function normalizeVietnameseText($text)
    {
        // Chuyển đổi về lowercase và xử lý diacritics Việt Nam
        $text = mb_strtolower($text, 'UTF-8');

        // Mapping các ký tự đặc biệt tiếng Việt
        $vietnamese_chars = [
            'À', 'Á', 'Ạ', 'Ả', 'Ã', 'Â', 'Ầ', 'Ấ', 'Ậ', 'Ẩ', 'Ẫ', 'Ă', 'Ằ', 'Ắ', 'Ặ', 'Ẳ', 'Ẵ',
            'È', 'É', 'Ẹ', 'Ẻ', 'Ẽ', 'Ê', 'Ề', 'Ế', 'Ệ', 'Ể', 'Ễ',
            'Ì', 'Í', 'Ị', 'Ỉ', 'Ĩ',
            'Ò', 'Ó', 'Ọ', 'Ỏ', 'Õ', 'Ô', 'Ồ', 'Ố', 'Ộ', 'Ổ', 'Ỗ', 'Ơ', 'Ờ', 'Ớ', 'Ợ', 'Ở', 'Ỡ',
            'Ù', 'Ú', 'Ụ', 'Ủ', 'Ũ', 'Ư', 'Ừ', 'Ứ', 'Ự', 'Ử', 'Ữ',
            'Ỳ', 'Ý', 'Ỵ', 'Ỷ', 'Ỹ',
            'Đ'
        ];

        $latin_chars = [
            'à', 'á', 'ạ', 'ả', 'ã', 'â', 'ầ', 'ấ', 'ậ', 'ẩ', 'ẫ', 'ă', 'ằ', 'ắ', 'ặ', 'ẳ', 'ẵ',
            'è', 'é', 'ẹ', 'ẻ', 'ẽ', 'ê', 'ề', 'ế', 'ệ', 'ể', 'ễ',
            'ì', 'í', 'ị', 'ỉ', 'ĩ',
            'ò', 'ó', 'ọ', 'ỏ', 'õ', 'ô', 'ồ', 'ố', 'ộ', 'ổ', 'ỗ', 'ơ', 'ờ', 'ớ', 'ợ', 'ở', 'ỡ',
            'ù', 'ú', 'ụ', 'ủ', 'ũ', 'ư', 'ừ', 'ứ', 'ự', 'ử', 'ữ',
            'ỳ', 'ý', 'ỵ', 'ỷ', 'ỹ',
            'đ'
        ];

        $text = str_replace($vietnamese_chars, $latin_chars, $text);

        return $text;
    }
}
