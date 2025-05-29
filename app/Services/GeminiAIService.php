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
                $basePrompt .= "\nChủ đề: " . str_replace('key_', '', $key) . "\n";
                $basePrompt .= "Câu hỏi thường gặp: " . implode(', ', $faq['questions']) . "\n";
                $basePrompt .= "Trả lời: " . $faq['answer'] . "\n";
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
}
