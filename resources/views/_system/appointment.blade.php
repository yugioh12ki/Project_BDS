@extends('_layout._layadmin.app')
@section('appointment')
@if(session('success'))
    <div class="alert alert-success">
        <i class="fa fa-check-circle"></i> {{ session('success') }}
    </div>
@endif
@if(session('error'))
    <div class="alert alert-danger">
        <i class="fa fa-exclamation-circle"></i> {{ session('error') }}
    </div>
@endif
@if(isset($error))
    <div class="alert alert-danger">
        <i class="fa fa-exclamation-circle"></i> {{ $error }}
    </div>
@else
<h1>Danh sách Lịch hẹn</h1>
<div class="action-buttons">
    <form action="{{ route('admin.appointment.search.date') }}" method="GET" class="search-form mb-3">
        <div class="input-group" style="max-width: 400px;">
            <input type="date" name="date" class="form-control" placeholder="Chọn ngày" required>
            <button type="submit" class="btn btn-primary btn-sm">
                <i class="fas fa-search"></i> Tìm
            </button>
        </div>
    </form>
</div>
<div class="table-container" style="margin-top: 50px;">
    <table>
        <thead>
            <tr>
                @foreach ($columns as $column )
                @if( $column !== 'OwnerID' && $column !== 'CusID' && $column !== 'TitleAppoint' && $column !== 'DescAppoint' )
                        @if($column === 'AppointmentID')
                        <th>ID</th>
                        @elseif($column === 'PopertyID')
                        <th>Bất Động sản ID</th>
                        @elseif($column === 'AgentID')
                        <th>Người tạo lịch hẹn</th>
                        @elseif($column === 'AppointmentDateStart')
                        <th>Ngày hẹn bắt đầu</th>
                        @elseif($column === 'AppointmentDateEnd')
                        <th>Ngày hẹn kết thúc</th>
                        @else
                        <th>{{ $column }}</th>
                        @endif

                @endif
                @endforeach
                <th>Chức năng</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($appointments as $appointment)
                <tr>
                    @foreach ($columns as $column)

                    @if( $column !== 'OwnerID' && $column !== 'CusID'
                        && $column !== 'TitleAppoint' && $column !== 'AgentID' && $column !== 'DescAppoint' )
                            <td>{{ $appointment->$column }}</td>
                    @endif
                    @if($column === 'AgentID')
                            <td>{{ $appointment->user_agent->Name ?? 'NOT FOUND 404' }}</td>
                            @endif
                    @endforeach
                    <td>
                        <button
                            type="button"
                            class="btn btn-primary btn-sm"
                            data-bs-toggle="modal"
                            data-bs-target="#viewAppointmentModal{{ $appointment->AppointmentID }}">
                            <i class="fas fa-eye"></i> Xem
                        </button>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Đặt các modal ở đây, ngoài bảng -->
    @foreach ($appointments as $appointment)
    <div class="modal fade" id="viewAppointmentModal{{ $appointment->AppointmentID }}" tabindex="-1" role="dialog" aria-labelledby="viewAppointmentModalLabel{{ $appointment->AppointmentID }}" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="viewAppointmentModalLabel{{ $appointment->AppointmentID }}">Chi tiết cuộc hẹn</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    @include('_system.partialview.checkedit_appoint', ['appointment' => $appointment])
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#deleteModal{{ $appointment->AppointmentID }}">
                        <i class="fa fa-trash"></i> Xóa
                    </button>
                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Đóng</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Xác nhận xóa -->
    <div class="modal fade" id="deleteModal{{ $appointment->AppointmentID }}" tabindex="-1" aria-labelledby="deleteModalLabel{{ $appointment->AppointmentID }}" aria-hidden="true">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header border-0">
                    <h5 class="modal-title" id="deleteModalLabel{{ $appointment->AppointmentID }}">Xác nhận xóa</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center py-4">
                    Bạn có chắc chắn muốn xóa cuộc hẹn này?
                </div>
                <div class="modal-footer justify-content-center border-0">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <form action="{{ route('admin.appointment.delete', $appointment->AppointmentID) }}" method="POST" style="display: inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">Xóa</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @endforeach
</div>

@endif
@endsection

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Xử lý khi modal xác nhận xóa được mở
    document.querySelectorAll('[data-bs-target^="#deleteModal"]').forEach(function(button) {
        button.addEventListener('click', function() {
            // Khi nhấp vào nút xóa, đóng modal chi tiết để modal xác nhận hiển thị đúng
            const viewModalId = this.closest('.modal').id;
            bootstrap.Modal.getInstance(document.getElementById(viewModalId)).hide();
        });
    });
});
</script>
