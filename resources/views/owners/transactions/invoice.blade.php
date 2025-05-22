<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hóa đơn giao dịch #{{ $transaction->TransactionID }}</title>
    <link rel="stylesheet" href="{{ asset('css/invoice.css') }}">
</head>
<body>
    <div class="invoice-container">
        <div class="header clearfix">
            <div class="company-info">
                <h2>BDS Company</h2>
                <p>
                    123 Đường Nguyễn Huệ<br>
                    Quận 1, TP Hồ Chí Minh<br>
                    SĐT: (84) 28 1234 5678<br>
                    Email: contact@bdscompany.com
                </p>
            </div>
            <div class="invoice-info">
                <h1>HÓA ĐƠN</h1>
                <p>
                    <strong>Mã hóa đơn:</strong> INV-{{ $transaction->TransactionID }}<br>
                    <strong>Ngày lập:</strong> {{ \Carbon\Carbon::parse($transaction->TransactionDate)->format('d/m/Y') }}<br>
                    <strong>Trạng thái:</strong> {{ $transaction->Status }}
                </p>
            </div>
        </div>

        <div class="client-info clearfix">
            <div class="client-left">
                <h3>Thông tin khách hàng</h3>
                @if($transaction->trans_cus)
                <p>
                    <strong>{{ $transaction->trans_cus->FullName }}</strong><br>
                    SĐT: {{ $transaction->trans_cus->PhoneNumber }}<br>
                    Email: {{ $transaction->trans_cus->Email }}
                </p>
                @else
                <p>Không có thông tin khách hàng</p>
                @endif
            </div>
            <div class="client-right">
                <h3>Thông tin chủ sở hữu</h3>
                @if($transaction->trans_owner)
                <p>
                    <strong>{{ $transaction->trans_owner->FullName }}</strong><br>
                    SĐT: {{ $transaction->trans_owner->PhoneNumber }}<br>
                    Email: {{ $transaction->trans_owner->Email }}
                </p>
                @else
                <p>Không có thông tin chủ sở hữu</p>
                @endif
            </div>
        </div>

        <h3>Chi tiết giao dịch</h3>
        <table class="table">
            <thead>
                <tr>
                    <th>Bất động sản</th>
                    <th>Loại giao dịch</th>
                    <th>Diện tích</th>
                    <th>Đơn giá</th>
                    <th>Thành tiền</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        @if($transaction->property)
                        {{ $transaction->property->Title }} <br>
                        <small>{{ $transaction->property->Address }}, {{ $transaction->property->Ward }}, {{ $transaction->property->District }}, {{ $transaction->property->Province }}</small>
                        @else
                        Không có thông tin
                        @endif
                    </td>
                    <td>{{ $transaction->TransactionType }}</td>
                    <td>
                        @if($transaction->property && $transaction->property->chiTiet)
                        {{ $transaction->property->chiTiet->Area }} m²
                        @else
                        N/A
                        @endif
                    </td>
                    <td>
                        @if($transaction->property && $transaction->property->chiTiet && $transaction->property->chiTiet->Area > 0)
                        {{ number_format($transaction->Amount / $transaction->property->chiTiet->Area, 0) }} VND/m²
                        @else
                        N/A
                        @endif
                    </td>
                    <td>{{ number_format($transaction->Amount, 0) }} VND</td>
                </tr>
            </tbody>
        </table>

        <div class="totals">
            <table>
                <tr>
                    <td>Giá trị giao dịch:</td>
                    <td>{{ number_format($transaction->Amount, 0) }} VND</td>
                </tr>
                <tr>
                    <td>Hoa hồng môi giới ({{ $transaction->Commission ? round(($transaction->Commission / $transaction->Amount) * 100, 2) : 0 }}%):</td>
                    <td>{{ number_format($transaction->Commission ?? 0, 0) }} VND</td>
                </tr>
                <tr class="total">
                    <td>Tổng thanh toán:</td>
                    <td>{{ number_format($transaction->Amount, 0) }} VND</td>
                </tr>
            </table>
        </div>

        <div class="clearfix"></div>

        @if($transaction->Description)
        <div class="notes">
            <h3>Ghi chú</h3>
            <p>{{ $transaction->Description }}</p>
        </div>
        @endif

        <div class="footer">
            <p>Mọi thắc mắc về giao dịch, vui lòng liên hệ qua email: support@bdscompany.com hoặc số điện thoại: (84) 28 1234 5678</p>
            <p>Cảm ơn quý khách đã sử dụng dịch vụ của chúng tôi!</p>
        </div>
    </div>
</body>
</html> 