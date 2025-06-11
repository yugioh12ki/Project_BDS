# 🤖 Gemini AI System Performance Test Suite

## Tổng Quan

Test suite chuyên sâu để đánh giá hiệu suất hệ thống AI **Google Gemini 1.5 Flash** trong ứng dụng bất động sản, bao gồm chatbot hỗ trợ khách hàng và phân tích hợp đồng.

## 🎯 Mô Hình AI Được Test

### **Google Gemini 1.5 Flash**
- **Loại**: Large Language Model (LLM)
- **Kiến trúc**: Transformer-based Multimodal AI
- **Phương pháp**: Retrieval Augmented Generation (RAG)
- **Context Window**: 1M tokens
- **API Version**: v1beta

### **Cấu Hình Parameters**

#### Chatbot Configuration:
```php
'maxOutputTokens' => 1000,
'temperature' => 0.3,      // Low creativity, high consistency
'topP' => 0.8,            // Nucleus sampling  
'topK' => 40,             // Top-K sampling
```

#### Contract Analysis Configuration:
```php
'maxOutputTokens' => 2500,
'temperature' => 0.1,      // Maximum precision
'topP' => 0.8,
'topK' => 40,
```

## 🧪 Chức Năng Được Test

### 1. **Chatbot Performance**
- ✅ Database integration với RAG
- ✅ Vietnamese natural language processing
- ✅ Real estate domain knowledge
- ✅ Response time optimization
- ✅ Confidence scoring

### 2. **Contract Analysis**
- ✅ Legal document validation
- ✅ Risk assessment
- ✅ Compliance checking
- ✅ Structured analysis output
- ✅ Vietnamese legal terminology

### 3. **NLP Capabilities**
- ✅ Keyword extraction accuracy
- ✅ Entity recognition (location, price, property type)
- ✅ Intent classification
- ✅ Vietnamese diacritics handling

### 4. **Database Integration** 
- ✅ Smart search functionality
- ✅ Multi-table queries
- ✅ Real-time data retrieval
- ✅ Context building from DB results

### 5. **System Performance**
- ✅ Response time analysis
- ✅ Concurrent request handling
- ✅ Memory usage monitoring
- ✅ Error rate tracking

## 🚀 Cách Sử Dụng

### **Yêu Cầu Hệ Thống**
- PHP 8.0+
- Laravel 10+
- Composer
- PHPUnit
- GEMINI_API_KEY configured

### **Cài Đặt**

1. **Clone repository và install dependencies:**
```bash
composer install
```

2. **Cấu hình environment:**
```bash
cp .env.example .env
# Thêm GEMINI_API_KEY vào .env file
```

3. **Setup database:**
```bash
php artisan migrate
php artisan db:seed
```

### **Chạy Tests**

#### **Method 1: PowerShell Script (Recommended)**
```powershell
.\run_ai_tests.ps1
```

#### **Method 2: Laravel Artisan**
```bash
php artisan test tests/Feature/GeminiAISystemPerformanceTest.php --verbose
```

#### **Method 3: PHPUnit Direct**
```bash
./vendor/bin/phpunit tests/Feature/GeminiAISystemPerformanceTest.php --verbose
```

#### **Method 4: PHP Script**
```bash
php tests/ai_test_runner.php
```

## 📊 Kết Quả & Benchmarks

### **Performance Benchmarks**

| Metric | Excellent | Good | Acceptable | Poor |
|--------|-----------|------|------------|------|
| **Response Time** | < 1s | < 3s | < 5s | > 10s |
| **Confidence Score** | > 0.9 | > 0.8 | > 0.7 | < 0.6 |
| **Success Rate** | > 95% | > 90% | > 85% | < 80% |
| **DB Integration** | > 90% | > 80% | > 70% | < 60% |

### **NLP Accuracy Targets**

