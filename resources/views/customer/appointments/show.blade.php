@extends('_layout._layhome.home')

@section('home')
<div class="container">
    <div class="appointment-container">
        <h2>Danh sách lịch hẹn</h2>
        
        @if($appointments->count() > 0)
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Mã lịch hẹn</th>
                            <th>Bất động sản</th>
                            <th>Ngày hẹn</th>
                            <th>Thời gian</th>
                            <th>Môi giới</th>
                            <th>Trạng thái</th>
                            <th>Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($appointments as $apt)
                        <tr>
                            <td>{{ $apt->AppointmentID }}</td>
                            <td>{{ $apt->property->Title }}</td>
                            <td>{{ \Carbon\Carbon::parse($apt->AppointmentDateStart)->format('d/m/Y') }}</td>
                            <td>{{ \Carbon\Carbon::parse($apt->AppointmentDateEnd)->format('d/m/Y') }}</td>
                            <td>{{ $apt->agent->Name ?? 'Chưa có' }}</td>
                            <td>
                                <span class="badge {{ 
                                    $apt->Status == 'pending' ? 'bg-warning' : 
                                    ($apt->Status == 'confirmed' ? 'bg-success' : 'bg-danger') 
                                }}">
                                    {{ $apt->Status }}
                                </span>
                            </td>
                            <td>
                                <button class="btn btn-info btn-sm view-details" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#appointmentModal-{{ $apt->AppointmentID }}">
                                    Xem chi tiết
                                </button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @foreach($appointments as $apt)
            <!-- Modal for each appointment -->
            <div class="modal fade" id="appointmentModal-{{ $apt->AppointmentID }}" tabindex="-1">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Chi tiết lịch hẹn #{{ $apt->AppointmentID }}</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <div class="appointment-details">
                                <p><strong>Bất động sản:</strong> {{ $apt->property->Title }}</p>
                                <p><strong>Địa chỉ:</strong> {{ $apt->property->Address }}</p>
                                <p><strong>Ngày hẹn:</strong> {{ \Carbon\Carbon::parse($apt->AppointmentDateStart)->format('d/m/Y') }}</p>
                                <p><strong>Thời gian:</strong> {{ $apt->AppointmentTime }}</p>
                                <p><strong>Môi giới:</strong> {{ $apt->agent->Name ?? 'Chưa có' }}</p>
                                <p><strong>Ghi chú:</strong> {{ $apt->Notes }}</p>
                                <p><strong>Trạng thái:</strong> 
                                    <span class="badge {{ 
                                        $apt->Status == 'pending' ? 'bg-warning' : 
                                        ($apt->Status == 'confirmed' ? 'bg-success' : 'bg-danger') 
                                    }}">
                                        {{ $apt->Status }}
                                    </span>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        @else
            <div class="alert alert-info">
                Bạn chưa có lịch hẹn nào.
            </div>
        @endif
    </div>
</div>
@endsection
