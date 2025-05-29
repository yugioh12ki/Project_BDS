<?php

// Test file for Claude AI integration
// Run this from the command line: php test_claude_integration.php

require_once __DIR__ . '/vendor/autoload.php';

use App\Services\ClaudeAIService;
use Dotenv\Dotenv;

// Load environment variables
$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

// Test Claude AI Service
echo "Testing Claude AI Integration...\n";
echo "================================\n\n";

try {
    $claudeService = new ClaudeAIService();

    // Test simple question
    echo "Test 1: Simple FAQ question\n";
    echo "Question: 'Khi nào công ty mở cửa?'\n";

    $faqContext = [
        "key_open_time" => [
            "questions" => [
                "Khi nào mở cửa",
                "Giờ mở cửa là mấy giờ",
                "Khi nào đóng cửa",
                "Thời gian làm việc"
            ],
            "answer" => "Công ty mở cửa từ 8h đến 17h, thứ 2 đến thứ 6."
        ]
    ];

    $result = $claudeService->askClaude(
        "Khi nào công ty mở cửa?",
        $faqContext,
        []
    );

    if ($result['success']) {
        echo "✅ Success!\n";
        echo "Answer: " . $result['answer'] . "\n";
        echo "Confidence: " . $result['confidence'] . "\n\n";
    } else {
        echo "❌ Failed!\n";
        echo "Error: " . ($result['error'] ?? 'Unknown error') . "\n\n";
    }

    // Test complex question
    echo "Test 2: Complex question\n";
    echo "Question: 'Tôi muốn tìm hiểu về quy trình đầu tư bất động sản'\n";

    $result2 = $claudeService->askClaude(
        "Tôi muốn tìm hiểu về quy trình đầu tư bất động sản",
        $faqContext,
        []
    );

    if ($result2['success']) {
        echo "✅ Success!\n";
        echo "Answer: " . $result2['answer'] . "\n";
        echo "Confidence: " . $result2['confidence'] . "\n\n";
    } else {
        echo "❌ Failed!\n";
        echo "Error: " . ($result2['error'] ?? 'Unknown error') . "\n\n";
    }

} catch (Exception $e) {
    echo "❌ Exception occurred: " . $e->getMessage() . "\n";
    echo "Make sure you have set CLAUDE_API_KEY in your .env file\n";
}

echo "Test completed!\n";
echo "\nNote: If tests fail with API errors, please:\n";
echo "1. Add your actual Claude API key to .env file\n";
echo "2. Ensure you have internet connection\n";
echo "3. Verify your Claude API subscription is active\n";
