<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Test Claude AI Integration</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .result-container {
            max-height: 400px;
            overflow-y: auto;
        }
        .json-response {
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 0.375rem;
            padding: 1rem;
            font-family: 'Courier New', monospace;
            font-size: 0.875rem;
        }
    </style>
</head>
<body class="bg-light">
    <div class="container mt-5">
        <div class="row">
            <div class="col-md-10 mx-auto">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h4><i class="fas fa-robot me-2"></i>Test Claude AI Integration</h4>
                        <small>Test tích hợp Claude AI với Database và FAQ</small>
                    </div>
                    <div class="card-body">

                        <!-- System Status -->
                        <div class="mb-4">
                            <h5><i class="fas fa-server me-2"></i>System Status</h5>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <span>Claude API Key:</span>
                                        <span id="apiKeyStatus" class="badge bg-secondary">Checking...</span>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <span>FAQ File:</span>
                                        <span id="faqFileStatus" class="badge bg-secondary">Checking...</span>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <span>Database:</span>
                                        <span id="dbStatus" class="badge bg-success">Connected</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <hr>

                        <!-- Test Basic Claude -->
                        <div class="mb-4">
                            <h5><i class="fas fa-comment me-2"></i>1. Test Basic Claude AI</h5>
                            <div class="row">
                                <div class="col-md-8">
                                    <input type="text" id="testMessage" class="form-control" placeholder="Nhập câu hỏi test..." value="Xin chào, bạn có thể giúp tôi gì?">
                                </div>
                                <div class="col-md-4">
                                    <button class="btn btn-primary w-100" onclick="testBasicClaude()">
                                        <i class="fas fa-paper-plane me-1"></i>Test Claude
                                    </button>
                                </div>
                            </div>
                            <div id="basicResult" class="alert mt-3" style="display:none;"></div>
                        </div>

                        <!-- Test FAQ Integration -->
                        <div class="mb-4">
                            <h5><i class="fas fa-database me-2"></i>2. Test FAQ Integration</h5>
                            <button class="btn btn-success" onclick="testFAQIntegration()">
                                <i class="fas fa-check-circle me-1"></i>Test FAQ + Claude
                            </button>
                            <div id="faqResult" class="alert mt-3" style="display:none;"></div>
                        </div>

                        <!-- Test Full Chatbot -->
                        <div class="mb-4">
                            <h5><i class="fas fa-cogs me-2"></i>3. Test Full Chatbot Logic</h5>
                            <div class="row">
                                <div class="col-md-6">
                                    <input type="text" id="fullTestMessage" class="form-control mb-2" placeholder="Câu hỏi test..." value="Tôi cần hỗ trợ về giờ làm việc">
                                </div>
                                <div class="col-md-3">
                                    <select id="userType" class="form-control mb-2">
                                        <option value="guest">Guest User</option>
                                        <option value="user">Logged-in User</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <button class="btn btn-warning w-100" onclick="testFullChatbot()">
                                        <i class="fas fa-rocket me-1"></i>Test Full Logic
                                    </button>
                                </div>
                            </div>
                            <div id="fullResult" class="alert mt-3" style="display:none;"></div>
                        </div>

                        <!-- Test Common Questions -->
                        <div class="mb-4">
                            <h5><i class="fas fa-question-circle me-2"></i>4. Test FAQ Questions</h5>
                            <div class="row">
                                <div class="col-md-3 mb-2">
                                    <button class="btn btn-outline-primary w-100" onclick="testQuestion('Công ty mở cửa lúc mấy giờ?')">
                                        Giờ mở cửa
                                    </button>
                                </div>
                                <div class="col-md-3 mb-2">
                                    <button class="btn btn-outline-primary w-100" onclick="testQuestion('Làm sao đổi mật khẩu?')">
                                        Đổi mật khẩu
                                    </button>
                                </div>
                                <div class="col-md-3 mb-2">
                                    <button class="btn btn-outline-primary w-100" onclick="testQuestion('Tôi cần hỗ trợ')">
                                        Cần hỗ trợ
                                    </button>
                                </div>
                                <div class="col-md-3 mb-2">
                                    <button class="btn btn-outline-primary w-100" onclick="testQuestion('Tôi muốn mua nhà')">
                                        Câu hỏi mới
                                    </button>
                                </div>
                            </div>
                            <div id="questionResult" class="alert mt-3" style="display:none;"></div>
                        </div>

                        <!-- Performance Test -->
                        <div class="mb-4">
                            <h5><i class="fas fa-tachometer-alt me-2"></i>5. Performance Test</h5>
                            <button class="btn btn-info" onclick="performanceTest()">
                                <i class="fas fa-stopwatch me-1"></i>Test Response Time
                            </button>
                            <div id="performanceResult" class="alert mt-3" style="display:none;"></div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script>
        // Setup CSRF token
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        // Test basic Claude AI
        function testBasicClaude() {
            const message = document.getElementById('testMessage').value;
            const resultDiv = document.getElementById('basicResult');

            showLoading(resultDiv, 'Đang test Claude AI...');

            fetch('/test/claude/ask', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({ message: message })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    resultDiv.className = 'alert alert-success';
                    resultDiv.innerHTML = `
                        <h6><i class="fas fa-check-circle"></i> Claude AI Response:</h6>
                        <div class="row">
                            <div class="col-md-6">
                                <strong>Question:</strong> ${data.message}<br>
                                <strong>FAQ Loaded:</strong> ${data.faq_loaded ? 'Yes' : 'No'}<br>
                                <strong>Confidence:</strong> ${data.claude_response.confidence}<br>
                                <small class="text-muted">Time: ${data.timestamp}</small>
                            </div>
                            <div class="col-md-6">
                                <strong>Answer:</strong><br>
                                <div class="bg-light p-2 rounded">${data.claude_response.answer}</div>
                            </div>
                        </div>
                    `;
                } else {
                    showError(resultDiv, data.error);
                }
            })
            .catch(error => {
                showError(resultDiv, 'Network Error: ' + error.message);
            });
        }

        // Test FAQ integration
        function testFAQIntegration() {
            const resultDiv = document.getElementById('faqResult');

            showLoading(resultDiv, 'Đang test FAQ integration...');

            fetch('/test/claude/faq')
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    resultDiv.className = 'alert alert-success';
                    resultDiv.innerHTML = `
                        <h6><i class="fas fa-check-circle"></i> FAQ Integration Test:</h6>
                        <div class="row">
                            <div class="col-md-6">
                                <strong>FAQ Loaded:</strong> ${data.faq_loaded ? 'Yes' : 'No'}<br>
                                <strong>FAQ Count:</strong> ${data.faq_count} entries<br>
                                <strong>Test Question:</strong> ${data.test_question}<br>
                                <strong>Confidence:</strong> ${data.claude_response.confidence}
                            </div>
                            <div class="col-md-6">
                                <strong>Claude Answer:</strong><br>
                                <div class="bg-light p-2 rounded">${data.claude_response.answer}</div>
                                <details class="mt-2">
                                    <summary>FAQ Keys</summary>
                                    <pre class="small">${JSON.stringify(data.faq_keys, null, 2)}</pre>
                                </details>
                            </div>
                        </div>
                    `;
                } else {
                    showError(resultDiv, data.error);
                }
            })
            .catch(error => {
                showError(resultDiv, 'Network Error: ' + error.message);
            });
        }

        // Test full chatbot
        function testFullChatbot() {
            const message = document.getElementById('fullTestMessage').value;
            const userType = document.getElementById('userType').value;
            const resultDiv = document.getElementById('fullResult');

            showLoading(resultDiv, 'Đang test full chatbot logic...');

            fetch('/test/claude/full', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({
                    message: message,
                    user_type: userType
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    resultDiv.className = 'alert alert-success';
                    resultDiv.innerHTML = `
                        <h6><i class="fas fa-check-circle"></i> Full Chatbot Test:</h6>
                        <div class="result-container">
                            <div class="json-response">
                                <strong>Input:</strong><br>
                                ${JSON.stringify(data.input, null, 2)}<br><br>
                                <strong>Chatbot Response:</strong><br>
                                ${JSON.stringify(data.chatbot_response, null, 2)}
                            </div>
                        </div>
                    `;
                } else {
                    showError(resultDiv, data.error);
                }
            })
            .catch(error => {
                showError(resultDiv, 'Network Error: ' + error.message);
            });
        }

        // Test specific questions
        function testQuestion(question) {
            document.getElementById('testMessage').value = question;
            testBasicClaude();
        }

        // Performance test
        function performanceTest() {
            const resultDiv = document.getElementById('performanceResult');
            showLoading(resultDiv, 'Đang test performance...');

            const questions = [
                'Xin chào',
                'Công ty mở cửa lúc mấy giờ?',
                'Tôi cần hỗ trợ',
                'Làm sao đổi mật khẩu?',
                'Tôi muốn mua nhà'
            ];

            const startTime = Date.now();
            let completedTests = 0;
            const results = [];

            questions.forEach((question, index) => {
                const questionStartTime = Date.now();

                fetch('/test/claude/ask', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify({ message: question })
                })
                .then(response => response.json())
                .then(data => {
                    const responseTime = Date.now() - questionStartTime;
                    results.push({
                        question: question,
                        responseTime: responseTime,
                        success: data.success
                    });

                    completedTests++;
                    if (completedTests === questions.length) {
                        const totalTime = Date.now() - startTime;
                        const avgTime = results.reduce((sum, r) => sum + r.responseTime, 0) / results.length;

                        resultDiv.className = 'alert alert-info';
                        resultDiv.innerHTML = `
                            <h6><i class="fas fa-tachometer-alt"></i> Performance Results:</h6>
                            <strong>Total Time:</strong> ${totalTime}ms<br>
                            <strong>Average Response Time:</strong> ${Math.round(avgTime)}ms<br>
                            <strong>Tests Completed:</strong> ${completedTests}/${questions.length}<br>
                            <details class="mt-2">
                                <summary>Detailed Results</summary>
                                <pre class="small">${JSON.stringify(results, null, 2)}</pre>
                            </details>
                        `;
                    }
                })
                .catch(error => {
                    completedTests++;
                    results.push({
                        question: question,
                        responseTime: 0,
                        success: false,
                        error: error.message
                    });
                });
            });
        }

        // Helper functions
        function showLoading(element, message) {
            element.style.display = 'block';
            element.className = 'alert alert-info';
            element.innerHTML = `<i class="fas fa-spinner fa-spin"></i> ${message}`;
        }

        function showError(element, error) {
            element.className = 'alert alert-danger';
            element.innerHTML = `<i class="fas fa-exclamation-triangle"></i> Error: ${error}`;
        }

        // Check system status on load
        document.addEventListener('DOMContentLoaded', function() {
            // Check API key
            const hasApiKey = {{ env('CLAUDE_API_KEY') ? 'true' : 'false' }};
            document.getElementById('apiKeyStatus').textContent = hasApiKey ? 'Configured' : 'Missing';
            document.getElementById('apiKeyStatus').className = hasApiKey ? 'badge bg-success' : 'badge bg-danger';

            // Check FAQ file
            fetch('/test/claude/faq')
            .then(response => response.json())
            .then(data => {
                document.getElementById('faqFileStatus').textContent = data.success ? `Available (${data.faq_count})` : 'Error';
                document.getElementById('faqFileStatus').className = data.success ? 'badge bg-success' : 'badge bg-danger';
            })
            .catch(() => {
                document.getElementById('faqFileStatus').textContent = 'Error';
                document.getElementById('faqFileStatus').className = 'badge bg-danger';
            });
        });
    </script>
</body>
</html>
