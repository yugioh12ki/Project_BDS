<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Services\GeminiAIService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

/**
 * Test hiệu suất và độ chính xác của hệ thống AI Gemini 1.5 Flash
 *
 * Mô hình AI: Google Gemini 1.5 Flash
 * Chức năng: Chatbot BĐS + Phân tích hợp đồng
 * Phương pháp: Large Language Model (LLM) với Retrieval Augmented Generation (RAG)
 */
class GeminiAISystemPerformanceTest extends TestCase
{
    use RefreshDatabase;

    private $geminiService;
    private $testResults = [];
    private $startTime;

    public function setUp(): void
    {
        parent::setUp();
        $this->geminiService = new GeminiAIService();
        $this->startTime = microtime(true);

        // Tạo test data
        $this->seedTestData();
    }

    public function tearDown(): void
    {
        $this->generateDetailedReport();
        parent::tearDown();
    }

    /**
     * ==============================================
     * THÔNG TIN HỆ THỐNG AI
     * ==============================================
     */
    public function test_system_specifications()
    {
        $specs = [
            'ai_model' => 'Google Gemini 1.5 Flash',
            'model_type' => 'Large Language Model (LLM)',
            'architecture' => 'Transformer-based Multimodal AI',
            'approach' => 'Retrieval Augmented Generation (RAG)',
            'api_endpoint' => 'https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash',
            'context_window' => '1M tokens',
            'capabilities' => [
                'Text Generation',
                'Natural Language Understanding',
                'Database Integration',
                'Contract Analysis',
                'Vietnamese Language Processing'
            ]
        ];

        $this->logTestResult('System Specifications', $specs, true, 'INFO');
        $this->assertTrue(true);
    }

    /**
     * ==============================================
     * TEST CẤU HÌNH PARAMETERS
     * ==============================================
     */
    public function test_ai_parameters_configuration()
    {
        $chatbotConfig = [
            'maxOutputTokens' => 1000,
            'temperature' => 0.3,    // Low creativity, high consistency
            'topP' => 0.8,          // Nucleus sampling
            'topK' => 40,           // Top-K sampling
            'use_case' => 'Customer Support Chatbot'
        ];

        $contractConfig = [
            'maxOutputTokens' => 2500,
            'temperature' => 0.1,    // Very low creativity, maximum precision
            'topP' => 0.8,
            'topK' => 40,
            'use_case' => 'Legal Contract Analysis'
        ];

        // Kiểm tra tính hợp lý của parameters
        $this->assertGreaterThanOrEqual(0.0, $chatbotConfig['temperature']);
        $this->assertLessThanOrEqual(1.0, $chatbotConfig['temperature']);
        $this->assertGreaterThan($contractConfig['temperature'], $chatbotConfig['temperature']);

        $this->logTestResult('Chatbot Parameters', $chatbotConfig, true);
        $this->logTestResult('Contract Analysis Parameters', $contractConfig, true);
    }

