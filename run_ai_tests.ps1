# ====================================================================
# GEMINI AI SYSTEM PERFORMANCE TEST SUITE
# ====================================================================
# Script để chạy đầy đủ test hiệu suất hệ thống AI Gemini 1.5 Flash
# Bao gồm: Chatbot, Contract Analysis, NLP, Database Integration
# ====================================================================

Write-Host "=" * 80 -ForegroundColor Cyan
Write-Host "           GEMINI 1.5 FLASH AI SYSTEM PERFORMANCE TEST" -ForegroundColor Yellow
Write-Host "=" * 80 -ForegroundColor Cyan
Write-Host ""

# Thông tin hệ thống AI
Write-Host "🤖 MÔ HÌNH AI ĐANG TEST:" -ForegroundColor Green
Write-Host "   • Model: Google Gemini 1.5 Flash" -ForegroundColor White
Write-Host "   • Type: Large Language Model (LLM)" -ForegroundColor White
Write-Host "   • Architecture: Transformer-based Multimodal AI" -ForegroundColor White
Write-Host "   • Approach: Retrieval Augmented Generation (RAG)" -ForegroundColor White
Write-Host "   • Context Window: 1M tokens" -ForegroundColor White
Write-Host "   • API: generativelanguage.googleapis.com/v1beta" -ForegroundColor White
Write-Host ""

# Cấu hình parameters
Write-Host "⚙️  PARAMETERS CONFIGURATION:" -ForegroundColor Green
Write-Host "   • Chatbot Temperature: 0.3 (Low creativity, high consistency)" -ForegroundColor White
Write-Host "   • Contract Analysis Temperature: 0.1 (Maximum precision)" -ForegroundColor White
Write-Host "   • Top-P: 0.8 (Nucleus sampling)" -ForegroundColor White
Write-Host "   • Top-K: 40 (Top-K sampling)" -ForegroundColor White
Write-Host "   • Max Tokens: 1000 (Chatbot) / 2500 (Contract)" -ForegroundColor White
Write-Host ""

# Chức năng test
Write-Host "🎯 CHỨC NĂNG ĐANG TEST:" -ForegroundColor Green
Write-Host "   1. Chatbot hỗ trợ khách hàng bất động sản" -ForegroundColor White
Write-Host "   2. Tích hợp cơ sở dữ liệu thông minh (RAG)" -ForegroundColor White
Write-Host "   3. Phân tích hợp đồng bất động sản" -ForegroundColor White
Write-Host "   4. Xử lý ngôn ngữ tự nhiên tiếng Việt" -ForegroundColor White
Write-Host "   5. Confidence scoring system" -ForegroundColor White
Write-Host "   6. Stress testing & scalability" -ForegroundColor White
Write-Host ""

# Kiểm tra environment
Write-Host "🔍 KIỂM TRA ENVIRONMENT..." -ForegroundColor Yellow

# Check PHP
try {
    $phpVersion = php -v 2>$null | Select-String "PHP" | Select-Object -First 1
    if ($phpVersion) {
        Write-Host "   ✅ PHP: $($phpVersion.Line.Split()[1])" -ForegroundColor Green
    } else {
        Write-Host "   ❌ PHP not found!" -ForegroundColor Red
        exit 1
    }
} catch {
    Write-Host "   ❌ PHP not available!" -ForegroundColor Red
    exit 1
}

# Check Laravel
try {
    $laravelVersion = php artisan --version 2>$null
    if ($laravelVersion) {
        Write-Host "   ✅ Laravel: $laravelVersion" -ForegroundColor Green
    }
} catch {
    Write-Host "   ⚠️  Laravel artisan not available" -ForegroundColor Yellow
}

