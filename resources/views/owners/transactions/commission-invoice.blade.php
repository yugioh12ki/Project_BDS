<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hóa đơn hoa hồng - {{ $commission->CommissionID }}</title>
    <style>
        body {
            font-family: 'Times New Roman', serif;
            margin: 0;
            padding: 20px;
            background: white;
        }
        .invoice-container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            padding: 30px;
            border: 1px solid #ddd;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #333;
            padding-bottom: 20px;
        }
        .company-name {
            font-size: 24px;
            font-weight: bold;
            color: #2c3e50;
            margin-bottom: 5px;
        }
        .invoice-title {
            font-size: 20px;
            color: #e74c3c;
            font-weight: bold;
            margin-top: 10px;
        }
        .invoice-info {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
        }
        .info-section {
            width: 48%;
        }
        .info-title {
            font-weight: bold;
            font-size: 14px;
            color: #2c3e50;
            margin-bottom: 10px;
            text-transform: uppercase;
        }
        .info-content {
            font-size: 13px;
            line-height: 1.6;
        }
        .transaction-details {
            margin: 30px 0;
        }
        .details-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .details-table th,
        .details-table td {
            border: 1px solid #ddd;
            padding: 12px;
            text-align: left;
        }
        .details-table th {
            background-color: #f8f9fa;
            font-weight: bold;
            color: #2c3e50;
        }
        .amount-section {
            margin-top: 30px;
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 5px;
        }
        .amount-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
            font-size: 14px;
        }
        .total-amount {
            font-size: 18px;
            font-weight: bold;
            color: #e74c3c;
            border-top: 2px solid #ddd;
            padding-top: 10px;
            margin-top: 10px;
        }
        .signature-section {
            margin-top: 50px;
            display: flex;
            justify-content: space-between;
        }
        .signature-box {
            width: 45%;
            text-align: center;
        }
        .signature-title {
            font-weight: bold;
            margin-bottom: 60px;
            text-transform: uppercase;
        }
        .signature-line {
            border-top: 1px solid #333;
            margin-top: 50px;
            padding-top: 5px;
            font-style: italic;
        }
        .footer {
            margin-top: 40px;
            text-align: center;
            font-size: 12px;
            color: #7f8c8d;
            border-top: 1px solid #ddd;
            padding-top: 20px;
        }
        @media print {
            body { margin: 0; }
            .invoice-container {
                border: none;
                padding: 0;
                box-shadow: none;
            }
        }
    </style>