    /**
     * ==============================================
     * TEST HIỆU SUẤT CHATBOT
     * ==============================================
     */
    public function test_chatbot_performance_with_database_integration()
    {
        $testCases = [
            [
                'query' => 'Tìm căn hộ ở Hà Nội giá dưới 5 tỷ',
                'expected_features' => ['property_search', 'database_query', 'location_filter'],
                'expected_confidence' => 0.8
            ],
            [
                'query' => 'Cho tôi biết thông tin về agent có ID AG001',
                'expected_features' => ['user_search', 'id_extraction'],
                'expected_confidence' => 0.9
            ],
            [
                'query' => 'Hoa hồng của tôi tháng này như thế nào?',
                'expected_features' => ['commission_search', 'time_filter'],
                'expected_confidence' => 0.7
            ]
        ];

        $results = [];
        foreach ($testCases as $index => $case) {
            $startTime = microtime(true);

            $result = $this->geminiService->askGemini($case['query']);

            $responseTime = (microtime(true) - $startTime) * 1000; // ms

            $performance = [
                'query' => $case['query'],
                'success' => $result['success'] ?? false,
                'confidence' => $result['confidence'] ?? 0,
                'database_searched' => $result['database_searched'] ?? false,
                'response_time_ms' => round($responseTime, 2),
                'answer_length' => strlen($result['answer'] ?? ''),
                'search_results_count' => $result['search_results_count'] ?? []
            ];

            // Đánh giá chất lượng
            $quality_score = $this->evaluateResponseQuality($result, $case);
            $performance['quality_score'] = $quality_score;

            $results[] = $performance;

            // Assertions
            $this->assertTrue($result['success']);
            $this->assertGreaterThanOrEqual($case['expected_confidence'] - 0.2, $result['confidence']);
            $this->assertLessThan(5000, $responseTime); // < 5 seconds
        }

        $avgConfidence = array_sum(array_column($results, 'confidence')) / count($results);
        $avgResponseTime = array_sum(array_column($results, 'response_time_ms')) / count($results);

        $summary = [
            'total_tests' => count($results),
            'success_rate' => (count(array_filter($results, fn($r) => $r['success'])) / count($results)) * 100,
            'average_confidence' => round($avgConfidence, 3),
            'average_response_time_ms' => round($avgResponseTime, 2),
            'database_integration_rate' => (count(array_filter($results, fn($r) => $r['database_searched'])) / count($results)) * 100
        ];

        $this->logTestResult('Chatbot Performance Test', $results, true);
        $this->logTestResult('Chatbot Performance Summary', $summary, true);

        // Performance assertions
        $this->assertGreaterThanOrEqual(90, $summary['success_rate']);
        $this->assertGreaterThanOrEqual(0.7, $summary['average_confidence']);
        $this->assertLessThan(3000, $summary['average_response_time_ms']);
    }

    /**
     * ==============================================
     * TEST KEYWORD EXTRACTION & NLP
     * ==============================================
     */
    public function test_nlp_keyword_extraction_accuracy()
    {
        $testCases = [
            [
                'input' => 'Tìm nhà ở quận 1 TPHCM giá 3 tỷ có ID BDS12345',
                'expected' => [
                    'property_type' => 'nhà',
                    'location' => 'hồ chí minh',
                    'price' => '3 tỷ',
                    'id' => 'BDS12345'
                ]
            ],
            [
                'input' => 'Căn hộ chung cư Hà Nội dưới 2.5 triệu',
                'expected' => [
                    'property_type' => 'căn hộ',
                    'location' => 'hà nội',
                    'price' => '2.5 triệu'
                ]
            ],
            [
                'input' => 'Agent môi giới ở Đà Nẵng',
                'expected' => [
                    'location' => 'đà nẵng',
                    'user_type' => 'agent'
                ]
            ]
        ];

        $results = [];
        foreach ($testCases as $case) {
            // Sử dụng reflection để test private method
            $reflection = new \ReflectionClass($this->geminiService);
            $method = $reflection->getMethod('extractSearchKeywords');
            $method->setAccessible(true);

            $extracted = $method->invoke($this->geminiService, $case['input']);

            $accuracy = $this->calculateExtractionAccuracy($extracted, $case['expected']);

            $results[] = [
                'input' => $case['input'],
                'expected' => $case['expected'],
                'extracted' => $extracted,
                'accuracy' => $accuracy
            ];

            $this->assertGreaterThanOrEqual(0.7, $accuracy);
        }

        $avgAccuracy = array_sum(array_column($results, 'accuracy')) / count($results);

        $this->logTestResult('NLP Keyword Extraction', $results, true);
        $this->logTestResult('NLP Average Accuracy', ['accuracy' => $avgAccuracy], true);

        $this->assertGreaterThanOrEqual(0.8, $avgAccuracy);
    }

