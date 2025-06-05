@if($commissions->isEmpty())
    <div class="text-center py-5">
        <div class="empty-state">
            <i class="fas fa-percentage fa-3x text-muted mb-3"></i>
            <h5 class="text-muted">Chưa có hoa hồng {{ $type }}</h5>
            <p class="text-muted">Không có dữ liệu hoa hồng {{ strtolower($type) }} nào được tìm thấy.</p>
        </div>
    </div>
@else
    <!-- Admin Dashboard Header -->
    <div class="admin-dashboard-header mb-4">
        <div class="text-center">
            <h4 class="text-primary mb-1">
                <i class="fas fa-chart-line me-2"></i>Quản lý hoa hồng {{ $type }}
            </h4>
            <p class="text-muted mb-0">Thống kê và phân tích doanh thu hoa hồng</p>
        </div>
    </div>

    <!-- Key Performance Indicators -->
    <div class="row mb-4">
        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12 mb-3">
            <div class="card kpi-card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="kpi-icon bg-primary bg-gradient rounded-3 p-3 me-3">
                            <i class="fas fa-list-ol text-white fa-lg"></i>
                        </div>
                        <div class="flex-grow-1">
                            <h3 class="text-dark mb-1 fw-bold">{{ $commissions->count() }}</h3>
                            <p class="text-muted mb-0 small">Tổng giao dịch</p>
                            <div class="trend-indicator mt-1">
                                <i class="fas fa-arrow-up text-success me-1"></i>
                                <small class="text-success">+12% từ tháng trước</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12 mb-3">
            <div class="card kpi-card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="kpi-icon bg-success bg-gradient rounded-3 p-3 me-3">
                            <i class="fas fa-hand-holding-usd text-white fa-lg"></i>
                        </div>
                        <div class="flex-grow-1">
                            <h3 class="text-dark mb-1 fw-bold">{{ number_format($commissions->sum('Amount')/1000000, 1) }}M</h3>
                            <p class="text-muted mb-0 small">Tổng hoa hồng (VNĐ)</p>
                            <div class="trend-indicator mt-1">
                                <i class="fas fa-arrow-up text-success me-1"></i>
                                <small class="text-success">+8.5% từ tháng trước</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12 mb-3">
            <div class="card kpi-card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="kpi-icon bg-info bg-gradient rounded-3 p-3 me-3">
                            <i class="fas fa-chart-line text-white fa-lg"></i>
                        </div>
                        <div class="flex-grow-1">
                            @php
                                $totalRevenue = $commissions->sum(function($commission) {
                                    return $commission->comm_trans ? $commission->comm_trans->TotalPrice : 0;
                                });
                            @endphp
                            <h3 class="text-dark mb-1 fw-bold">{{ number_format($totalRevenue/1000000000, 1) }}B</h3>
                            <p class="text-muted mb-0 small">Tổng doanh thu (VNĐ)</p>
                            <div class="trend-indicator mt-1">
                                <i class="fas fa-arrow-up text-success me-1"></i>
                                <small class="text-success">+15.2% từ tháng trước</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12 mb-3">
            <div class="card kpi-card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="kpi-icon bg-warning bg-gradient rounded-3 p-3 me-3">
                            <i class="fas fa-percentage text-white fa-lg"></i>
                        </div>
                        <div class="flex-grow-1">
                            @php
                                $commissionRatio = $totalRevenue > 0 ? ($commissions->sum('Amount') / $totalRevenue) * 100 : 0;
                            @endphp
                            <h3 class="text-dark mb-1 fw-bold">{{ number_format($commissionRatio, 1) }}%</h3>
                            <p class="text-muted mb-0 small">Tỷ lệ hoa hồng</p>
                            <div class="trend-indicator mt-1">
                                <i class="fas fa-arrow-down text-danger me-1"></i>
                                <small class="text-danger">-2.3% từ tháng trước</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Analytics Dashboard -->
    <div class="row mb-4">
        <!-- Revenue Trend Chart -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom">
                    <div class="d-flex justify-content-between align-items-center">
                        <h6 class="card-title mb-0 text-primary fw-bold">
                            <i class="fas fa-chart-area me-2"></i>Xu hướng doanh thu hoa hồng
                        </h6>
                        <div class="btn-group btn-group-sm" role="group">
                            <button type="button" class="btn btn-outline-primary active" data-period="7days">7 ngày</button>
                            <button type="button" class="btn btn-outline-primary" data-period="30days">30 ngày</button>
                            <button type="button" class="btn btn-outline-primary" data-period="90days">90 ngày</button>
                            <button type="button" class="btn btn-outline-primary" data-period="year">Năm</button>
                        </div>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="revenue-chart-container" style="height: 350px; position: relative;">
                        <canvas id="revenueChart" style="width: 100%; height: 100%;"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Performance Metrics -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom">
                    <h6 class="card-title mb-0 text-primary fw-bold">
                        <i class="fas fa-analytics me-2"></i>Phân tích hiệu suất
                    </h6>
                </div>
                <div class="card-body">
                    <!-- Commission Rate Analysis -->
                    <div class="performance-metric mb-4">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="text-muted small">Tỷ lệ hoa hồng</span>
                            <span class="fw-bold text-primary">{{ number_format($commissionRatio, 2) }}%</span>
                        </div>
                        <div class="progress mb-2" style="height: 8px;">
                            <div class="progress-bar bg-gradient-primary" role="progressbar"
                                 style="width: {{ min($commissionRatio * 2, 100) }}%"></div>
                        </div>
                        <small class="text-muted">Mục tiêu: 5.0%</small>
                    </div>

                    <!-- Agent Performance -->
                    <div class="performance-metric mb-4">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="text-muted small">Số agent hoạt động</span>
                            @php $activeAgents = $commissions->pluck('AgentID')->unique()->count(); @endphp
                            <span class="fw-bold text-success">{{ $activeAgents }}</span>
                        </div>
                        <div class="progress mb-2" style="height: 8px;">
                            <div class="progress-bar bg-gradient-success" role="progressbar"
                                 style="width: {{ min($activeAgents * 10, 100) }}%"></div>
                        </div>
                        <small class="text-muted">{{ number_format($commissions->sum('Amount') / max($activeAgents, 1), 0) }} VNĐ/agent</small>
                    </div>

                    <!-- Monthly Growth -->
                    <div class="performance-metric mb-4">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="text-muted small">Tăng trưởng tháng</span>
                            <span class="fw-bold text-success">+15.2%</span>
                        </div>
                        <div class="progress mb-2" style="height: 8px;">
                            <div class="progress-bar bg-gradient-info" role="progressbar" style="width: 76%"></div>
                        </div>
                        <small class="text-muted">So với tháng trước</small>
                    </div>

                    <!-- Revenue Breakdown -->
                    <div class="revenue-breakdown-summary">
                        <h6 class="text-muted mb-3">Phân bổ doanh thu</h6>
                        <div class="breakdown-item d-flex justify-content-between mb-2">
                            <span class="text-muted small">Doanh thu thuần</span>
                            <span class="fw-bold">{{ number_format(($totalRevenue - $commissions->sum('Amount')) / 1000000, 1) }}M</span>
                        </div>
                        <div class="breakdown-item d-flex justify-content-between mb-2">
                            <span class="text-muted small">Hoa hồng</span>
                            <span class="fw-bold text-success">{{ number_format($commissions->sum('Amount') / 1000000, 1) }}M</span>
                        </div>
                        <hr class="my-2">
                        <div class="breakdown-item d-flex justify-content-between">
                            <span class="text-muted fw-bold">Tổng doanh thu</span>
                            <span class="fw-bold text-primary">{{ number_format($totalRevenue / 1000000, 1) }}M</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Commission Management Table -->
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom">
                    <div class="d-flex justify-content-between align-items-center">
                        <h6 class="card-title mb-0 text-primary fw-bold">
                            <i class="fas fa-table me-2"></i>Danh sách hoa hồng {{ $type }}
                        </h6>
                        <div class="d-flex gap-2">
                            <button type="button" class="btn btn-outline-secondary btn-sm" id="toggleTableWidth" title="Mở rộng/Thu gọn bảng">
                                <i class="fas fa-expand-arrows-alt"></i>
                            </button>
                            <input type="text" class="form-control form-control-sm" placeholder="Tìm kiếm..." style="width: 200px;">
                        </div>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th width="8%">Mã HH</th>
                                    <th width="15%">Môi giới</th>
                                    <th width="20%">Bất động sản</th>
                                    <th width="15%">Giao dịch</th>
                                    <th width="12%">Hoa hồng</th>
                                    <th width="10%">Trạng thái</th>
                                    <th width="10%">Ngày TT</th>
                                    <th width="10%">Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($commissions as $commission)
                                    <tr class="commission-row">
                                        <!-- Mã Hoa Hồng -->
                                        <td class="col-commission-id">
                                            <span class="badge bg-primary">{{ $commission->CommissionID }}</span>
                                            <!-- Debug: Commission ID = {{ $commission->CommissionID }} -->
                                        </td>

                                        <!-- Thông tin Môi giới -->
                                        <td class="col-agent">
                                            @if($commission->comm_agent)
                                                <div class="d-flex align-items-center">
                                                    <div class="agent-avatar bg-secondary rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 32px; height: 32px;">
                                                        <i class="fas fa-user text-white"></i>
                                                    </div>
                                                    <div class="agent-info">
                                                        <div class="fw-bold text-dark small agent-name">{{ $commission->comm_agent->Name }}</div>
                                                        <small class="text-muted agent-phone">{{ $commission->comm_agent->Phone }}</small>
                                                        <div class="agent-email" style="display: none;">
                                                            <small class="text-muted">{{ $commission->comm_agent->Email ?? 'N/A' }}</small>
                                                        </div>
                                                    </div>
                                                </div>
                                            @else
                                                <span class="text-muted">Chưa gán</span>
                                            @endif
                                        </td>

                                        <!-- Thông tin Bất động sản -->
                                        <td class="col-property">
                                            @if($commission->comm_trans && $commission->comm_trans->trans_property)
                                                <div class="property-info">
                                                    <div class="fw-bold text-dark small property-title">
                                                        <span class="property-title-short">{{ Str::limit($commission->comm_trans->trans_property->Title, 30) }}</span>
                                                        <span class="property-title-full" style="display: none;">{{ $commission->comm_trans->trans_property->Title }}</span>
                                                    </div>
                                                    <small class="text-muted property-address">
                                                        <span class="property-address-short">{{ Str::limit($commission->comm_trans->trans_property->Address, 40) }}</span>
                                                        <span class="property-address-full" style="display: none;">{{ $commission->comm_trans->trans_property->Address }}</span>
                                                    </small>
                                                </div>
                                            @else
                                                <span class="text-muted">N/A</span>
                                            @endif
                                        </td>

                                        <!-- Thông tin Giao dịch -->
                                        <td class="col-transaction">
                                            @if($commission->comm_trans)
                                                <div class="transaction-info">
                                                    <div class="fw-bold text-success">{{ number_format($commission->comm_trans->TotalPrice, 0) }} VNĐ</div>
                                                    <small class="text-muted">{{ \Carbon\Carbon::parse($commission->comm_trans->TransactionDate)->format('d/m/Y') }}</small>
                                                    <div class="transaction-type" style="display: none;">
                                                        <small class="text-info">{{ $commission->comm_trans->TransactionType ?? $type }}</small>
                                                    </div>
                                                </div>
                                            @else
                                                <span class="text-muted">N/A</span>
                                            @endif
                                        </td>

                                        <!-- Hoa hồng -->
                                        <td class="col-commission">
                                            <div class="commission-info">
                                                <div class="fw-bold text-primary">{{ number_format($commission->Amount, 0) }} VNĐ</div>
                                                <small class="text-muted">{{ $commission->Percentage }}%</small>
                                            </div>
                                        </td>

                                        <!-- Trạng thái thanh toán -->
                                        <td class="col-status">
                                            @if($commission->PaidDate)
                                                <span class="badge bg-success">
                                                    <i class="fas fa-check-circle me-1"></i>Đã TT
                                                </span>
                                            @else
                                                <span class="badge bg-warning">
                                                    <i class="fas fa-clock me-1"></i>Chờ TT
                                                </span>
                                            @endif
                                        </td>

                                        <!-- Ngày thanh toán -->
                                        <td class="col-date">
                                            @if($commission->PaidDate)
                                                <span class="text-success small">{{ \Carbon\Carbon::parse($commission->PaidDate)->format('d/m/Y') }}</span>
                                            @else
                                                <span class="text-muted small">-</span>
                                            @endif
                                        </td>

                                        <!-- Thao tác -->
                                        <td class="col-actions">
                                            <div class="btn-group btn-group-sm" role="group">
                                                <form method="GET" action="{{ route('admin.commission.view') }}" target="_blank" style="display: inline;">
                                                    <input type="hidden" name="commission_id" value="{{ $commission->CommissionID }}">
                                                    <button type="submit" class="btn btn-outline-primary"
                                                            title="Xem chi tiết - ID: {{ $commission->CommissionID }}">
                                                        <i class="fas fa-eye"></i>
                                                    </button>
                                                </form>
                                                @if(!$commission->PaidDate)
                                                    <button type="button" class="btn btn-outline-warning" title="Thanh toán">
                                                        <i class="fas fa-credit-card"></i>
                                                    </button>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endif

