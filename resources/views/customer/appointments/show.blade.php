@extends('_layout._layhome.home')

@section('styles')
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
<style>
/* Full-width background wrapper */
.full-bg-wrapper {
    min-height: 100vh;
    width: 100vw;
    margin-left: calc(-50vw + 50%);
    margin-right: calc(-50vw + 50%);
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    background-attachment: fixed;
    position: relative;
    overflow-x: hidden;
}

.full-bg-wrapper::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grid" width="10" height="10" patternUnits="userSpaceOnUse"><path d="M 10 0 L 0 0 0 10" fill="none" stroke="rgba(255,255,255,0.1)" stroke-width="0.5"/></pattern></defs><rect width="100" height="100" fill="url(%23grid)"/></svg>');
    pointer-events: none;
    z-index: 0;
}

.full-bg-wrapper > * {
    position: relative;
    z-index: 1;
}

/* Appointment Container Styling */
.appointment-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 30px 20px;
    font-family: 'Poppins', sans-serif;
}

/* Page Header */
.page-header {
    text-align: center;
    margin-bottom: 50px;
    color: white;
    position: relative;
}

.page-header::before {
    content: '';
    position: absolute;
    top: -20px;
    left: 50%;
    transform: translateX(-50%);
    width: 100px;
    height: 4px;
    background: rgba(255,255,255,0.3);
    border-radius: 2px;
}

.page-title {
    font-size: 3rem;
    font-weight: 700;
    margin-bottom: 15px;
    text-shadow: 0 4px 8px rgba(0,0,0,0.2);
    letter-spacing: -0.5px;
}

.page-subtitle {
    font-size: 1.2rem;
    opacity: 0.9;
    margin: 0;
    font-weight: 300;
    max-width: 600px;
    margin: 0 auto;
}

/* Stats Cards */
.appointments-stats {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 25px;
    margin-bottom: 50px;
    max-width: 1200px;
    margin-left: auto;
    margin-right: auto;
    margin-bottom: 50px;
}

.stat-card {
    background: white;
    padding: 25px;
    border-radius: 15px;
    box-shadow: 0 8px 25px rgba(0,0,0,0.1);
    display: flex;
    align-items: center;
    gap: 20px;
    transition: transform 0.3s ease;
}

.stat-card:hover {
    transform: translateY(-5px);
}

.stat-icon {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 24px;
    color: white;
}

