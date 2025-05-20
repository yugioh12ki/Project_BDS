{{-- Property Statistics Component --}}
<div class="property-statistics mb-4">
    <div class="row g-3">
        <!-- Total Properties Card -->
        <div class="col-md-3 col-sm-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h6 class="text-muted">Tổng số BĐS</h6>
                        <div class="icon-box bg-light-primary">
                            <i class="fas fa-home text-primary"></i>
                        </div>
                    </div>
                    <h2 class="mb-0 fw-bold">{{ $totalProperties }}</h2>
                    <div class="mt-2 text-muted small">
                        <span>Cập nhật: {{ \Carbon\Carbon::now()->format('d/m/Y H:i') }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pending Properties Card -->
        <div class="col-md-3 col-sm-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h6 class="text-muted">Đang chờ duyệt</h6>
                        <div class="icon-box bg-light-warning">
                            <i class="fas fa-clock text-warning"></i>
                        </div>
                    </div>
                    <h2 class="mb-0 fw-bold">{{ $pendingProperties }}</h2>
                    <div class="mt-2 text-muted small">
                        <a href="{{ route('admin.property.status', ['status' => 'pending']) }}" class="text-decoration-none">
                            <i class="fas fa-arrow-right"></i> Xem chi tiết
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Approved Properties Card -->
        <div class="col-md-3 col-sm-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h6 class="text-muted">Đã duyệt</h6>
                        <div class="icon-box bg-light-success">
                            <i class="fas fa-check-circle text-success"></i>
                        </div>
                    </div>
                    <h2 class="mb-0 fw-bold">{{ $approvedProperties }}</h2>
                    <div class="mt-2 text-muted small">
                        <a href="{{ route('admin.property.status', ['status' => 'approved']) }}" class="text-decoration-none">
                            <i class="fas fa-arrow-right"></i> Xem chi tiết
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Rejected Properties Card -->
        <div class="col-md-3 col-sm-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h6 class="text-muted">Đã từ chối</h6>
                        <div class="icon-box bg-light-danger">
                            <i class="fas fa-times-circle text-danger"></i>
                        </div>
                    </div>
                    <h2 class="mb-0 fw-bold">{{ $rejectedProperties }}</h2>
                    <div class="mt-2 text-muted small">
                        <a href="{{ route('admin.property.status', ['status' => 'rejected']) }}" class="text-decoration-none">
                            <i class="fas fa-arrow-right"></i> Xem chi tiết
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.icon-box {
    width: 45px;
    height: 45px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
}

.icon-box i {
    font-size: 1.5rem;
}

.bg-light-primary {
    background-color: rgba(13, 110, 253, 0.15);
}

.bg-light-success {
    background-color: rgba(25, 135, 84, 0.15);
}

.bg-light-warning {
    background-color: rgba(255, 193, 7, 0.15);
}

.bg-light-danger {
    background-color: rgba(220, 53, 69, 0.15);
}

.property-statistics .card {
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.property-statistics .card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 20px rgba(0,0,0,.1) !important;
}
</style>