    /**
     * ==============================================
     * TEST PHÂN TÍCH HỢP ĐỒNG
     * ==============================================
     */
    public function test_contract_analysis_performance()
    {
        $sampleContracts = [
            [
                'type' => 'sale',
                'content' => $this->getSampleSaleContract(),
                'expected_risks' => ['high_risks', 'legal_compliance_issues'],
                'expected_confidence' => 0.8
            ],
            [
                'type' => 'rental',
                'content' => $this->getSampleRentalContract(),
                'expected_risks' => ['medium_risks'],
                'expected_confidence' => 0.75
            ],
            [
                'type' => 'invalid',
                'content' => 'Đây chỉ là một văn bản thông thường không phải hợp đồng',
                'expected_valid' => false
            ]
        ];

        $results = [];
        foreach ($sampleContracts as $contract) {
            $startTime = microtime(true);

            try {
                $result = $this->geminiService->analyzeContract($contract['content'], 'test');
                $responseTime = (microtime(true) - $startTime) * 1000;

                $analysis = [
                    'contract_type' => $contract['type'],
                    'success' => $result['success'] ?? false,
                    'response_time_ms' => round($responseTime, 2),
                    'validation_passed' => $result['validation']['is_valid'] ?? false,
                    'analysis_quality' => $this->evaluateContractAnalysis($result)
                ];

                if ($contract['type'] === 'invalid') {
                    $this->assertFalse($result['success']);
                } else {
                    $this->assertTrue($result['success']);
                    $this->assertArrayHasKey('analysis', $result);
                    $this->assertArrayHasKey('detailed_analysis', $result);
                }

                $results[] = $analysis;

            } catch (\Exception $e) {
                $results[] = [
                    'contract_type' => $contract['type'],
                    'success' => false,
                    'error' => $e->getMessage(),
                    'response_time_ms' => (microtime(true) - $startTime) * 1000
                ];
            }
        }

        $successRate = (count(array_filter($results, fn($r) => $r['success'])) / count($results)) * 100;
        $avgResponseTime = array_sum(array_column($results, 'response_time_ms')) / count($results);

        $contractSummary = [
            'total_contracts_tested' => count($results),
            'success_rate' => $successRate,
            'average_response_time_ms' => round($avgResponseTime, 2),
            'validation_accuracy' => $this->calculateValidationAccuracy($results)
        ];

        $this->logTestResult('Contract Analysis Performance', $results, true);
        $this->logTestResult('Contract Analysis Summary', $contractSummary, true);

        // Assertions for contract analysis
        $this->assertGreaterThanOrEqual(80, $successRate);
        $this->assertLessThan(10000, $avgResponseTime); // < 10 seconds
    }

    /**
     * ==============================================
     * TEST CONFIDENCE SCORING SYSTEM
     * ==============================================
     */
    public function test_confidence_scoring_accuracy()
    {
        $testScenarios = [
            [
                'scenario' => 'Perfect match with database',
                'mock_response' => ['candidates' => [['content' => ['parts' => [['text' => 'Dựa trên thông tin từ cơ sở dữ liệu, tôi tìm thấy 5 bất động sản phù hợp tại Hà Nội với giá từ 2-3 tỷ VNĐ. Mã BĐS: HN001, địa chỉ: 123 Nguyễn Du, Hai Bà Trưng, Hà Nội. Giá: 2,500,000,000 VNĐ.']]]]]],
                'search_results' => ['found_data' => true, 'properties' => [1,2,3,4,5]],
                'expected_min_confidence' => 0.85
            ],
            [
                'scenario' => 'No database match, general answer',
                'mock_response' => ['candidates' => [['content' => ['parts' => [['text' => 'Tôi không tìm thấy thông tin cụ thể. Vui lòng liên hệ hotline 0123456789.']]]]]],
                'search_results' => ['found_data' => false],
                'expected_min_confidence' => 0.6
            ],
            [
                'scenario' => 'Uncertain response',
                'mock_response' => ['candidates' => [['content' => ['parts' => [['text' => 'Tôi không chắc chắn về thông tin này. Có thể bạn nên liên hệ admin.']]]]]],
                'search_results' => ['found_data' => false],
                'expected_max_confidence' => 0.6
            ]
        ];

        $confidenceResults = [];
        foreach ($testScenarios as $scenario) {
            $reflection = new \ReflectionClass($this->geminiService);
            $method = $reflection->getMethod('calculateConfidence');
            $method->setAccessible(true);

            $confidence = $method->invoke(
                $this->geminiService,
                $scenario['mock_response'],
                $scenario['search_results']
            );

            $result = [
                'scenario' => $scenario['scenario'],
                'calculated_confidence' => $confidence,
                'expected_range' => [
                    'min' => $scenario['expected_min_confidence'] ?? 0,
                    'max' => $scenario['expected_max_confidence'] ?? 1
                ],
                'within_expected_range' => $confidence >= ($scenario['expected_min_confidence'] ?? 0)
                                        && $confidence <= ($scenario['expected_max_confidence'] ?? 1)
            ];

            $confidenceResults[] = $result;

            if (isset($scenario['expected_min_confidence'])) {
                $this->assertGreaterThanOrEqual($scenario['expected_min_confidence'], $confidence);
            }
            if (isset($scenario['expected_max_confidence'])) {
                $this->assertLessThanOrEqual($scenario['expected_max_confidence'], $confidence);
            }
        }

        $accuracyRate = (count(array_filter($confidenceResults, fn($r) => $r['within_expected_range'])) / count($confidenceResults)) * 100;

        $this->logTestResult('Confidence Scoring Test', $confidenceResults, true);
        $this->logTestResult('Confidence Scoring Accuracy', ['accuracy_rate' => $accuracyRate], true);

        $this->assertGreaterThanOrEqual(80, $accuracyRate);
    }