.stat-icon.pending { background: #f39c12; }
.stat-icon.confirmed { background: #27ae60; }
.stat-icon.completed { background: #3498db; }
.stat-icon.cancelled { background: #e74c3c; }

.stat-info h3 {
    margin: 0;
    font-size: 2rem;
    font-weight: 700;
    color: #2c3e50;
}

.stat-info p {
    margin: 5px 0 0 0;
    color: #7f8c8d;
    font-weight: 500;
}

/* Appointments Grid */
.appointments-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(400px, 1fr));
    gap: 25px;
    margin-bottom: 40px;
}

.appointment-card {
    background: white;
    border-radius: 20px;
    padding: 30px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.1);
    transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    position: relative;
    overflow: hidden;
    border: 1px solid rgba(255,255,255,0.1);
}

.appointment-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: linear-gradient(90deg, #667eea, #764ba2);
}

.appointment-card:hover {
    transform: translateY(-8px) scale(1.02);
    box-shadow: 0 20px 40px rgba(0,0,0,0.15);
}

.appointment-card.priority-high::before {
    background: linear-gradient(90deg, #e74c3c, #c0392b);
}

.appointment-card.priority-medium::before {
    background: linear-gradient(90deg, #f39c12, #e67e22);
}

.appointment-card.priority-low::before {
    background: linear-gradient(90deg, #27ae60, #2ecc71);
}

.appointment-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 20px;
}

.appointment-title {
    font-size: 1.3rem;
    font-weight: 700;
    color: #2c3e50;
    margin: 0;
    line-height: 1.3;
}

.status-badge {
    padding: 8px 16px;
    border-radius: 25px;
    font-size: 0.8rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 1px;
    position: relative;
    overflow: hidden;
    border: 2px solid transparent;
}

.status-badge::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
    transition: left 0.6s;
}

.status-badge:hover::before {
    left: 100%;
}

.status-badge.pending {
    background: linear-gradient(135deg, #ffeaa7, #fdcb6e);
    color: #e17055;
    border-color: #fdcb6e;
}

.status-badge.confirmed {
    background: linear-gradient(135deg, #55efc4, #00cec9);
    color: #00695c;
    border-color: #00cec9;
}

.status-badge.completed {
    background: linear-gradient(135deg, #74b9ff, #0984e3);
    color: #0d47a1;
    border-color: #0984e3;
}

.status-badge.cancelled {
    background: linear-gradient(135deg, #fab1a0, #e17055);
    color: #c62828;
    border-color: #e17055;
}

.appointment-description {
    color: #7f8c8d;
    margin-bottom: 20px;
    line-height: 1.6;
}

.appointment-details {
    display: grid;
    gap: 12px;
}

.info-row {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 8px 0;
    border-bottom: 1px solid rgba(0,0,0,0.05);
    transition: background-color 0.3s ease;
}

.info-row:hover {
    background-color: rgba(102, 126, 234, 0.05);
    border-radius: 8px;
    padding-left: 10px;
    padding-right: 10px;
}

.info-row:last-child {
    border-bottom: none;
}

.info-label {
    font-weight: 600;
    color: #2c3e50;
    min-width: 120px;
    font-size: 0.9rem;
}

.info-value {
    color: #34495e;
    flex: 1;
    font-size: 0.9rem;
}

.appointment-actions {
    margin-top: 20px;
    display: flex;
    gap: 10px;
    justify-content: flex-end;
}

.btn {
    padding: 12px 20px;
    border: none;
    border-radius: 12px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    position: relative;
    overflow: hidden;
    font-size: 0.9rem;
    letter-spacing: 0.5px;
    text-transform: uppercase;
}

.btn::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
    transition: left 0.6s;
}

.btn:hover::before {
    left: 100%;
}

.btn-primary {
    background: linear-gradient(135deg, #667eea, #764ba2);
    color: white;
    box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
}

.btn-primary:hover {
    background: linear-gradient(135deg, #5a6fd8, #6a42a0);
    transform: translateY(-3px);
    box-shadow: 0 8px 25px rgba(102, 126, 234, 0.4);
}

.btn-danger {
    background: linear-gradient(135deg, #e74c3c, #c0392b);
    color: white;
    box-shadow: 0 4px 15px rgba(231, 76, 60, 0.3);
}

.btn-danger:hover {
    background: linear-gradient(135deg, #c0392b, #a93226);
    transform: translateY(-3px);
    box-shadow: 0 8px 25px rgba(231, 76, 60, 0.4);
}

/* Modal Styling */
.modal {
    display: none;
    position: fixed;
    z-index: 1000;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0,0,0,0.5);
}

.modal-content {
    background-color: white;
    margin: 5% auto;
    padding: 30px;
    border-radius: 15px;
    width: 90%;
    max-width: 600px;
    max-height: 80vh;
    overflow-y: auto;
}

.modal-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
    padding-bottom: 15px;
    border-bottom: 2px solid #eee;
}

.modal-title {
    font-size: 1.5rem;
    font-weight: 700;
    color: #2c3e50;
    margin: 0;
}

.close {
    color: #aaa;
    font-size: 28px;
    font-weight: bold;
    cursor: pointer;
    transition: color 0.3s ease;
}

.close:hover {
    color: #000;
}

.modal-body {
    line-height: 1.6;
}

.modal-info-grid {
    display: grid;
    gap: 15px;
}

.modal-info-row {
    display: flex;
    padding: 10px 0;
    border-bottom: 1px solid #f0f0f0;
}

.modal-info-label {
    font-weight: 600;
    color: #2c3e50;
    min-width: 140px;
}

.modal-info-value {
    color: #34495e;
    flex: 1;
}

.no-appointments {
    text-align: center;
    color: white;
    font-size: 1.2rem;
    padding: 60px 20px;
}

.no-appointments-icon {
    font-size: 4rem;
    margin-bottom: 20px;
    opacity: 0.7;
}

/* Back Button */
.back-to-home {
    position: fixed;
    bottom: 30px;
    right: 30px;
    background: white;
    color: #667eea;
    padding: 15px 20px;
    border-radius: 50px;
    text-decoration: none;
    box-shadow: 0 8px 25px rgba(0,0,0,0.15);
    transition: all 0.3s ease;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 8px;
}

.back-to-home:hover {
    transform: translateY(-3px);
    box-shadow: 0 12px 30px rgba(0,0,0,0.2);
    color: #5a6fd8;
}

/* Responsive Design */
@media (max-width: 768px) {
    .appointment-container {
        padding: 15px;
    }
    
    .page-title {
        font-size: 2rem;
    }
    
    .appointments-grid {
        grid-template-columns: 1fr;
        gap: 20px;
    }
    
    .appointment-card {
        padding: 20px;
    }
    
    .appointments-stats {
        grid-template-columns: 1fr;
    }
    
    .modal-content {
        margin: 10% auto;
        padding: 20px;
        width: 95%;
    }
    
    .back-to-home {
        bottom: 20px;
        right: 20px;
    }
}
</style>
@endsection

@section('content')
<!-- Full-width background wrapper -->
<div class="full-bg-wrapper">
    <div class="appointment-container">
        <div class="page-header">
            <h1 class="page-title">📅 Lịch Hẹn Của Tôi</h1>
            <p class="page-subtitle">Quản lý và theo dõi tất cả các cuộc hẹn xem bất động sản</p>
        </div>

    @if($appointments && $appointments->count() > 0)
        <!-- Stats Cards -->
        <div class="appointments-stats">
            <div class="stat-card">
                <div class="stat-icon pending">
                    <i class="fas fa-clock"></i>
                </div>
                <div class="stat-info">
                    <h3>{{ $appointments->where('Status', 'Pending')->count() }}</h3>
                    <p>Chờ xác nhận</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon confirmed">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="stat-info">
                    <h3>{{ $appointments->where('Status', 'Confirmed')->count() }}</h3>
                    <p>Đã xác nhận</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon completed">
                    <i class="fas fa-star"></i>
                </div>
                <div class="stat-info">
                    <h3>{{ $appointments->where('Status', 'Completed')->count() }}</h3>
                    <p>Hoàn thành</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon cancelled">
                    <i class="fas fa-times-circle"></i>
                </div>
                <div class="stat-info">
                    <h3>{{ $appointments->where('Status', 'Cancelled')->count() }}</h3>
                    <p>Đã hủy</p>
                </div>
            </div>
        </div>

        <!-- Appointments Grid -->
        <div class="appointments-grid">
            @foreach($appointments as $appointment)
                <div class="appointment-card">
                    <div class="appointment-header">
                        <h3 class="appointment-title">{{ $appointment->TitleAppoint ?? 'Cuộc hẹn xem nhà' }}</h3>
                        <span class="status-badge {{ strtolower($appointment->Status ?? 'pending') }}">
                            {{ $appointment->Status ?? 'Pending' }}
                        </span>
                    </div>

                    @if($appointment->DescAppoint)
                        <div class="appointment-description">
                            {{ Str::limit($appointment->DescAppoint, 100) }}
                        </div>
                    @endif

                    <div class="appointment-details">
                        <div class="info-row">
                            <span class="info-label">🏠 Bất động sản:</span>
                            <span class="info-value">
                                {{ $appointment->property->Title ?? 'Không có thông tin' }}
                            </span>
                        </div>
                        
                        <div class="info-row">
                            <span class="info-label">👤 Agent:</span>
                            <span class="info-value">
                                {{ $appointment->agent->Name ?? 'Chưa phân công' }}
                            </span>
                        </div>
                        
                        <div class="info-row">
                            <span class="info-label">🏡 Chủ sở hữu:</span>
                            <span class="info-value">
                                {{ $appointment->owner->Name ?? 'Không có thông tin' }}
                            </span>
                        </div>
                        
                        <div class="info-row">
                            <span class="info-label">📅 Bắt đầu:</span>
                            <span class="info-value">
                                {{ $appointment->AppointmentDateStart ? \Carbon\Carbon::parse($appointment->AppointmentDateStart)->format('d/m/Y H:i') : 'Chưa xác định' }}
                            </span>
                        </div>
                        
                        <div class="info-row">
                            <span class="info-label">⏰ Kết thúc:</span>
                            <span class="info-value">
                                {{ $appointment->AppointmentDateEnd ? \Carbon\Carbon::parse($appointment->AppointmentDateEnd)->format('d/m/Y H:i') : 'Chưa xác định' }}
                            </span>
                        </div>
                    </div>

                    <div class="appointment-actions">
                        <button class="btn btn-primary" onclick="showAppointmentDetails({{ $appointment->AppointmentID }})">
                            <i class="fas fa-info-circle"></i> Chi tiết
                        </button>
                        @if($appointment->Status === 'Pending' || $appointment->Status === 'Confirmed')
                            <button class="btn btn-danger" onclick="cancelAppointment({{ $appointment->AppointmentID }})">
                                <i class="fas fa-times"></i> Hủy lịch
                            </button>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="no-appointments">
            <div class="no-appointments-icon">📅</div>
            <h2>Chưa có lịch hẹn nào</h2>
            <p>Bạn chưa có cuộc hẹn xem bất động sản nào. Hãy tìm kiếm và đặt lịch xem nhà ngay!</p>
        </div>
    @endif

    <!-- Back to Home Button -->
    <a href="{{ url('/') }}" class="back-to-home">
        <i class="fas fa-home"></i> Về trang chủ
    </a>
</div>

<!-- Modal for Appointment Details -->
<div id="appointmentModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2 class="modal-title">Chi tiết lịch hẹn</h2>
            <span class="close" onclick="closeModal()">&times;</span>
        </div>
        <div class="modal-body">
            <div id="modalContent">
                <!-- Content will be loaded here -->
            </div>
        </div>
    </div>    </div>
</div>
@endsection

@section('scripts')
<script>
// Show appointment details in modal
function showAppointmentDetails(appointmentId) {
    const appointments = @json($appointments ?? []);
    const appointment = appointments.find(app => app.AppointmentID == appointmentId);
    
    if (!appointment) {
        alert('Không tìm thấy thông tin lịch hẹn');
        return;
    }
    
    const modalContent = document.getElementById('modalContent');
    modalContent.innerHTML = `
        <div class="modal-info-grid">
            <div class="modal-info-row">
                <span class="modal-info-label">Tiêu đề:</span>
                <span class="modal-info-value">${appointment.TitleAppoint || 'Cuộc hẹn xem nhà'}</span>
            </div>
            <div class="modal-info-row">
                <span class="modal-info-label">Mô tả:</span>
                <span class="modal-info-value">${appointment.DescAppoint || 'Không có mô tả'}</span>
            </div>
            <div class="modal-info-row">
                <span class="modal-info-label">Bất động sản:</span>
                <span class="modal-info-value">${appointment.property?.Title || 'Không có thông tin'}</span>
            </div>
            <div class="modal-info-row">
                <span class="modal-info-label">Agent phụ trách:</span>
                <span class="modal-info-value">${appointment.agent?.Name || 'Chưa phân công'}</span>
            </div>
            <div class="modal-info-row">
                <span class="modal-info-label">Chủ sở hữu:</span>
                <span class="modal-info-value">${appointment.owner?.Name || 'Không có thông tin'}</span>
            </div>
            <div class="modal-info-row">
                <span class="modal-info-label">Thời gian bắt đầu:</span>
                <span class="modal-info-value">${appointment.AppointmentDateStart ? new Date(appointment.AppointmentDateStart).toLocaleString('vi-VN') : 'Chưa xác định'}</span>
            </div>
            <div class="modal-info-row">
                <span class="modal-info-label">Thời gian kết thúc:</span>
                <span class="modal-info-value">${appointment.AppointmentDateEnd ? new Date(appointment.AppointmentDateEnd).toLocaleString('vi-VN') : 'Chưa xác định'}</span>
            </div>
            <div class="modal-info-row">
                <span class="modal-info-label">Trạng thái:</span>
                <span class="modal-info-value">
                    <span class="status-badge ${appointment.Status?.toLowerCase() || 'pending'}">
                        ${appointment.Status || 'Pending'}
                    </span>
                </span>
            </div>
        </div>
    `;
    
    document.getElementById('appointmentModal').style.display = 'block';
}

// Close modal
function closeModal() {
    document.getElementById('appointmentModal').style.display = 'none';
}

// Close modal when clicking outside
window.onclick = function(event) {
    const modal = document.getElementById('appointmentModal');
    if (event.target == modal) {
        modal.style.display = 'none';
    }
}

// Cancel appointment
function cancelAppointment(appointmentId) {
    if (confirm('Bạn có chắc chắn muốn hủy lịch hẹn này không?')) {
        fetch(`/appointments/${appointmentId}/cancel`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Hủy lịch hẹn thành công!');
                location.reload();
            } else {
                alert('Có lỗi xảy ra khi hủy lịch hẹn. Vui lòng thử lại.');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Có lỗi xảy ra khi hủy lịch hẹn. Vui lòng thử lại.');
        });
    }
}
</script>
@endsection
