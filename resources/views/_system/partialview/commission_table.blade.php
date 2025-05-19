@if(isset($error))
    <div class="alert alert-danger">
        {{ $error }}
    </div>
@else
<div class="table-responsive">
    <table >
        <thead >
            <tr>
                <th>Mã HH</th>
                <th>Số tiền</th>
                <th>Trạng thái</th>
                <th>Ngày thanh toán</th>
                <th class="text-center">Chức năng</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($commissions as $commission)
                <tr>
                    <td>{{ $commission->CommissionID }}</td>
                    <td class="">{{ number_format($commission->Amount, 0, ',', '.') }} VND</td>
                    <td class="text">
                        @if($commission->StatusCommission == 'Success')
                            <span class="badge bg-success">Thành công</span>
                        @elseif($commission->StatusCommission == 'Pending')
                            <span class="badge bg-primary">Đang chờ</span>
                        @elseif($commission->StatusCommission == 'Cancelled')
                            <span class="badge bg-danger">Đã hủy</span>
                        @else
                            <span class="badge bg-secondary">{{ $commission->StatusCommission }}</span>
                        @endif
                    </td>
                    <td>{{ $commission->PaidDate ? date('d/m/Y', strtotime($commission->PaidDate)) : 'Chưa thanh toán' }}</td>
                    <td class="text-center">
                        <div class="btn-group btn-group-sm">
                            <button type="button" class="btn btn-info view-commission" data-id="{{ $commission->CommissionID }}" data-bs-toggle="modal" data-bs-target="#viewModal">
                                <i class="fas fa-eye"></i> Xem
                            </button>
                            <form id="delete-commission-form" action="{{ route('admin.commission.delete', $commission->CommissionID ?? '') }}" method="POST" style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger" onclick="return confirm('Bạn có chắc muốn xóa hoa hồng này?');">
                                    <i class="fas fa-trash"></i> Xóa
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

<!-- Xem chi tiết Modal -->
<div class="modal fade" id="viewModal" tabindex="-1" aria-labelledby="viewModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="viewModalLabel">Chi tiết hoa hồng</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                @include('_system.partialview.edit_commission', ['commission' => $commission])
            </div>
            <div class="modal-footer">

                <form id="delete-commission-form" action="{{ route('admin.commission.delete', $commission->CommissionID ?? '') }}" method="POST" style="display:inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger" onclick="return confirm('Bạn có chắc muốn xóa hoa hồng này?');">
                        <i class="fas fa-trash"></i> Xóa
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>




@endif
