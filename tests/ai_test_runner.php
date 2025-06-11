<?php

/**
 * AI SYSTEM PERFORMANCE TEST RUNNER
 *
 * Chạy test này để kiểm tra hiệu suất hệ thống AI Gemini 1.5 Flash
 *
 * Usage: php artisan test tests/Feature/GeminiAISystemPerformanceTest.php --verbose
 */

require_once __DIR__ . '/../../vendor/autoload.php';

class AIPerformanceTestRunner
{
    public static function run()
    {
        echo "=".str_repeat("=", 80)."\n";
        echo "           HỆ THỐNG AI PERFORMANCE TEST - GEMINI 1.5 FLASH\n";
        echo "=".str_repeat("=", 80)."\n\n";

        echo "🤖 MÔ HÌNH AI ĐANG SỬ DỤNG:\n";
        echo "   • Model: Google Gemini 1.5 Flash\n";
        echo "   • Type: Large Language Model (LLM)\n";
        echo "   • Architecture: Transformer-based Multimodal AI\n";
        echo "   • Approach: Retrieval Augmented Generation (RAG)\n";
        echo "   • Context Window: 1M tokens\n";
        echo "   • API Endpoint: generativelanguage.googleapis.com\n\n";

        echo "🎯 CHỨC NĂNG ĐANG TEST:\n";
        echo "   1. Chatbot hỗ trợ khách hàng bất động sản\n";
        echo "   2. Tích hợp cơ sở dữ liệu thông minh (RAG)\n";
        echo "   3. Phân tích hợp đồng bất động sản\n";
        echo "   4. Xử lý ngôn ngữ tự nhiên tiếng Việt\n";
        echo "   5. Confidence scoring system\n\n";

        echo "⚙️  THÔNG SỐ CONFIGURATION:\n";
        echo "   • Chatbot Temperature: 0.3 (Low creativity, high consistency)\n";
        echo "   • Contract Analysis Temperature: 0.1 (Maximum precision)\n";
        echo "   • Top-P: 0.8 (Nucleus sampling)\n";
        echo "   • Top-K: 40 (Top-K sampling)\n";
        echo "   • Max Output Tokens: 1000 (Chatbot) / 2500 (Contract)\n\n";

        echo "📊 PHƯƠNG PHÁP ĐÁNH GIÁ:\n";
        echo "   • Response Time Analysis\n";
        echo "   • Confidence Score Validation\n";
        echo "   • Database Integration Testing\n";
        echo "   • NLP Accuracy Measurement\n";
        echo "   • Contract Analysis Quality Assessment\n";
        echo "   • Stress Testing & Scalability\n\n";

        echo "🔄 ĐANG CHẠY TESTS...\n\n";

        // Chạy PHPUnit tests
        $output = shell_exec('cd "' . __DIR__ . '/../.." && php artisan test tests/Feature/GeminiAISystemPerformanceTest.php --verbose 2>&1');

        echo $output;

        echo "\n📈 PHÂN TÍCH KẾT QUẢ:\n";
        self::analyzeResults();

        echo "\n✅ TEST HOÀN THÀNH! Kiểm tra file log để xem báo cáo chi tiết.\n";
    }

    private static function analyzeResults()
    {
        $logFiles = glob(__DIR__ . '/../../storage/logs/ai_performance_test_*.json');

        if (empty($logFiles)) {
            echo "   Không tìm thấy file báo cáo.\n";
            return;
        }

        $latestLog = max($logFiles);
        $report = json_decode(file_get_contents($latestLog), true);

        echo "   📄 Báo cáo: " . basename($latestLog) . "\n";

        if (isset($report['Success rate'])) {
            $successRate = floatval(str_replace('%', '', $report['Success rate']));
            echo "   📊 Tỷ lệ thành công: " . $report['Success rate'] . "\n";

            if ($successRate >= 90) {
                echo "   ✅ XUẤT SẮC: Hệ thống hoạt động rất tốt!\n";
            } elseif ($successRate >= 80) {
                echo "   ✅ TỐT: Hệ thống hoạt động ổn định.\n";
            } elseif ($successRate >= 70) {
                echo "   ⚠️  TRUNG BÌNH: Cần cải thiện một số điểm.\n";
            } else {
                echo "   ❌ CẦN KHẮC PHỤC: Hệ thống cần được tối ưu hóa.\n";
            }
        }

        if (isset($report['Tổng thời gian test'])) {
            echo "   ⏱️  Thời gian test: " . $report['Tổng thời gian test'] . "\n";
        }

        echo "\n   🔍 ĐÁNH GIÁ CHI TIẾT:\n";

        // Phân tích từng component
        $components = [
            'Chatbot Performance' => '🤖',
            'Contract Analysis' => '📋',
            'NLP Accuracy' => '🧠',
            'Confidence Scoring' => '📈',
            'Stress Test' => '💪'
        ];

        foreach ($components as $component => $icon) {
            $found = false;
            foreach ($report as $key => $value) {
                if (strpos($key, $component) !== false) {
                    echo "   {$icon} {$component}: ";
                    if (is_array($value) && isset($value['Status'])) {
                        echo $value['Status'] . "\n";
                    } else {
                        echo "TESTED\n";
                    }
                    $found = true;
                    break;
                }
            }
            if (!$found) {
                echo "   {$icon} {$component}: NOT TESTED\n";
            }
        }
    }
}

// Chạy test nếu file này được execute trực tiếp
if (basename(__FILE__) === basename($_SERVER['SCRIPT_NAME'])) {
    AIPerformanceTestRunner::run();
}
