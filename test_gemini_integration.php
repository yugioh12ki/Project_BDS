<?php

// Test file for Gemini AI integration
// Run this from the command line: php test_gemini_integration.php

require_once __DIR__ . '/vendor/autoload.php';

use App\Services\GeminiAIService;
use Dotenv\Dotenv;

// Load environment variables
$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

// Test Gemini AI Service
echo "Testing Gemini AI Integration...\n";
echo "================================\n\n";

try {
    $geminiService = new GeminiAIService();

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

    $result = $geminiService->askGemini(
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

    $result2 = $geminiService->askGemini(
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

    // Test escalation logic
    echo "Test 3: Escalation logic\n";
    echo "Testing shouldEscalateToAdmin() method...\n";

    // Test guest user with low confidence
    $shouldEscalate1 = $geminiService->shouldEscalateToAdmin(0.3, 'guest');
    echo "Guest user, confidence 0.3: " . ($shouldEscalate1 ? "Escalate" : "No escalation") . "\n";

    // Test logged-in user with low confidence
    $shouldEscalate2 = $geminiService->shouldEscalateToAdmin(0.3, 'user');
    echo "Logged-in user, confidence 0.3: " . ($shouldEscalate2 ? "Escalate" : "No escalation") . "\n";

    // Test high confidence
    $shouldEscalate3 = $geminiService->shouldEscalateToAdmin(0.8, 'user');
    echo "User, confidence 0.8: " . ($shouldEscalate3 ? "Escalate" : "No escalation") . "\n\n";

} catch (Exception $e) {
    echo "❌ Exception occurred: " . $e->getMessage() . "\n";
    echo "Make sure you have set GEMINI_API_KEY in your .env file\n";
}

echo "Test completed!\n";
echo "\nNote: If tests fail with API errors, please:\n";
echo "1. Add your actual Gemini API key to .env file\n";
echo "2. Ensure you have internet connection\n";
echo "3. Verify your Gemini API subscription is active\n";
echo "4. Check if your API quota hasn't been exceeded\n";