| Task | Target Accuracy |
|------|----------------|
| **Keyword Extraction** | > 90% |
| **Location Recognition** | > 90% |
| **Property Type** | > 85% |
| **Price Extraction** | > 88% |
| **ID Recognition** | > 95% |

### **Contract Analysis Benchmarks**

| Component | Minimum Score |
|-----------|---------------|
| **Validation Accuracy** | > 90% |
| **Analysis Completeness** | > 80% |
| **Legal Compliance** | > 8.0/10 |
| **Customer Protection** | > 7.0/10 |

## 📈 Báo Cáo

### **Output Files**

1. **JSON Log**: `storage/logs/ai_performance_test_TIMESTAMP.json`
   - Raw performance data
   - Detailed test results
   - Error logs

2. **HTML Report**: `tests/reports/ai_performance_report_TIMESTAMP.html` 
   - Visual dashboard
   - Performance charts
   - Executive summary

3. **Console Output**: Real-time progress và summary

### **Ví dụ Kết Quả**

```
🎯 SUCCESS RATE: 94.2%
⏱️  AVG RESPONSE TIME: 2.34s  
📊 AVG CONFIDENCE: 0.847
🔗 DB INTEGRATION RATE: 91.3%

🏆 OVERALL: EXCELLENT - Hệ thống AI hoạt động xuất sắc!
```

## 🔧 Tối Ưu Hóa

### **Performance Tuning**

1. **Temperature Adjustment:**
   - Chatbot: 0.2-0.4 (balance creativity/consistency)
   - Contract: 0.05-0.15 (maximum precision)

2. **Token Optimization:**
   - Monitor token usage
   - Adjust maxOutputTokens based on needs

3. **Database Optimization:**
   - Index frequently searched fields
   - Optimize query performance
   - Implement caching

### **Monitoring Recommendations**

1. **Regular Testing**: Weekly performance tests
2. **Alerting**: Setup alerts for degraded performance  
3. **Logging**: Comprehensive error tracking
4. **A/B Testing**: Test parameter variations

## 📋 Test Cases

### **Chatbot Test Scenarios**

```php
// Simple property search
'Tìm căn hộ ở Hà Nội' 
// Expected: DB search, location extraction

// Complex search with multiple criteria  
'Tìm biệt thự 3 tầng ở quận 1 TPHCM giá từ 15-20 tỷ có sổ hồng'
// Expected: Multiple filters, high accuracy

// User inquiry
'Thông tin agent có mã AG001'
// Expected: User lookup, ID extraction

// General question
'Quy trình mua nhà như thế nào?'
// Expected: FAQ response, no DB search
```

### **Contract Analysis Samples**

- ✅ Valid sale contracts
- ✅ Valid rental agreements  
- ✅ Invalid documents (should reject)
- ✅ Contracts with legal issues

## 🛠️ Troubleshooting

### **Common Issues**

1. **API Key Issues:**
```bash
# Check .env file
grep GEMINI_API_KEY .env

# Test API connectivity
php artisan tinker
>>> app(App\Services\GeminiAIService::class)->askGemini('test');
```

2. **Database Connection:**
```bash
# Test DB connection
php artisan db:show
```

3. **Memory Issues:**
```bash
# Increase memory limit
ini_set('memory_limit', '512M');
```

## 📞 Support

Để hỗ trợ và báo lỗi:

1. **Check logs**: `storage/logs/laravel.log`
2. **Review test output**: Console messages
3. **Validate configuration**: Environment settings
4. **Monitor performance**: Regular test runs

## 🔄 Phiên Bản

- **v1.0**: Initial release với Gemini 1.5 Flash
- **v1.1**: Enhanced contract analysis
- **v1.2**: Improved Vietnamese NLP
- **v1.3**: Advanced benchmarking metrics

---

**🤖 Powered by Google Gemini 1.5 Flash**  
**🏢 Real Estate AI Assistant**  
**🇻🇳 Optimized for Vietnamese Market**