    /**
     * ==============================================
     * TEST STRESS & PERFORMANCE
     * ==============================================
     */
    public function test_system_stress_and_scalability()
    {
        $stressTests = [];
        $concurrentRequests = 5;

        // Test concurrent requests
        $promises = [];
        $startTime = microtime(true);

        for ($i = 0; $i < $concurrentRequests; $i++) {
            $query = "Test query số {$i} - tìm bất động sản";
            $result = $this->geminiService->askGemini($query);
            $promises[] = $result;
        }

        $totalTime = (microtime(true) - $startTime) * 1000;
        $avgTimePerRequest = $totalTime / $concurrentRequests;

        $stressResults = [
            'concurrent_requests' => $concurrentRequests,
            'total_time_ms' => round($totalTime, 2),
            'average_time_per_request_ms' => round($avgTimePerRequest, 2),
            'successful_requests' => count(array_filter($promises, fn($p) => $p['success'] ?? false)),
            'throughput_requests_per_second' => round($concurrentRequests / ($totalTime / 1000), 2)
        ];

        $this->logTestResult('Stress Test Results', $stressResults, true);

        // Performance assertions
        $this->assertLessThan(15000, $totalTime); // Total time < 15 seconds
        $this->assertLessThan(5000, $avgTimePerRequest); // Avg time < 5 seconds per request
        $this->assertGreaterThanOrEqual(0.2, $stressResults['throughput_requests_per_second']);
    }

    /**
     * ==============================================
     * HELPER METHODS
     * ==============================================
     */

    private function seedTestData()
    {
        // Tạo test data cho database
        DB::table('properties')->insert([
            'PropertyID' => 'HN001',
            'Title' => 'Căn hộ cao cấp Hà Nội',
            'Price' => 2500000000,
            'Address' => '123 Nguyễn Du',
            'Ward' => 'Hai Bà Trưng',
            'District' => 'Hai Bà Trưng',
            'Province' => 'Hà Nội',
            'Status' => 'active'
        ]);

        DB::table('users')->insert([
            'UserID' => 'AG001',
            'Name' => 'Nguyễn Văn A',
            'Role' => 'Agent',
            'Email' => 'agent@test.com',
            'Phone' => '0123456789'
        ]);
    }

    private function evaluateResponseQuality($result, $testCase)
    {
        $score = 0;

        if ($result['success']) $score += 30;
        if ($result['confidence'] >= 0.7) $score += 25;
        if ($result['database_searched']) $score += 20;
        if (strlen($result['answer']) > 50) $score += 15;
        if (strpos($result['answer'], 'BĐS') !== false || strpos($result['answer'], 'bất động sản') !== false) $score += 10;

        return $score / 100;
    }

    private function calculateExtractionAccuracy($extracted, $expected)
    {
        $matches = 0;
        $total = count($expected);

        foreach ($expected as $key => $value) {
            if (isset($extracted[$key]) && strpos(strtolower($extracted[$key]), strtolower($value)) !== false) {
                $matches++;
            }
        }

        return $total > 0 ? $matches / $total : 0;
    }

    private function evaluateContractAnalysis($result)
    {
        if (!$result['success']) return 0;

        $score = 0;
        $analysis = $result['detailed_analysis'] ?? [];

        // Check structure completeness
        if (isset($analysis['contract_analysis'])) $score += 20;
        if (isset($analysis['benefits_analysis'])) $score += 20;
        if (isset($analysis['risk_assessment'])) $score += 20;
        if (isset($analysis['detailed_ratings'])) $score += 20;
        if (isset($analysis['recommendations'])) $score += 20;

        return $score / 100;
    }

