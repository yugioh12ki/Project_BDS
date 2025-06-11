<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Xem trước Template</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f8f9fa;
            margin: 0;
            padding: 20px;
        }

        .preview-container {
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            padding: 30px;
            max-width: 800px;
            margin: 0 auto;
        }

        .preview-header {
            border-bottom: 2px solid #dee2e6;
            padding-bottom: 15px;
            margin-bottom: 30px;
            text-align: center;
        }

        .document-content {
            line-height: 1.6;
            font-size: 14px;
            color: #333;
        }

        .document-content h2 {
            color: #2c3e50;
            text-align: center;
            margin-bottom: 30px;
            font-weight: bold;
            text-transform: uppercase;
        }

        .document-content h3 {
            color: #34495e;
            margin-top: 25px;
            margin-bottom: 15px;
            font-weight: 600;
        }

        .document-content p {
            margin-bottom: 12px;
            text-align: justify;
        }

        .document-content table {
            width: 100%;
            margin-top: 30px;
        }

        .document-content table td {
            padding: 20px;
            text-align: center;
            vertical-align: top;
        }

        .refresh-btn {
            position: fixed;
            bottom: 20px;
            right: 20px;
            background: linear-gradient(135deg, #007bff, #0056b3);
            border: none;
            color: white;
            padding: 12px 16px;
            border-radius: 50px;
            box-shadow: 0 4px 12px rgba(0, 123, 255, 0.3);
            transition: all 0.3s ease;
        }

        .refresh-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(0, 123, 255, 0.4);
        }

        .variable-highlight {
            background-color: #fff3cd;
            padding: 2px 4px;
            border-radius: 3px;
            border: 1px solid #ffeaa7;
            font-weight: 500;
        }

        .status-bar {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            background: #28a745;
            color: white;
            padding: 8px;
            text-align: center;
            font-size: 12px;
            z-index: 1000;
        }

        .preview-info {
            background: #e3f2fd;
            border: 1px solid #bbdefb;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 20px;
            font-size: 12px;
        }
    </style>
