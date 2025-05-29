<?php

require_once __DIR__ . '/vendor/autoload.php';

// Load Laravel app
$app = require_once __DIR__ . '/bootstrap/app.php';

// Boot the application
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Services\GeminiAIService;

echo "=== GEMINI AI với DATABASE INTEGRATION TEST ===\n\n";

$geminiService = new GeminiAIService();

// Test cases để kiểm tra khả năng tìm kiếm database
$testCases = [
    [
        'question' => 'Tìm bất động sản ở Hà Nội',
        'description' => 'Tìm kiếm BĐS theo địa điểm'
    ],
    [
        'question' => 'Có nhà nào cho thuê không?',
        'description' => 'Tìm kiếm BĐS cho thuê'
    ],
    [
        'question' => 'Thống kê tổng quan hệ thống',
        'description' => 'Lấy thống kê database'
    ],






































































































echo "✅ HOÀN THÀNH TEST DATABASE INTEGRATION!\n";}    }        echo "  ❌ Lỗi: " . $e->getMessage() . "\n\n";    } catch (Exception $e) {        echo "  Tổng: {$totalResults} kết quả\n\n";        }            }                $totalResults += $count;                echo "  - {$type}: {$count} kết quả\n";            if ($count > 0) {            $count = count($items);        foreach ($results as $type => $items) {        $totalResults = 0;        $results = $geminiService->smartSearch($query, 10);    try {    echo "Tìm kiếm: '{$query}'\n";foreach ($searchQueries as $query) {$searchQueries = ['BDS', 'Hà Nội', 'Agent', 'Transaction'];echo str_repeat('-', 30) . "\n";echo "🔍 SMART SEARCH TEST:\n";// Test smart searchecho "\n" . str_repeat('=', 70) . "\n";}    echo "❌ Lỗi lấy thống kê: " . $e->getMessage() . "\n";} catch (Exception $e) {    }        echo sprintf("%-25s: %s\n", str_replace('_', ' ', ucfirst($key)), number_format($value));    foreach ($stats as $key => $value) {    $stats = $geminiService->getDatabaseStats();try {echo str_repeat('-', 30) . "\n";echo "📊 THỐNG KÊ DATABASE:\n";// Test thống kê database}    echo "\n" . str_repeat('=', 70) . "\n\n";        }        }            echo "🐛 Chi tiết lỗi: " . $response['error'] . "\n";        if (isset($response['error'])) {        echo "📝 Lỗi: " . $response['answer'] . "\n";        echo "❌ Thất bại!\n";    } else {        }            echo "🔍 Đã tìm kiếm database: KHÔNG\n";        } else {            }                }                    echo "   - " . ucfirst($type) . ": " . $count . " kết quả\n";                if ($count > 0) {            foreach ($response['search_results_count'] as $type => $count) {            echo "📊 Kết quả tìm kiếm:\n";            echo "🔍 Đã tìm kiếm database: CÓ\n";        if (isset($response['database_searched']) && $response['database_searched']) {                echo "⏱️ Thời gian xử lý: " . $processingTime . "ms\n";        echo "🎯 Độ tin cậy: " . round($response['confidence'] * 100, 1) . "%\n";        echo "📝 Trả lời: " . $response['answer'] . "\n";        echo "✅ Thành công!\n";    if ($response['success']) {        $processingTime = round(($endTime - $startTime) * 1000, 2);        $endTime = microtime(true);    $response = $geminiService->askGemini($testCase['question']);    $startTime = microtime(true);        echo str_repeat('-', 50) . "\n";
    echo "Câu hỏi: " . $testCase['question'] . "\n";    echo "TEST " . ($index + 1) . ": " . $testCase['description'] . "\n";foreach ($testCases as $index => $testCase) {];    ]        'description' => 'Tìm kiếm phản hồi'        'question' => 'Feedback từ khách hàng',    [    ],        'description' => 'Tìm kiếm hoa hồng'        'question' => 'Hoa hồng chưa thanh toán',    [    ],        'description' => 'Lấy danh mục BĐS'        'question' => 'Danh sách loại bất động sản',    [    ],        'description' => 'Tìm kiếm giao dịch'        'question' => 'Có giao dịch nào đang pending không?',    [    ],        'description' => 'Tìm kiếm người dùng'        'question' => 'Tìm môi giới tên Nam',    [