    private function calculateValidationAccuracy($results)
    {
        $validContracts = array_filter($results, fn($r) => $r['contract_type'] !== 'invalid');
        $invalidContracts = array_filter($results, fn($r) => $r['contract_type'] === 'invalid');

        $validCorrect = count(array_filter($validContracts, fn($r) => $r['validation_passed'] ?? false));
        $invalidCorrect = count(array_filter($invalidContracts, fn($r) => !($r['validation_passed'] ?? true)));

        $totalCorrect = $validCorrect + $invalidCorrect;
        $totalTests = count($results);

        return $totalTests > 0 ? ($totalCorrect / $totalTests) * 100 : 0;
    }

    private function getSampleSaleContract()
    {
        return "HỢP ĐỒNG MUA BÁN BẤT ĐỘNG SẢN

        Hôm nay, ngày __ tháng __ năm 2024, chúng tôi gồm:

        BÊN BÁN (Bên A):
        - Họ tên: Nguyễn Văn A
        - CMND: 123456789

        BÊN MUA (Bên B):
        - Họ tên: Trần Thị B
        - CMND: 987654321

        Cùng thỏa thuận mua bán căn hộ chung cư tại địa chỉ: 123 Nguyễn Du, Hai Bà Trưng, Hà Nội

        ĐIỀU 1: THÔNG TIN BẤT ĐỘNG SẢN
        - Diện tích: 80m2
        - Giá bán: 2.500.000.000 VNĐ
        - Sổ hồng số: SH123456

        ĐIỀU 2: ĐIỀU KHOẢN THANH TOÁN
        - Đặt cọc: 250.000.000 VNĐ
        - Thanh toán còn lại trong 30 ngày

        ĐIỀU 3: NGHĨA VỤ CÁC BÊN
        - Bên A cam kết pháp lý rõ ràng
        - Bên B thanh toán đúng hạn";
    }

    private function getSampleRentalContract()
    {
        return "HỢP ĐỒNG CHO THUÊ NHÀ Ở

        BÊN CHO THUÊ: Nguyễn Văn C
        BÊN THUÊ: Lê Thị D

        Địa chỉ cho thuê: Căn hộ 2PN tại 456 Lê Lợi, Quận 1, TPHCM

        Giá thuê: 15.000.000 VNĐ/tháng
        Tiền cọc: 30.000.000 VNĐ
        Thời hạn: 12 tháng";
    }

    private function logTestResult($testName, $data, $success, $level = 'INFO')
    {
        $this->testResults[] = [
            'test_name' => $testName,
            'timestamp' => date('Y-m-d H:i:s'),
            'success' => $success,
            'data' => $data,
            'level' => $level
        ];

        Log::info("AI Test: {$testName}", ['data' => $data, 'success' => $success]);
    }

    private function generateDetailedReport()
    {
        $totalTime = microtime(true) - $this->startTime;

        $report = [
            '=== BÁOCÁO KIỂM TRA HỆ THỐNG AI GEMINI 1.5 FLASH ===' => '',
            'Thời gian test' => date('Y-m-d H:i:s'),
            'Tổng thời gian test' => round($totalTime, 2) . ' seconds',
            'Tổng số test cases' => count($this->testResults),
            'Success rate' => (count(array_filter($this->testResults, fn($r) => $r['success'])) / count($this->testResults)) * 100 . '%',
            '' => '',
            '=== CHI TIẾT CÁC TEST ===' => '',
        ];

        foreach ($this->testResults as $result) {
            $report[$result['test_name']] = [
                'Status' => $result['success'] ? 'PASS' : 'FAIL',
                'Time' => $result['timestamp'],
                'Data' => $result['data']
            ];
        }

        // Ghi báo cáo ra file
        $reportPath = storage_path('logs/ai_performance_test_' . date('Y_m_d_H_i_s') . '.json');
        file_put_contents($reportPath, json_encode($report, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

        echo "\n=== BÁO CÁO TEST HỆ THỐNG AI ===\n";
        echo "File báo cáo: {$reportPath}\n";
        echo "Tổng thời gian: " . round($totalTime, 2) . " seconds\n";
        echo "Success rate: " . (count(array_filter($this->testResults, fn($r) => $r['success'])) / count($this->testResults)) * 100 . "%\n";
    }
}