</head>
<body>
    <div class="status-bar">
        <i class="fas fa-eye me-1"></i>
        Chế độ xem trước - Tự động cập nhật khi template được chỉnh sửa
    </div>

    <div class="preview-container">
        <div class="preview-header">
            <h5 class="mb-1">
                <i class="fas fa-file-alt text-primary me-2"></i>
                Xem trước Template
            </h5>
            <small class="text-muted">{{ $filename }}</small>
        </div>

        <div class="preview-info">
            <div class="row">
                <div class="col-md-8">
                    <strong><i class="fas fa-info-circle me-1"></i>Thông tin:</strong>
                    Đây là bản xem trước của template. Các biến sẽ được thay thế bằng dữ liệu thực tế khi tạo giao dịch.
                </div>
                <div class="col-md-4 text-end">
                    <small class="text-muted">Cập nhật lần cuối: <span id="lastUpdate">{{ now()->format('H:i:s') }}</span></small>
                </div>
            </div>
        </div>

        <div class="document-content" id="documentContent">
            <!-- Template content will be loaded here -->
            <div class="text-center py-4">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Đang tải...</span>
                </div>
                <p class="mt-2 text-muted">Đang tải nội dung template...</p>
            </div>
        </div>
    </div>

    <!-- Refresh button -->
    <button type="button" class="btn refresh-btn" onclick="refreshPreview()" title="Làm mới">
        <i class="fas fa-sync-alt"></i>
    </button>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        const filename = '{{ $filename }}';
        let refreshInterval = null;

        // Sample data for variable replacement
        const sampleData = {
            '{customer_name}': 'Nguyễn Văn A',
            '{property_address}': '123 Đường ABC, Quận 1, TP.HCM',
            '{price}': '15.000.000',
            '{rent_months}': '12',
            '{start_date}': '15/06/2025',
            '{end_date}': '15/06/2026',
            '{customer_phone}': '0901234567',
            '{customer_email}': 'nguyenvana@email.com',
            '{customer_id}': '123456789',
            '{deposit_amount}': '30.000.000',
            '{agent_name}': 'Trần Thị B',
            '{company_name}': 'Công ty Bất động sản XYZ'
        };

        // Load and display template content
        async function loadTemplateContent() {
            try {
                const contentDiv = document.getElementById('documentContent');

                // Sample template content for preview
                const templateContent = `
                    <h2>HỢP ĐỒNG THUÊ NHÀ</h2>

                    <p><strong>Bên cho thuê:</strong> Công ty Bất động sản XYZ</p>
                    <p><strong>Bên thuê:</strong> <span class="variable-highlight">{customer_name}</span></p>
                    <p><strong>Số điện thoại:</strong> <span class="variable-highlight">{customer_phone}</span></p>
                    <p><strong>Email:</strong> <span class="variable-highlight">{customer_email}</span></p>
                    <p><strong>Địa chỉ bất động sản:</strong> <span class="variable-highlight">{property_address}</span></p>
                    <p><strong>Giá thuê:</strong> <span class="variable-highlight">{price}</span> VNĐ/tháng</p>

                    <h3>ĐIỀU KHOẢN HỢP ĐỒNG</h3>

                    <p><strong>Điều 1: Đối tượng cho thuê</strong></p>
                    <p>Bên cho thuê đồng ý cho bên thuê thuê căn nhà tại địa chỉ: <span class="variable-highlight">{property_address}</span></p>

                    <p><strong>Điều 2: Thời hạn thuê</strong></p>
                    <p>Thời hạn thuê nhà là <span class="variable-highlight">{rent_months}</span> tháng, kể từ ngày <span class="variable-highlight">{start_date}</span> đến ngày <span class="variable-highlight">{end_date}</span></p>

                    <p><strong>Điều 3: Giá thuê và phương thức thanh toán</strong></p>
                    <p>Giá thuê nhà: <span class="variable-highlight">{price}</span> VNĐ/tháng</p>
                    <p>Tiền đặt cọc: <span class="variable-highlight">{deposit_amount}</span> VNĐ</p>
                    <p>Bên thuê thanh toán tiền thuê vào ngày 5 hàng tháng.</p>

                    <p><strong>Điều 4: Quyền và nghĩa vụ các bên</strong></p>
                    <p><strong>4.1. Quyền và nghĩa vụ của bên cho thuê:</strong></p>
                    <ul>
                        <li>Bàn giao nhà đúng hiện trạng như đã thỏa thuận</li>
                        <li>Đảm bảo quyền sử dụng hợp pháp của bên thuê</li>
                        <li>Không được tăng giá thuê trong thời hạn hợp đồng</li>
                    </ul>

                    <p><strong>4.2. Quyền và nghĩa vụ của bên thuê:</strong></p>
                    <ul>
                        <li>Sử dụng nhà đúng mục đích đã thỏa thuận</li>
                        <li>Thanh toán tiền thuê đầy đủ và đúng hạn</li>
                        <li>Giữ gìn, bảo quản tài sản như của chính mình</li>
                        <li>Không được chuyển nhượng, cho thuê lại khi chưa có sự đồng ý của bên cho thuê</li>
                    </ul>

                    <p><strong>Điều 5: Điều khoản chung</strong></p>
                    <p>Hợp đồng này có hiệu lực kể từ ngày ký. Mọi tranh chấp phát sinh sẽ được giải quyết thông qua thương lượng hoặc cơ quan có thẩm quyền.</p>

                    <br>
                    <p><strong>Hợp đồng được lập thành 02 bản có giá trị pháp lý như nhau, mỗi bên giữ 01 bản.</strong></p>

                    <table style="width: 100%; border: none; margin-top: 40px;">
                        <tr>
                            <td style="text-align: center; width: 50%; border: none;">
                                <strong>BÊN CHO THUÊ</strong><br>
                                <i>(Ký và ghi rõ họ tên)</i><br><br><br>
                                <span class="variable-highlight">{agent_name}</span>
                            </td>
                            <td style="text-align: center; width: 50%; border: none;">
                                <strong>BÊN THUÊ</strong><br>
                                <i>(Ký và ghi rõ họ tên)</i><br><br><br>
                                <span class="variable-highlight">{customer_name}</span>
                            </td>
                        </tr>
                    </table>
                `;

                // Replace variables with sample data for preview
                let processedContent = templateContent;
                Object.keys(sampleData).forEach(variable => {
                    const regex = new RegExp(escapeRegExp(variable), 'g');
                    processedContent = processedContent.replace(regex, `<span class="variable-highlight">${sampleData[variable]}</span>`);
                });

                contentDiv.innerHTML = processedContent;
                updateLastUpdateTime();

            } catch (error) {
                console.error('Error loading template content:', error);
                document.getElementById('documentContent').innerHTML = `
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        Lỗi khi tải nội dung template: ${error.message}
                    </div>
                `;
            }
        }

        // Escape special regex characters
        function escapeRegExp(string) {
            return string.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
        }

        // Refresh preview
        function refreshPreview() {
            const refreshBtn = document.querySelector('.refresh-btn');
            const originalIcon = refreshBtn.innerHTML;

            refreshBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
            refreshBtn.disabled = true;

            setTimeout(() => {
                loadTemplateContent();
                refreshBtn.innerHTML = originalIcon;
                refreshBtn.disabled = false;
            }, 1000);
        }

        // Update last update time
        function updateLastUpdateTime() {
            const lastUpdateElement = document.getElementById('lastUpdate');
            if (lastUpdateElement) {
                const now = new Date();
                lastUpdateElement.textContent = now.toLocaleTimeString('vi-VN');
            }
        }

        // Auto-refresh every 5 seconds to sync with editor changes
        function startAutoRefresh() {
            refreshInterval = setInterval(() => {
                loadTemplateContent();
            }, 5000);
        }

        // Initialize
        document.addEventListener('DOMContentLoaded', function() {
            loadTemplateContent();
            startAutoRefresh();
        });

        // Cleanup on window close
        window.addEventListener('beforeunload', function() {
            if (refreshInterval) {
                clearInterval(refreshInterval);
            }
        });

        // Listen for messages from parent window
        window.addEventListener('message', function(event) {
            if (event.data && event.data.action === 'refresh-preview') {
                refreshPreview();
            }
        });
    </script>
</body>
</html>