</head>
<body>
    <div class="invoice-container">
        <!-- Header -->
        <div class="header">
            <div class="company-name">HỆ THỐNG QUẢN LÝ BẤT ĐỘNG SẢN</div>
            <div>Địa chỉ: 123 Đường ABC, Quận XYZ, TP. Hồ Chí Minh</div>
            <div>Điện thoại: (028) 1234 5678 | Email: info@batdongsan.com</div>
            <div class="invoice-title">HÓA ĐƠN HOA HỒNG</div>
            <div>Số: {{ $commission->CommissionID }} | Ngày: {{ \Carbon\Carbon::parse($commission->PaidDate)->format('d/m/Y') }}</div>
        </div>

        <!-- Invoice Information -->
        <div class="invoice-info">
            <div class="info-section">
                <div class="info-title">Thông tin chủ sở hữu</div>
                <div class="info-content">
                    <strong>Họ tên:</strong> {{ $commission->owner_name }}<br>
                    <strong>Điện thoại:</strong> {{ $commission->owner_phone }}<br>
                    <strong>Địa chỉ:</strong> {{ $commission->owner_address }}
                </div>
            </div>
            <div class="info-section">
                <div class="info-title">Thông tin môi giới</div>
                <div class="info-content">
                    <strong>Họ tên:</strong> {{ $commission->agent_name }}<br>
                    <strong>Điện thoại:</strong> {{ $commission->agent_phone }}<br>
                    <strong>Địa chỉ:</strong> {{ $commission->agent_address }}
                </div>
            </div>
        </div>

        <!-- Transaction Details -->
        <div class="transaction-details">
            <div class="info-title">Chi tiết giao dịch</div>
            <table class="details-table">
                <tr>
                    <th>Mã giao dịch</th>
                    <td>{{ $commission->TransactionID }}</td>
                </tr>
                <tr>
                    <th>Ngày giao dịch</th>
                    <td>{{ \Carbon\Carbon::parse($commission->TransactionDate)->format('d/m/Y') }}</td>
                </tr>
                <tr>
                    <th>Bất động sản</th>
                    <td>{{ $commission->property_title }}</td>
                </tr>
                <tr>
                    <th>Địa chỉ BĐS</th>
                    <td>{{ $commission->property_address }}</td>
                </tr>
                <tr>
                    <th>Loại giao dịch</th>
                    <td>
                        <span class="badge">
                            {{ $commission->TransactionType === 'Sale' ? 'Bán' : 'Cho thuê' }}
                        </span>
                    </td>
                </tr>
                <tr>
                    <th>Giá trị giao dịch</th>
                    <td><strong>{{ number_format($commission->TotalPrice) }} VND</strong></td>
                </tr>
                <tr>
                    <th>Tỷ lệ hoa hồng</th>
                    <td>{{ number_format($commission->Percentage * 100, 1) }}%</td>
                </tr>
            </table>
        </div>

        <!-- Amount Section -->
        <div class="amount-section">
            <div class="amount-row">
                <span>Tỷ lệ hoa hồng:</span>
                <span>{{ number_format($commission->Percentage * 100, 1) }}%</span>
            </div>
            <div class="amount-row">
                <span>Giá trị giao dịch:</span>
                <span>{{ number_format($commission->TotalPrice) }} VND</span>
            </div>
            <div class="amount-row total-amount">
                <span><strong>TỔNG HOA HỒNG:</strong></span>
                <span><strong>{{ number_format($commission->Amount) }} VND</strong></span>
            </div>
            <div style="margin-top: 15px; font-style: italic; font-size: 13px;">
                Bằng chữ: <strong>{{ $this->numberToWords($commission->Amount) }} đồng</strong>
            </div>
        </div>

        <!-- Payment Status -->
        <div style="margin: 20px 0; padding: 15px; background-color: #e8f5e8; border-left: 4px solid #28a745;">
            <strong>Trạng thái thanh toán:</strong>
            @if($commission->StatusCommission === 'Success')
                <span style="color: #28a745;">✓ Đã thanh toán</span>
                <br><strong>Ngày thanh toán:</strong> {{ \Carbon\Carbon::parse($commission->PaidDate)->format('d/m/Y') }}
            @else
                <span style="color: #dc3545;">⏳ Chờ thanh toán</span>
            @endif
        </div>

        <!-- Signatures -->
        <div class="signature-section">
            <div class="signature-box">
                <div class="signature-title">Chủ sở hữu</div>
                <div class="signature-line">{{ $commission->owner_name }}</div>
            </div>
            <div class="signature-box">
                <div class="signature-title">Môi giới</div>
                <div class="signature-line">{{ $commission->agent_name }}</div>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p>Hóa đơn này được tạo tự động bởi hệ thống quản lý bất động sản</p>
            <p>Cảm ơn quý khách đã sử dụng dịch vụ của chúng tôi!</p>
        </div>
    </div>

    <script>
        // Auto print when page loads (optional)
        // window.onload = function() { window.print(); }
    </script>
</body>
</html>

@php
function numberToWords($number) {
    $units = ['', 'một', 'hai', 'ba', 'bốn', 'năm', 'sáu', 'bảy', 'tám', 'chín'];
    $tens = ['', '', 'hai mươi', 'ba mươi', 'bốn mươi', 'năm mươi', 'sáu mươi', 'bảy mươi', 'tám mươi', 'chín mươi'];

    if ($number == 0) return 'không';
    if ($number < 10) return $units[$number];
    if ($number < 100) {
        $ten = intval($number / 10);
        $unit = $number % 10;
        if ($ten == 1) {
            return 'mười' . ($unit > 0 ? ' ' . $units[$unit] : '');
        }
        return $tens[$ten] . ($unit > 0 ? ' ' . $units[$unit] : '');
    }

    // Simplified version for demo - in production, implement full number to words conversion
    return number_format($number, 0, ',', '.');
}
@endphp
