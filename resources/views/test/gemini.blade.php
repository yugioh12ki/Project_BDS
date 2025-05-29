<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Test Gemini AI Integration</title>
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
                    <div class="card-header bg-success text-white">
                        <h4><i class="fas fa-robot me-2"></i>Test Gemini AI Integration</h4>
                        <small>Test tích hợp Gemini AI với Database và FAQ</small>
                    </div>
                    <div class="card-body">

                        <!-- System Status -->
                        <div class="mb-4">
                            <h5><i class="fas fa-server me-2"></i>System Status</h5>
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <span>Gemini API Key:</span>
                                        <span id="apiKeyStatus" class="badge bg-secondary">Checking...</span>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <span>FAQ File:</span>
                                        <span id="faqFileStatus" class="badge bg-secondary">Checking...</span>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <span>Database:</span>
                                        <span id="dbStatus" class="badge bg-secondary">Checking...</span>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <span>Overall Health:</span>
                                        <span id="overallHealth" class="badge bg-secondary">Checking...</span>
                                    </div>
                                </div>
                            </div>
                            <div class="mt-2">
                                <button class="btn btn-sm btn-outline-info" onclick="refreshSystemStatus()">
                                    <i class="fas fa-sync me-1"></i>Refresh Status
                                </button>
                                <button class="btn btn-sm btn-outline-primary" onclick="testDatabaseConnection()">
                                    <i class="fas fa-database me-1"></i>Test DB Connection
                                </button>
                            </div>
                            <div id="systemStatusResult" class="alert mt-3" style="display:none;"></div>
                        </div>

                        <hr>

                        <!-- Test Basic Gemini -->
                        <div class="mb-4">
                            <h5><i class="fas fa-comment me-2"></i>1. Test Basic Gemini AI</h5>
                            <div class="row">
                                <div class="col-md-8">
                                    <input type="text" id="testMessage" class="form-control" placeholder="Nhập câu hỏi test..." value="Xin chào, bạn có thể giúp tôi gì?">
                                </div>
                                <div class="col-md-4">
                                    <button class="btn btn-success w-100" onclick="testBasicGemini()">
                                        <i class="fas fa-paper-plane me-1"></i>Test Gemini
                                    </button>
                                </div>
                            </div>
                            <div id="basicResult" class="alert mt-3" style="display:none;"></div>
                        </div>

                        <!-- Test FAQ Integration -->
                        <div class="mb-4">
                            <h5><i class="fas fa-database me-2"></i>2. Test FAQ Integration</h5>
                            <button class="btn btn-info" onclick="testFAQIntegration()">
                                <i class="fas fa-check-circle me-1"></i>Test FAQ + Gemini
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
                                    <button class="btn btn-outline-success w-100" onclick="testQuestion('Công ty mở cửa lúc mấy giờ?')">
                                        Giờ mở cửa
                                    </button>
                                </div>
                                <div class="col-md-3 mb-2">
                                    <button class="btn btn-outline-success w-100" onclick="testQuestion('Làm sao đổi mật khẩu?')">
                                        Đổi mật khẩu
                                    </button>
                                </div>
                                <div class="col-md-3 mb-2">
                                    <button class="btn btn-outline-success w-100" onclick="testQuestion('Tôi cần hỗ trợ')">
                                        Cần hỗ trợ
                                    </button>
                                </div>
                                <div class="col-md-3 mb-2">
                                    <button class="btn btn-outline-success w-100" onclick="testQuestion('Tôi muốn mua nhà')">
                                        Câu hỏi mới
                                    </button>
                                </div>
                            </div>
                            <div id="questionResult" class="alert mt-3" style="display:none;"></div>
                        </div>

                        <!-- Performance Test -->
                        <div class="mb-4">
                            <h5><i class="fas fa-tachometer-alt me-2"></i>5. Performance Test</h5>
                            <button class="btn btn-primary" onclick="performanceTest()">
                                <i class="fas fa-stopwatch me-1"></i>Test Response Time
                            </button>
                            <div id="performanceResult" class="alert mt-3" style="display:none;"></div>
                        </div>

                        <!-- Database Integration Test -->
                        <div class="mb-4">
                            <h5><i class="fas fa-database me-2"></i>6. Test Database Integration</h5>
                            <div class="row">
                                <div class="col-md-8">
                                    <input type="text" id="databaseTestMessage" class="form-control" placeholder="Nhập câu hỏi có liên quan đến database..." value="Tìm bất động sản ở Hà Nội">
                                </div>
                                <div class="col-md-4">
                                    <button class="btn btn-info w-100" onclick="testDatabaseSearch()">
                                        <i class="fas fa-search me-1"></i>Test Database Search
                                    </button>
                                </div>
                            </div>
                            <div id="databaseResult" class="alert mt-3" style="display:none;"></div>
                        </div>

                        <!-- Smart Search Test -->
                        <div class="mb-4">
                            <h5><i class="fas fa-brain me-2"></i>7. Test Smart Search</h5>
                            <div class="row">
                                <div class="col-md-8">
                                    <input type="text" id="smartSearchMessage" class="form-control" placeholder="Nhập từ khóa search..." value="BDS">
                                </div>
                                <div class="col-md-4">
                                    <button class="btn btn-success w-100" onclick="testSmartSearch()">
                                        <i class="fas fa-magic me-1"></i>Test Smart Search
                                    </button>
                                </div>
                            </div>
                            <div id="smartSearchResult" class="alert mt-3" style="display:none;"></div>
                        </div>

                        <!-- Database Test Scenarios -->
                        <div class="mb-4">
                            <h5><i class="fas fa-clipboard-list me-2"></i>8. Test Database Scenarios</h5>
                            <div class="row">
                                <div class="col-md-3 mb-2">
                                    <button class="btn btn-outline-primary w-100" onclick="testDatabaseQuestion('Tìm bất động sản ở Hà Nội')">
                                        BĐS Hà Nội
                                    </button>
                                </div>
                                <div class="col-md-3 mb-2">
                                    <button class="btn btn-outline-primary w-100" onclick="testDatabaseQuestion('Có nhà nào cho thuê không?')">
                                        Nhà cho thuê
                                    </button>
                                </div>
                                <div class="col-md-3 mb-2">
                                    <button class="btn btn-outline-primary w-100" onclick="testDatabaseQuestion('Thống kê tổng quan hệ thống')">
                                        Thống kê
                                    </button>
                                </div>
                                <div class="col-md-3 mb-2">
                                    <button class="btn btn-outline-warning w-100" onclick="testAllScenarios()">
                                        <i class="fas fa-play me-1"></i>All Scenarios
                                    </button>
                                </div>
                            </div>
                            <div id="scenarioResult" class="alert mt-3" style="display:none;"></div>
                        </div>

                        <!-- Quick Navigation -->
                        <div class="mb-4">
                            <h5><i class="fas fa-link me-2"></i>Quick Links</h5>
                            <div class="row">
                                <div class="col-md-3 mb-2">
                                    <a href="/chat" class="btn btn-outline-primary w-100">
                                        <i class="fas fa-comments me-1"></i>User Chat
                                    </a>
                                </div>
                                <div class="col-md-3 mb-2">
                                    <a href="/admin/chatbot/admin" class="btn btn-outline-secondary w-100">
                                        <i class="fas fa-cogs me-1"></i>Admin Panel
                                    </a>
                                </div>
                                <div class="col-md-3 mb-2">
                                    <a href="/admin/chatbot/faq" class="btn btn-outline-info w-100">
                                        <i class="fas fa-question me-1"></i>Manage FAQ
                                    </a>
                                </div>
                                <div class="col-md-3 mb-2">
                                    <a href="/chatbot/demo" class="btn btn-outline-warning w-100">
                                        <i class="fas fa-desktop me-1"></i>Demo Page
                                    </a>
                                </div>
                            </div>
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

        // Test basic Gemini AI
        function testBasicGemini() {
            const message = document.getElementById('testMessage').value;
            const resultDiv = document.getElementById('basicResult');

            showLoading(resultDiv, 'Đang test Gemini AI...');

            fetch('/test/gemini/ask', {
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
                        <h6><i class="fas fa-check-circle"></i> Gemini AI Response:</h6>
                        <div class="row">
                            <div class="col-md-6">
                                <strong>Question:</strong> ${data.message}<br>
                                <strong>FAQ Loaded:</strong> ${data.faq_loaded ? 'Yes' : 'No'}<br>
                                <strong>Confidence:</strong> ${data.gemini_response.confidence}<br>
                                <small class="text-muted">Time: ${data.timestamp}</small>
                            </div>
                            <div class="col-md-6">
                                <strong>Answer:</strong><br>
                                <div class="bg-light p-2 rounded">${data.gemini_response.answer}</div>
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

            fetch('/test/gemini/faq')
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
                                <strong>Confidence:</strong> ${data.gemini_response.confidence}
                            </div>
                            <div class="col-md-6">
                                <strong>Gemini Answer:</strong><br>
                                <div class="bg-light p-2 rounded">${data.gemini_response.answer}</div>
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

            fetch('/test/gemini/full', {
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
            testBasicGemini();
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

                fetch('/test/gemini/ask', {
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
                        success: data.success,
                        confidence: data.success ? data.gemini_response.confidence : 0
                    });

                    completedTests++;
                    if (completedTests === questions.length) {
                        const totalTime = Date.now() - startTime;
                        const avgTime = results.reduce((sum, r) => sum + r.responseTime, 0) / results.length;
                        const avgConfidence = results.reduce((sum, r) => sum + r.confidence, 0) / results.length;

                        resultDiv.className = 'alert alert-info';
                        resultDiv.innerHTML = `
                            <h6><i class="fas fa-tachometer-alt"></i> Performance Results:</h6>
                            <strong>Total Time:</strong> ${totalTime}ms<br>
                            <strong>Average Response Time:</strong> ${Math.round(avgTime)}ms<br>
                            <strong>Average Confidence:</strong> ${avgConfidence.toFixed(2)}<br>
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
                        error: error.message,
                        confidence: 0
                    });
                });
            });
        }

        // Test database integration
        function testDatabaseSearch() {
            const message = document.getElementById('databaseTestMessage').value;
            const resultDiv = document.getElementById('databaseResult');

            showLoading(resultDiv, 'Đang test database search...');

            fetch('/test/gemini/database', {
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
                        <h6><i class="fas fa-check-circle"></i> Database Search Test:</h6>
                        <div class="row">
                            <div class="col-md-6">
                                <strong>Question:</strong> ${data.message}<br>
                                <strong>Database Searched:</strong> ${data.database_searched ? 'Yes' : 'No'}<br>
                                <strong>Search Results:</strong> ${data.search_results_count} records<br>
                                <strong>Confidence:</strong> ${data.gemini_response.confidence}<br>
                                <strong>Response Time:</strong> ${data.response_time}ms
                            </div>
                            <div class="col-md-6">
                                <strong>AI Answer:</strong><br>
                                <div class="bg-light p-2 rounded">${data.gemini_response.answer}</div>
                                ${data.search_metadata ? `
                                <details class="mt-2">
                                    <summary>Search Metadata</summary>
                                    <pre class="small">${JSON.stringify(data.search_metadata, null, 2)}</pre>
                                </details>` : ''}
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

        // Test smart search
        function testSmartSearch() {
            const query = document.getElementById('smartSearchMessage').value;
            const resultDiv = document.getElementById('smartSearchResult');

            showLoading(resultDiv, 'Đang test smart search...');

            fetch('/test/gemini/smart-search', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({ query: query })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    resultDiv.className = 'alert alert-success';
                    resultDiv.innerHTML = `
                        <h6><i class="fas fa-check-circle"></i> Smart Search Results:</h6>
                        <div class="row">
                            <div class="col-md-12">
                                <strong>Query:</strong> ${data.query}<br>
                                <strong>Total Results:</strong> ${data.total_results}<br>
                                <strong>Search Time:</strong> ${data.search_time}ms<br><br>

                                <div class="table-responsive">
                                    <table class="table table-sm">
                                        <thead>
                                            <tr>
                                                <th>Table</th>
                                                <th>Count</th>
                                                <th>Sample Data</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            ${Object.entries(data.results).map(([table, items]) => `
                                                <tr>
                                                    <td><strong>${table}</strong></td>
                                                    <td>${Array.isArray(items) ? items.length : 0}</td>
                                                    <td>
                                                        ${Array.isArray(items) && items.length > 0 ?
                                                            `<details><summary>View</summary><pre class="small">${JSON.stringify(items.slice(0, 2), null, 2)}</pre></details>` :
                                                            'No data'}
                                                    </td>
                                                </tr>
                                            `).join('')}
                                        </tbody>
                                    </table>
                                </div>
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

        // Test database scenarios
        function testDatabaseQuestion(question) {
            document.getElementById('databaseTestMessage').value = question;
            testDatabaseSearch();
        }

        // Test all scenarios
        function testAllScenarios() {
            const resultDiv = document.getElementById('scenarioResult');
            showLoading(resultDiv, 'Đang test all database scenarios...');

            fetch('/test/gemini/scenarios')
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    resultDiv.className = 'alert alert-info';
                    resultDiv.innerHTML = `
                        <h6><i class="fas fa-chart-bar"></i> All Scenarios Test Results:</h6>
                        <div class="row">
                            <div class="col-md-12">
                                <strong>Total Scenarios:</strong> ${data.total_scenarios}<br>
                                <strong>Successful:</strong> ${data.successful_scenarios}<br>
                                <strong>Total Execution Time:</strong> ${data.total_time}ms<br>
                                <strong>Average Response Time:</strong> ${data.average_response_time}ms<br><br>

                                <div class="table-responsive">
                                    <table class="table table-sm">
                                        <thead>
                                            <tr>
                                                <th>Scenario</th>
                                                <th>Status</th>
                                                <th>Time</th>
                                                <th>Database Used</th>
                                                <th>Results</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            ${data.scenario_results.map(scenario => `
                                                <tr class="${scenario.success ? 'table-success' : 'table-danger'}">
                                                    <td>${scenario.description}</td>
                                                    <td>${scenario.success ? '✅' : '❌'}</td>
                                                    <td>${scenario.response_time}ms</td>
                                                    <td>${scenario.database_used ? 'Yes' : 'No'}</td>
                                                    <td>${scenario.results_count || 0}</td>
                                                </tr>
                                            `).join('')}
                                        </tbody>
                                    </table>
                                </div>

                                <details class="mt-3">
                                    <summary>Detailed Results</summary>
                                    <pre class="small">${JSON.stringify(data.detailed_results, null, 2)}</pre>
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

        // Test database connection
        function testDatabaseConnection() {
            const resultDiv = document.getElementById('systemStatusResult');
            showLoading(resultDiv, 'Testing database connection...');

            fetch('/test/gemini/db-connection')
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    resultDiv.className = 'alert alert-success';
                    resultDiv.innerHTML = `
                        <h6><i class="fas fa-check-circle"></i> Database Connection Test:</h6>
                        <div class="row">
                            <div class="col-md-6">
                                <strong>Status:</strong> ${data.database_connected ? 'Connected' : 'Failed'}<br>
                                <strong>Response Time:</strong> ${data.response_time}ms<br>
                                <strong>Test Time:</strong> ${data.timestamp}
                            </div>
                            <div class="col-md-6">
                                <strong>Database Stats:</strong><br>
                                <div class="bg-light p-2 rounded">
                                    <pre class="small mb-0">${JSON.stringify(data.stats, null, 2)}</pre>
                                </div>
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

        // Refresh system status
        function refreshSystemStatus() {
            // Reset all status indicators
            document.getElementById('apiKeyStatus').textContent = 'Checking...';
            document.getElementById('apiKeyStatus').className = 'badge bg-secondary';
            document.getElementById('faqFileStatus').textContent = 'Checking...';
            document.getElementById('faqFileStatus').className = 'badge bg-secondary';
            document.getElementById('dbStatus').textContent = 'Checking...';
            document.getElementById('dbStatus').className = 'badge bg-secondary';
            document.getElementById('overallHealth').textContent = 'Checking...';
            document.getElementById('overallHealth').className = 'badge bg-secondary';

            fetch('/test/gemini/status')
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const status = data.status;

                    // Update API Key status
                    document.getElementById('apiKeyStatus').textContent = status.gemini_api.configured ? 'Configured' : 'Missing';
                    document.getElementById('apiKeyStatus').className = status.gemini_api.configured ? 'badge bg-success' : 'badge bg-danger';

                    // Update FAQ status
                    document.getElementById('faqFileStatus').textContent = status.faq.loaded ? `Loaded (${status.faq.count})` : 'Not Found';
                    document.getElementById('faqFileStatus').className = status.faq.loaded ? 'badge bg-success' : 'badge bg-danger';

                    // Update Database status
                    document.getElementById('dbStatus').textContent = status.database.connected ? 'Connected' : 'Disconnected';
                    document.getElementById('dbStatus').className = status.database.connected ? 'badge bg-success' : 'badge bg-danger';

                    // Update Overall Health
                    const health = data.overall_health;
                    document.getElementById('overallHealth').textContent = `${health.health} (${health.percentage}%)`;
                    let healthClass = 'badge bg-danger';
                    if (health.percentage >= 90) healthClass = 'badge bg-success';
                    else if (health.percentage >= 75) healthClass = 'badge bg-warning';
                    else if (health.percentage >= 50) healthClass = 'badge bg-secondary';
                    document.getElementById('overallHealth').className = healthClass;
                } else {
                    console.error('Status check failed:', data.error);
                }
            })
            .catch(error => {
                console.error('Status check error:', error);
                document.getElementById('apiKeyStatus').textContent = 'Error';
                document.getElementById('apiKeyStatus').className = 'badge bg-danger';
                document.getElementById('faqFileStatus').textContent = 'Error';
                document.getElementById('faqFileStatus').className = 'badge bg-danger';
                document.getElementById('dbStatus').textContent = 'Error';
                document.getElementById('dbStatus').className = 'badge bg-danger';
                document.getElementById('overallHealth').textContent = 'Error';
                document.getElementById('overallHealth').className = 'badge bg-danger';
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
            const hasApiKey = {{ env('GEMINI_API_KEY') ? 'true' : 'false' }};
            document.getElementById('apiKeyStatus').textContent = hasApiKey ? 'Configured' : 'Missing';
            document.getElementById('apiKeyStatus').className = hasApiKey ? 'badge bg-success' : 'badge bg-danger';

            // Check FAQ file
            fetch('/test/gemini/faq')
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
