@extends('_layout._layadmin.app')
@section('appointment')
@if(isset($error))
    <div class="fa fa-danger">
        {{ $error }}
    </div>
@else
<h1>Danh sách Lịch hẹn</h1>
<div class="action-buttons">
</div>
<div class="table-container" style="margin-top: 50px;">
    <table>
        <thead>
            <tr>
                @foreach ($columns as $column )
                @if( $column !== 'OwnerID' && $column !== 'CusID' && $column !== 'TitleAppoint' )

                        @if($column === 'AgentID')
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
                        && $column !== 'TitleAppoint' && $column !== 'AgentID' )
                            <td>{{ $appointment->$column }}</td>
                    @endif
                    @if($column === 'AgentID')
                            <td>{{ $appointment->user_agent->Name ?? 'NOT FOUND 404' }}</td>
                            @endif
                    @endforeach
                    <td>
                        <button
                        type="button"
                        class="btn btn-primary btn-sm btn-view-appointment"
                        data-appointment='@json($appointment)'
                        data-bs-toggle="modal"
                        data-bs-target="#viewAppointmentModal">
                    <i class="fas fa-eye"></i> Xem
                </button>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
<!-- Modal chỉ đặt 1 lần ngoài foreach -->
<div class="modal fade" id="viewAppointmentModal" tabindex="-1" role="dialog" aria-labelledby="viewAppointmentModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="viewAppointmentModalLabel">Chi tiết lịch hẹn</h5>
        <button type="button" class="btn-close ms-auto" data-bs-dismiss="modal" aria-label="Đóng" style="position: absolute; right: 1rem; top: 1rem;"></button>
      </div>
      <div class="modal-body">
        <table class="table table-bordered">
          <tbody id="appointment-detail-body">
            @include('_system.partialview.checkedit_appoint', ['appointment' => null])
          </tbody>
        </table>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Đóng</button>
      </div>
    </div>
  </div>
</div>
@endif
@endsection

<script>
document.addEventListener('DOMContentLoaded', function() {
    $('.btn-view-appointment').on('click', function() {
        let appointment = $(this).data('appointment');
        let html = '';
        for (let key in appointment) {
            if(['OwnerID','CusID','TitleAppoint','created_at','updated_at'].includes(key)) continue;
            html += `<tr><th>${key}</th><td>${appointment[key]}</td></tr>`;
        }
        $('#appointment-detail-body').html(html);
    });
});
</script>
