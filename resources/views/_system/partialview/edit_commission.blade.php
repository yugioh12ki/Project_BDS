<div class="container">
    <h1 class="mb-4">Chi tiết Phiếu Hoa Hồng</h1>

    <form action="{{ route('admin.commission.update', $commission->CommissionID) }}" method="POST">
        @csrf
        @method('PUT')

        <table class="table table-bordered">
            <tbody>
                <tr>
                    <th>Mã Hoa Hồng</th>
                    <td>{{ $commission->CommissionID }}</td>
                </tr>
                <tr>
                    <th>Mã Giao Dịch</th>
                    <td>{{ $commission->TransactionID }}</td>
                </tr>
                <tr>
                    <th>Mã Môi Giới</th>
                    <td>{{ $commission->AgentID }}</td>
                </tr>
                <tr>
                    <th>Số Tiền</th>
                    <td>{{ number_format($commission->Amount, 0, ',', '.') }} VND</td>
                </tr>
                <tr>
                    <th>Thời Hạn Thuê (tháng)</th>
                    <td>{{ $commission->RentMonth ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <th>Phần Trăm</th>
                    <td>{{ $commission->Percentage ?? 'N/A' }}%</td>
                </tr>
                <tr>
                    <th>Loại Hoa Hồng</th>
                    <td>{{ $commission->TypeCom }}</td>
                </tr>
                <tr>
                    <th>Trạng Thái</th>
                    <td>
                        <select name="StatusCommission" class="form-select">
                            <option value="Pending" {{ $commission->StatusCommission == 'Pending' ? 'selected' : '' }}>Đang chờ
                            </option>
                            <option value="Success" {{ $commission->StatusCommission == 'Success' ? 'selected' : '' }}>Thành công
                            </option>
                            <option value="Cancel" {{ $commission->StatusCommission == 'Cancel' ? 'selected' : '' }}>Đã hủy
                            </option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <th>Ngày Thanh Toán</th>
                    <td>{{ $commission->PaidDate ? date('d/m/Y', strtotime($commission->PaidDate)) : 'Chưa thanh toán' }}</td>
                </tr>
            </tbody>
        </table>

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <button type="submit" class="btn btn-primary mt-2">Lưu thông tin</button>
    </form>
</div>