# Check .env file
if (Test-Path ".env") {
    Write-Host "   ✅ .env file exists" -ForegroundColor Green

    # Check GEMINI_API_KEY
    $envContent = Get-Content ".env"
    $geminiKey = $envContent | Where-Object { $_ -like "GEMINI_API_KEY=*" }
    if ($geminiKey) {
        Write-Host "   ✅ GEMINI_API_KEY configured" -ForegroundColor Green
    } else {
        Write-Host "   ⚠️  GEMINI_API_KEY not found in .env" -ForegroundColor Yellow
    }
} else {
    Write-Host "   ❌ .env file not found!" -ForegroundColor Red
    Write-Host "   Please create .env file and configure GEMINI_API_KEY" -ForegroundColor Red
    exit 1
}

Write-Host ""

# Tạo thư mục reports nếu chưa có
$reportsDir = "tests\reports"
if (!(Test-Path $reportsDir)) {
    New-Item -ItemType Directory -Force -Path $reportsDir | Out-Null
    Write-Host "   📁 Created reports directory" -ForegroundColor Green
}

# Tạo thư mục logs nếu chưa có
$logsDir = "storage\logs"
if (!(Test-Path $logsDir)) {
    New-Item -ItemType Directory -Force -Path $logsDir | Out-Null
    Write-Host "   📁 Created logs directory" -ForegroundColor Green
}

Write-Host "🚀 STARTING AI PERFORMANCE TESTS..." -ForegroundColor Yellow
Write-Host ""

# Ghi lại thời gian bắt đầu
$startTime = Get-Date
$timestamp = $startTime.ToString("yyyy_MM_dd_HH_mm_ss")

# Chạy PHPUnit tests
Write-Host "📊 RUNNING PHPUNIT TESTS..." -ForegroundColor Cyan
try {
    # Chạy test với output verbose
    $testOutput = php artisan test tests/Feature/GeminiAISystemPerformanceTest.php --verbose 2>&1

    Write-Host $testOutput

    # Kiểm tra kết quả
    if ($LASTEXITCODE -eq 0) {
        Write-Host "   ✅ PHPUnit tests completed successfully!" -ForegroundColor Green
    } else {
        Write-Host "   ⚠️  Some tests may have failed (Exit code: $LASTEXITCODE)" -ForegroundColor Yellow
    }
} catch {
    Write-Host "   ❌ Error running PHPUnit tests: $($_.Exception.Message)" -ForegroundColor Red
}

Write-Host ""

# Tìm file log mới nhất
Write-Host "📄 PROCESSING TEST RESULTS..." -ForegroundColor Cyan

$logFiles = Get-ChildItem "storage\logs\ai_performance_test_*.json" -ErrorAction SilentlyContinue | Sort-Object LastWriteTime -Descending
if ($logFiles.Count -gt 0) {
    $latestLog = $logFiles[0]
    Write-Host "   📋 Latest log: $($latestLog.Name)" -ForegroundColor Green

    try {
        $reportData = Get-Content $latestLog.FullName | ConvertFrom-Json

        # Tạo summary
        Write-Host ""
        Write-Host "📈 TEST SUMMARY:" -ForegroundColor Yellow

        if ($reportData.'Success rate') {
            $successRate = [decimal]$reportData.'Success rate'.Replace('%', '')
            Write-Host "   🎯 Success Rate: $($reportData.'Success rate')" -ForegroundColor $(if ($successRate -ge 90) { "Green" } elseif ($successRate -ge 80) { "Yellow" } else { "Red" })
        }

        if ($reportData.'Tổng thời gian test') {
            Write-Host "   ⏱️  Total Time: $($reportData.'Tổng thời gian test')" -ForegroundColor White
        }

        if ($reportData.'Tổng số test cases') {
            Write-Host "   📊 Total Test Cases: $($reportData.'Tổng số test cases')" -ForegroundColor White
        }

        # Đánh giá tổng thể
        Write-Host ""
        Write-Host "🏆 OVERALL ASSESSMENT:" -ForegroundColor Yellow

        if ($successRate -ge 95) {
            Write-Host "   🌟 EXCELLENT: Hệ thống AI hoạt động xuất sắc!" -ForegroundColor Green
        } elseif ($successRate -ge 90) {
            Write-Host "   ✅ VERY GOOD: Hệ thống AI hoạt động rất tốt!" -ForegroundColor Green
        } elseif ($successRate -ge 85) {
            Write-Host "   👍 GOOD: Hệ thống AI hoạt động tốt!" -ForegroundColor Yellow
        } elseif ($successRate -ge 80) {
            Write-Host "   ⚠️  ACCEPTABLE: Hệ thống AI cần cải thiện!" -ForegroundColor Yellow
        } else {
            Write-Host "   ❌ NEEDS IMPROVEMENT: Hệ thống AI cần tối ưu hóa!" -ForegroundColor Red
        }

    } catch {
        Write-Host "   ⚠️  Could not parse log file: $($_.Exception.Message)" -ForegroundColor Yellow
    }
} else {
    Write-Host "   ⚠️  No log files found" -ForegroundColor Yellow
}

