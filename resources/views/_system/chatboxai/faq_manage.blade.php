<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý FAQ - Chatbot AI</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .main-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }

        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            backdrop-filter: blur(10px);
            background: rgba(255, 255, 255, 0.95);
        }

        .card-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 15px 15px 0 0 !important;
            padding: 20px 25px;
        }

        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            border-radius: 10px;
            padding: 10px 20px;
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }

        .btn-warning {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            border: none;
            color: white;
        }

        .btn-danger {
            background: linear-gradient(135deg, #fc466b 0%, #3f5efb 100%);
            border: none;
        }

        .faq-item {
            border: 1px solid #e0e0e0;
            border-radius: 10px;
            margin-bottom: 15px;
            padding: 20px;
            background: white;
            transition: all 0.3s ease;
        }

        .faq-item:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }

        .faq-question {
            font-weight: 600;
            color: #333;
            margin-bottom: 10px;
            font-size: 16px;
        }

        .faq-answer {
            color: #666;
            margin-bottom: 10px;
            line-height: 1.6;
        }

        .faq-keywords {
            font-size: 12px;
            color: #888;
            font-style: italic;
        }

        .keyword-badge {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 2px 8px;
            border-radius: 12px;
            font-size: 11px;
            margin-right: 5px;
            display: inline-block;
            margin-bottom: 3px;
        }

        .modal-content {
            border-radius: 15px;
            border: none;
        }

        .modal-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 15px 15px 0 0;
        }

        .form-control, .form-select {
            border-radius: 10px;
            border: 1px solid #ddd;
            padding: 12px 15px;
            transition: all 0.3s ease;
        }

        .form-control:focus, .form-select:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }

        .stats-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 20px;
            text-align: center;
        }

        .loading {
            display: none;
            text-align: center;
            padding: 20px;
        }

        .empty-state {
            text-align: center;
            padding: 50px 20px;
            color: #666;
        }

        .empty-state i {
            font-size: 64px;
            color: #ddd;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="main-container">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="text-white mb-0">
                    <i class="fas fa-question-circle me-2"></i>
                    Quản lý FAQ
                </h1>
                <p class="text-white-50 mb-0">Quản lý câu hỏi thường gặp cho Chatbot AI</p>
            </div>
            <div>
                <a href="{{ route('admin.chatbot.admin') }}" class="btn btn-outline-light me-2">
                    <i class="fas fa-arrow-left me-1"></i>
                    Quay lại Admin
                </a>
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addFAQModal">
                    <i class="fas fa-plus me-1"></i>
                    Thêm FAQ
                </button>
            </div>
        </div>

        <!-- Stats -->
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="stats-card">
                    <div class="h3 mb-0" id="totalFAQs">{{ count($faqs) }}</div>
                    <div>Tổng số FAQ</div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stats-card">
                    <div class="h3 mb-0" id="recentFAQs">
                        {{ collect($faqs)->filter(function($faq) {
                            return isset($faq['created_at']) &&
                                   \Carbon\Carbon::parse($faq['created_at'])->isAfter(\Carbon\Carbon::now()->subDays(7));
                        })->count() }}
                    </div>
                    <div>FAQ trong 7 ngày</div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stats-card">
                    <div class="h3 mb-0" id="keywordCount">
                        {{ collect($faqs)->sum(function($faq) {
                            return count($faq['keywords'] ?? []);
                        }) }}
                    </div>
                    <div>Tổng từ khóa</div>
                </div>
            </div>
        </div>

        <!-- Main Card -->
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="mb-0">
                            <i class="fas fa-list me-2"></i>
                            Danh sách FAQ
                        </h4>
                    </div>
                    <div>
                        <input type="text" class="form-control" id="searchFAQ" placeholder="Tìm kiếm FAQ..." style="width: 300px; background: rgba(255,255,255,0.9);">
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="loading" id="loading">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Đang tải...</span>
                    </div>
                    <p class="mt-2 mb-0">Đang tải dữ liệu FAQ...</p>
                </div>

                <div id="faqContainer">
                    @if(count($faqs) > 0)
                        @foreach($faqs as $index => $faq)
                        <div class="faq-item" data-index="{{ $index }}">
                            <div class="row">
                                <div class="col-md-8">
                                    <div class="faq-question">
                                        <i class="fas fa-question-circle text-primary me-2"></i>
                                        {{ $faq['question'] }}
                                    </div>
                                    <div class="faq-answer">
                                        {{ Str::limit($faq['answer'], 200) }}
                                    </div>
                                    @if(isset($faq['keywords']) && is_array($faq['keywords']) && count($faq['keywords']) > 0)
                                    <div class="faq-keywords">
                                        <strong>Từ khóa:</strong>
                                        @foreach($faq['keywords'] as $keyword)
                                            <span class="keyword-badge">{{ $keyword }}</span>
                                        @endforeach
                                    </div>
                                    @endif
                                </div>
                                <div class="col-md-4 text-end">
                                    <div class="mb-2">
                                        @if(isset($faq['created_at']))
                                        <small class="text-muted">
                                            <i class="fas fa-calendar me-1"></i>
                                            {{ \Carbon\Carbon::parse($faq['created_at'])->format('d/m/Y H:i') }}
                                        </small>
                                        @endif
                                    </div>
                                    <div class="btn-group">
                                        <button class="btn btn-sm btn-warning edit-faq" data-index="{{ $index }}" title="Chỉnh sửa">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="btn btn-sm btn-danger delete-faq" data-index="{{ $index }}" title="Xóa">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    @else
                        <div class="empty-state">
                            <i class="fas fa-question-circle"></i>
                            <h4>Chưa có FAQ nào</h4>
                            <p>Hãy thêm câu hỏi thường gặp đầu tiên cho chatbot</p>
                            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addFAQModal">
                                <i class="fas fa-plus me-1"></i>
                                Thêm FAQ đầu tiên
                            </button>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Add FAQ Modal -->
    <div class="modal fade" id="addFAQModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-plus me-2"></i>
                        Thêm FAQ mới
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form id="addFAQForm">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">
                                <i class="fas fa-question me-1"></i>
                                Câu hỏi <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control" name="question" required
                                   placeholder="Nhập câu hỏi thường gặp...">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">
                                <i class="fas fa-comment me-1"></i>
                                Câu trả lời <span class="text-danger">*</span>
                            </label>
                            <textarea class="form-control" name="answer" rows="4" required
                                      placeholder="Nhập câu trả lời chi tiết..."></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">
                                <i class="fas fa-tags me-1"></i>
                                Từ khóa (phân cách bằng dấu phẩy)
                            </label>
                            <input type="text" class="form-control" name="keywords"
                                   placeholder="ví dụ: giá cả, thanh toán, hợp đồng...">
                            <div class="form-text">Giúp chatbot tìm câu trả lời phù hợp hơn</div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="fas fa-times me-1"></i>
                            Hủy
                        </button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i>
                            Lưu FAQ
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit FAQ Modal -->
    <div class="modal fade" id="editFAQModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-edit me-2"></i>
                        Chỉnh sửa FAQ
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form id="editFAQForm">
                    <input type="hidden" id="editIndex" name="index">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">
                                <i class="fas fa-question me-1"></i>
                                Câu hỏi <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control" id="editQuestion" name="question" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">
                                <i class="fas fa-comment me-1"></i>
                                Câu trả lời <span class="text-danger">*</span>
                            </label>
                            <textarea class="form-control" id="editAnswer" name="answer" rows="4" required></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">
                                <i class="fas fa-tags me-1"></i>
                                Từ khóa (phân cách bằng dấu phẩy)
                            </label>
                            <input type="text" class="form-control" id="editKeywords" name="keywords">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="fas fa-times me-1"></i>
                            Hủy
                        </button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i>
                            Cập nhật
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        // Setup CSRF token
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        // FAQ data
        let faqs = @json($faqs);

        // Search functionality
        $('#searchFAQ').on('keyup', function() {
            const searchTerm = $(this).val().toLowerCase();
            $('.faq-item').each(function() {
                const question = $(this).find('.faq-question').text().toLowerCase();
                const answer = $(this).find('.faq-answer').text().toLowerCase();
                const keywords = $(this).find('.faq-keywords').text().toLowerCase();

                if (question.includes(searchTerm) || answer.includes(searchTerm) || keywords.includes(searchTerm)) {
                    $(this).show();
                } else {
                    $(this).hide();
                }
            });
        });

        // Add FAQ
        $('#addFAQForm').on('submit', function(e) {
            e.preventDefault();

            const formData = new FormData(this);
            const submitBtn = $(this).find('button[type="submit"]');
            const originalText = submitBtn.html();

            submitBtn.html('<i class="fas fa-spinner fa-spin me-1"></i> Đang lưu...');
            submitBtn.prop('disabled', true);

            $.ajax({
                url: '{{ route("admin.chatbot.faq.add") }}',
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    if (response.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Thành công!',
                            text: response.message,
                            timer: 2000,
                            showConfirmButton: false
                        });

                        $('#addFAQModal').modal('hide');
                        $('#addFAQForm')[0].reset();
                        location.reload(); // Reload để cập nhật danh sách
                    }
                },
                error: function(xhr) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Lỗi!',
                        text: 'Có lỗi xảy ra khi thêm FAQ'
                    });
                },
                complete: function() {
                    submitBtn.html(originalText);
                    submitBtn.prop('disabled', false);
                }
            });
        });

        // Edit FAQ
        $(document).on('click', '.edit-faq', function() {
            const index = $(this).data('index');
            const faq = faqs[index];

            $('#editIndex').val(index);
            $('#editQuestion').val(faq.question);
            $('#editAnswer').val(faq.answer);
            $('#editKeywords').val(faq.keywords ? faq.keywords.join(', ') : '');

            $('#editFAQModal').modal('show');
        });

        $('#editFAQForm').on('submit', function(e) {
            e.preventDefault();

            const index = $('#editIndex').val();
            const formData = new FormData(this);
            const submitBtn = $(this).find('button[type="submit"]');
            const originalText = submitBtn.html();

            submitBtn.html('<i class="fas fa-spinner fa-spin me-1"></i> Đang cập nhật...');
            submitBtn.prop('disabled', true);

            $.ajax({
                url: `{{ route("admin.chatbot.faq.update", ":index") }}`.replace(':index', index),
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                headers: {
                    'X-HTTP-Method-Override': 'PUT'
                },
                success: function(response) {
                    if (response.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Thành công!',
                            text: response.message,
                            timer: 2000,
                            showConfirmButton: false
                        });

                        $('#editFAQModal').modal('hide');
                        location.reload();
                    }
                },
                error: function(xhr) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Lỗi!',
                        text: 'Có lỗi xảy ra khi cập nhật FAQ'
                    });
                },
                complete: function() {
                    submitBtn.html(originalText);
                    submitBtn.prop('disabled', false);
                }
            });
        });

        // Delete FAQ
        $(document).on('click', '.delete-faq', function() {
            const index = $(this).data('index');
            const faq = faqs[index];

            Swal.fire({
                title: 'Xác nhận xóa?',
                text: `Bạn có chắc muốn xóa FAQ: "${faq.question}"?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Xóa',
                cancelButtonText: 'Hủy'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: `{{ route("admin.chatbot.faq.delete", ":index") }}`.replace(':index', index),
                        method: 'DELETE',
                        success: function(response) {
                            if (response.success) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Đã xóa!',
                                    text: response.message,
                                    timer: 2000,
                                    showConfirmButton: false
                                });

                                location.reload();
                            }
                        },
                        error: function(xhr) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Lỗi!',
                                text: 'Có lỗi xảy ra khi xóa FAQ'
                            });
                        }
                    });
                }
            });
        });
    </script>
</body>
</html>
