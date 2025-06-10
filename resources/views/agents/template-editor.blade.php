<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chỉnh sửa Template</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- TinyMCE Editor -->
    <script src="https://cdn.tiny.cloud/1/no-api-key/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>

    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f8f9fa;
        }

        .editor-container {
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            padding: 20px;
            margin: 20px;
        }

        .editor-header {
            border-bottom: 1px solid #dee2e6;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }

        .save-btn {
            background: linear-gradient(135deg, #28a745, #20c997);
            border: none;
            color: white;
            padding: 10px 20px;
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .save-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(40, 167, 69, 0.3);
        }

        .toolbar {
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            padding: 10px;
            margin-bottom: 15px;
        }

        .template-info {
            background: #e3f2fd;
            border: 1px solid #bbdefb;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="editor-container">
            <div class="editor-header">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="mb-1">
                            <i class="fas fa-edit text-primary me-2"></i>
                            Chỉnh sửa Template
                        </h4>
                        <small class="text-muted">{{ $filename }}</small>
                    </div>
                    <div>
                        <button type="button" class="btn btn-outline-secondary me-2" onclick="window.close()">
                            <i class="fas fa-times me-1"></i>Đóng
                        </button>
                        <button type="button" class="btn save-btn" onclick="saveTemplate()">
                            <i class="fas fa-save me-1"></i>Lưu thay đổi
                        </button>
                    </div>
                </div>
            </div>

            <div class="template-info">
                <div class="row">
                    <div class="col-md-6">
                        <h6><i class="fas fa-info-circle me-1"></i>Thông tin Template</h6>
                        <p class="mb-1"><strong>Tên file:</strong> {{ $filename }}</p>
                        <p class="mb-0"><strong>Đường dẫn:</strong> {{ $filePath }}</p>
                    </div>
                    <div class="col-md-6">
                        <h6><i class="fas fa-lightbulb me-1"></i>Hướng dẫn</h6>
                        <ul class="mb-0 small">
                            <li>Sử dụng các biến: {customer_name}, {property_address}, {price}</li>
                            <li>Định dạng văn bản bằng thanh công cụ</li>
                            <li>Nhấn "Lưu thay đổi" để cập nhật template</li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="toolbar">
                <small class="text-muted">
                    <i class="fas fa-tools me-1"></i>
                    Sử dụng thanh công cụ bên dưới để định dạng nội dung template
                </small>
            </div>

            <!-- Text Editor -->
            <div id="editor-wrapper">
                <textarea id="templateEditor" name="content">{{ $templateContent ?? 'Đang tải nội dung template...' }}</textarea>
            </div>

            <!-- Auto-save indicator -->
            <div class="mt-3">
                <small class="text-muted">
                    <i class="fas fa-clock me-1"></i>
                    <span id="saveStatus">Sẵn sàng chỉnh sửa</span>
                </small>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        let editor = null;
        let autoSaveInterval = null;
        const filename = '{{ $filename }}';

        // Initialize TinyMCE editor
        tinymce.init({
            selector: '#templateEditor',
            height: 500,
            menubar: true,
            plugins: [
                'advlist', 'autolink', 'lists', 'link', 'image', 'charmap', 'preview',
                'anchor', 'searchreplace', 'visualblocks', 'code', 'fullscreen',
                'insertdatetime', 'media', 'table', 'help', 'wordcount', 'save'
            ],
            toolbar: 'undo redo | blocks | ' +
                'bold italic forecolor backcolor | alignleft aligncenter ' +
                'alignright alignjustify | bullist numlist outdent indent | ' +
                'removeformat | table | save | help',
            content_style: 'body { font-family: Segoe UI,Tahoma,Geneva,Verdana,sans-serif; font-size: 14px }',
            save_onsavecallback: function () {
                saveTemplate();
            },
            setup: function (ed) {
                editor = ed;
                ed.on('change', function() {
                    updateSaveStatus('Có thay đổi chưa lưu');
                });
            },
            language: 'vi'
        });

        // Load template content
        async function loadTemplateContent() {
            try {
                // For now, we'll use a placeholder content
                // In production, you'd load the actual file content
                const content = `
                    <h2>HỢP ĐỒNG THUÊ NHÀ</h2>

                    <p><strong>Bên cho thuê:</strong> [Thông tin chủ nhà]</p>
                    <p><strong>Bên thuê:</strong> {customer_name}</p>
                    <p><strong>Địa chỉ bất động sản:</strong> {property_address}</p>
                    <p><strong>Giá thuê:</strong> {price} VNĐ/tháng</p>

                    <h3>ĐIỀU KHOẢN HỢP ĐỒNG</h3>

                    <p><strong>Điều 1: Đối tượng cho thuê</strong></p>
                    <p>Bên cho thuê đồng ý cho bên thuê thuê căn nhà tại địa chỉ: {property_address}</p>

                    <p><strong>Điều 2: Thời hạn thuê</strong></p>
                    <p>Thời hạn thuê nhà là {rent_months} tháng, kể từ ngày {start_date}</p>

                    <p><strong>Điều 3: Giá thuê và phương thức thanh toán</strong></p>
                    <p>Giá thuê nhà: {price} VNĐ/tháng</p>
                    <p>Bên thuê thanh toán tiền thuê vào ngày 5 hàng tháng.</p>

                    <p><strong>Điều 4: Quyền và nghĩa vụ các bên</strong></p>
                    <p>- Bên cho thuê có trách nhiệm bàn giao nhà đúng hiện trạng</p>
                    <p>- Bên thuê có trách nhiệm giữ gìn tài sản, thanh toán tiền thuê đúng hạn</p>

                    <br>
                    <p><strong>Chữ ký các bên:</strong></p>
                    <table style="width: 100%; border: none;">
                        <tr>
                            <td style="text-align: center; width: 50%; border: none;">
                                <strong>BÊN CHO THUÊ</strong><br>
                                (Ký và ghi rõ họ tên)
                            </td>
                            <td style="text-align: center; width: 50%; border: none;">
                                <strong>BÊN THUÊ</strong><br>
                                (Ký và ghi rõ họ tên)
                            </td>
                        </tr>
                    </table>
                `;

                if (editor) {
                    editor.setContent(content);
                    updateSaveStatus('Đã tải nội dung template');
                }
            } catch (error) {
                console.error('Error loading template content:', error);
                updateSaveStatus('Lỗi khi tải nội dung');
            }
        }

        // Save template function
        async function saveTemplate() {
            if (!editor) {
                alert('Editor chưa được khởi tạo');
                return;
            }

            const content = editor.getContent();
            const saveBtn = document.querySelector('.save-btn');
            const originalText = saveBtn.innerHTML;

            try {
                // Show loading
                saveBtn.disabled = true;
                saveBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Đang lưu...';
                updateSaveStatus('Đang lưu...');

                const response = await fetch('/agent/template/save', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        filename: filename,
                        content: content
                    })
                });

                const result = await response.json();

                if (result.success) {
                    updateSaveStatus('Đã lưu thành công lúc ' + new Date().toLocaleTimeString());

                    // Notify parent window if available
                    if (window.parent && window.parent.transactionModal) {
                        window.parent.transactionModal.showSuccess('Template đã được lưu thành công!');
                    }
                } else {
                    updateSaveStatus('Lỗi khi lưu: ' + result.message);
                    alert('Lỗi khi lưu template: ' + result.message);
                }

            } catch (error) {
                console.error('Error saving template:', error);
                updateSaveStatus('Lỗi kết nối');
                alert('Lỗi kết nối khi lưu template');
            } finally {
                // Restore button
                saveBtn.disabled = false;
                saveBtn.innerHTML = originalText;
            }
        }

        // Update save status
        function updateSaveStatus(message) {
            const statusElement = document.getElementById('saveStatus');
            if (statusElement) {
                statusElement.textContent = message;
            }
        }

        // Auto-save every 30 seconds
        function startAutoSave() {
            if (autoSaveInterval) {
                clearInterval(autoSaveInterval);
            }

            autoSaveInterval = setInterval(async () => {
                if (editor && editor.isDirty()) {
                    await saveTemplate();
                    editor.setDirty(false);
                }
            }, 30000); // 30 seconds
        }

        // Initialize when TinyMCE is ready
        document.addEventListener('DOMContentLoaded', function() {
            // Start auto-save after editor is initialized
            setTimeout(() => {
                loadTemplateContent();
                startAutoSave();
            }, 2000);
        });

        // Cleanup on window close
        window.addEventListener('beforeunload', function() {
            if (autoSaveInterval) {
                clearInterval(autoSaveInterval);
            }
        });

        // Keyboard shortcuts
        document.addEventListener('keydown', function(e) {
            if (e.ctrlKey && e.key === 's') {
                e.preventDefault();
                saveTemplate();
            }
        });
    </script>
</body>
</html>