Write-Host ""

# Tạo HTML report
Write-Host "📊 GENERATING HTML REPORT..." -ForegroundColor Cyan

$htmlTemplate = "tests\reports\ai_performance_report_template.html"
$htmlReport = "tests\reports\ai_performance_report_$timestamp.html"

if (Test-Path $htmlTemplate) {
    try {
        $htmlContent = Get-Content $htmlTemplate -Raw

        # Replace timestamp placeholder
        $htmlContent = $htmlContent -replace '\[TIMESTAMP_PLACEHOLDER\]', $startTime.ToString("yyyy-MM-dd HH:mm:ss")

        # Save report
        $htmlContent | Set-Content $htmlReport -Encoding UTF8

        Write-Host "   ✅ HTML Report generated: $htmlReport" -ForegroundColor Green

        # Mở báo cáo trong browser
        Write-Host "   🌐 Opening report in browser..." -ForegroundColor Cyan
        Start-Process $htmlReport

    } catch {
        Write-Host "   ❌ Error generating HTML report: $($_.Exception.Message)" -ForegroundColor Red
    }
} else {
    Write-Host "   ⚠️  HTML template not found" -ForegroundColor Yellow
}

Write-Host ""

# Thống kê cuối
$endTime = Get-Date
$duration = $endTime - $startTime

Write-Host "=" * 80 -ForegroundColor Cyan
Write-Host "                        TEST COMPLETED!" -ForegroundColor Green
Write-Host "=" * 80 -ForegroundColor Cyan
Write-Host ""
Write-Host "🕐 Start Time: $($startTime.ToString('yyyy-MM-dd HH:mm:ss'))" -ForegroundColor White
Write-Host "🕐 End Time: $($endTime.ToString('yyyy-MM-dd HH:mm:ss'))" -ForegroundColor White
Write-Host "⏱️  Duration: $($duration.ToString('hh\:mm\:ss'))" -ForegroundColor White
Write-Host ""
Write-Host "📁 Files Generated:" -ForegroundColor Yellow
if ($logFiles.Count -gt 0) {
    Write-Host "   📋 JSON Log: $($latestLog.FullName)" -ForegroundColor White
}
if (Test-Path $htmlReport) {
    Write-Host "   🌐 HTML Report: $htmlReport" -ForegroundColor White
}
Write-Host ""

# Recommendations
Write-Host "💡 NEXT STEPS:" -ForegroundColor Yellow
Write-Host "   1. Review detailed HTML report for insights" -ForegroundColor White
Write-Host "   2. Check JSON logs for raw performance data" -ForegroundColor White
Write-Host "   3. Compare results with previous test runs" -ForegroundColor White
Write-Host "   4. Optimize parameters based on findings" -ForegroundColor White
Write-Host "   5. Schedule regular performance monitoring" -ForegroundColor White
Write-Host ""

Write-Host "🎉 Gemini 1.5 Flash AI System Performance Test Complete!" -ForegroundColor Green

# Pause to let user read results
Write-Host ""
Write-Host "Press any key to continue..." -ForegroundColor Gray
$null = $Host.UI.RawUI.ReadKey("NoEcho,IncludeKeyDown")
