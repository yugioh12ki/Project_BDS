@extends('_layout._layadmin.app')

@section('dashboard')
    <div class="index-container">
        <!-- Page Header -->
        <div class="page-header">
            <h1 class="index-title">Thống Kê Tổng Quan</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="#">Trang chủ</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Dashboard</li>
                </ol>
            </nav>
        </div>

        <!-- Statistics Cards -->
        <div class="dashboard-stats">
            <!-- Total Transactions -->
            <div class="stat-card primary">
                <div class="stat-icon">
                    <i class="fas fa-file-contract"></i>
                </div>
                <div class="stat-number">{{ \App\Models\Transaction::count() }}</div>
                <div class="stat-label">Tổng Giao Dịch</div>
                <div class="stat-trend up">
                    <i class="fas fa-arrow-up"></i>
                    <span>+12% so với tháng trước</span>
                </div>
            </div>

            <!-- Total Properties -->
            <div class="stat-card success">
                <div class="stat-icon">
                    <i class="fas fa-home"></i>
                </div>
                <div class="stat-number">{{ \App\Models\Property::count() }}</div>
                <div class="stat-label">Tổng Bất Động Sản</div>
                <div class="stat-trend up">
                    <i class="fas fa-arrow-up"></i>
                    <span>+8% so với tháng trước</span>
                </div>
            </div>

            <!-- Pending Commissions -->
            <div class="stat-card warning">
                <div class="stat-icon">
                    <i class="fas fa-money-bill-wave"></i>
                </div>
                <div class="stat-number">{{ \App\Models\Commission::where('StatusCommission', 'Pending')->count() }}</div>
                <div class="stat-label">Hoa Hồng Chờ Xử Lý</div>
                <div class="stat-trend down">
                    <i class="fas fa-arrow-down"></i>
                    <span>-3% so với tuần trước</span>
                </div>
            </div>

            <!-- Total Users -->
            <div class="stat-card danger">
                <div class="stat-icon">
                    <i class="fas fa-users"></i>
                </div>
                <div class="stat-number">{{ \App\Models\User::count() }}</div>
                <div class="stat-label">Tổng Người Dùng</div>
                <div class="stat-trend up">
                    <i class="fas fa-arrow-up"></i>
                    <span>+15% so với tháng trước</span>
                </div>
            </div>
        </div>

        <!-- Charts Row -->
        <div class="dashboard-grid">
            <!-- Transaction Chart -->
            <div class="chart-container">
                <div class="chart-header">
                    <h3>Thống Kê Giao Dịch Theo Loại</h3>
                    <div class="chart-controls">
                        <button class="btn btn-outline-primary btn-sm">Tháng</button>
                        <button class="btn btn-primary btn-sm">Năm</button>
                    </div>
                </div>
                <canvas id="transactionChart"></canvas>
            </div>

            <!-- Recent Activity -->
            <div class="page-section">
                <div class="section-header">
                    <h2>Hoạt Động Gần Đây</h2>
                </div>
                <div class="activity-feed">
                    <div class="activity-item">
                        <div class="activity-icon success">
                            <i class="fas fa-check"></i>
                        </div>
                        <div class="activity-content">
                            <div class="activity-title">Giao dịch được duyệt</div>
                            <div class="activity-description">Giao dịch #1234 đã được duyệt thành công</div>
                            <div class="activity-time">5 phút trước</div>
                        </div>
                    </div>
                    <div class="activity-item">
                        <div class="activity-icon warning">
                            <i class="fas fa-exclamation"></i>
                        </div>
                        <div class="activity-content">
                            <div class="activity-title">BĐS cần xem xét</div>
                            <div class="activity-description">3 bất động sản mới chờ duyệt</div>
                            <div class="activity-time">1 giờ trước</div>
                        </div>
                    </div>
                    <div class="activity-item">
                        <div class="activity-icon danger">
                            <i class="fas fa-times"></i>
                        </div>
                        <div class="activity-content">
                            <div class="activity-title">Người dùng bị khóa</div>
                            <div class="activity-description">Tài khoản user123 đã bị tạm khóa</div>
                            <div class="activity-time">2 giờ trước</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Commission Chart -->
        <div class="chart-container">
            <div class="chart-header">
                <h3>Thống Kê Hoa Hồng Theo Trạng Thái</h3>
                <div class="chart-controls">
                    <button class="btn btn-outline-primary btn-sm">Tuần</button>
                    <button class="btn btn-primary btn-sm">Tháng</button>
                </div>
            </div>
            <canvas id="commissionStatusChart"></canvas>
        </div>

        <!-- Monthly Statistics -->
        <div class="chart-container">
            <div class="chart-header">
                <h3>Thống Kê Giao Dịch Theo Tháng</h3>
                <div class="chart-controls">
                    <a href="#" class="btn btn-outline-primary btn-sm chart-type" data-type="bar">Cột</a>
                    <a href="#" class="btn btn-outline-primary btn-sm chart-type" data-type="line">Đường</a>
                    <a href="#" class="btn btn-primary btn-sm" id="refreshMonthlyChart">Làm mới</a>
                </div>
            </div>
            <canvas id="monthlyTransactionChart"></canvas>
        </div>

        <!-- Quick Actions -->
        <div class="page-section">
            <div class="section-header">
                <h2>Thao Tác Nhanh</h2>
            </div>
            <div class="quick-actions">
                <div class="quick-action">
                    <div class="action-icon">
                        <i class="fas fa-plus"></i>
                    </div>
                    <div class="action-title">Thêm BĐS Mới</div>
                    <div class="action-description">Tiếp nhận hồ sơ bất động sản mới</div>
                </div>
                <div class="quick-action">
                    <div class="action-icon">
                        <i class="fas fa-user-plus"></i>
                    </div>
                    <div class="action-title">Thêm Người Dùng</div>
                    <div class="action-description">Tạo tài khoản mới cho hệ thống</div>
                </div>
                <div class="quick-action">
                    <div class="action-icon">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <div class="action-title">Xem Báo Cáo</div>
                    <div class="action-description">Thống kê chi tiết và báo cáo</div>
                </div>
                <div class="quick-action">
                    <div class="action-icon">
                        <i class="fas fa-cog"></i>
                    </div>
                    <div class="action-title">Cài Đặt</div>
                    <div class="action-description">Quản lý cấu hình hệ thống</div>
                </div>
            </div>
        </div>
    </div>
                                <i class="fas fa-calendar fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Thống kê doanh thu -->
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-success shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                    Tổng Doanh Thu (VNĐ)</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                    {{ number_format(\App\Models\Transaction::where('TranStatus', 'Paid')->sum('TotalPrice'), 0, ',', '.') }}
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Hoa hồng đã trả -->
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-info shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                    Hoa Hồng Đã Thanh Toán</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                    {{ \App\Models\Commission::where('StatusCommission', 'Success')->count() }}
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-clipboard-list fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Hoa hồng chờ xử lý -->
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-warning shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                    Hoa Hồng Chờ Xử Lý</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                    {{ \App\Models\Commission::where('StatusCommission', 'Pending')->count() }}
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-comments fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Content Row -->
        <div class="row">
            <!-- Biểu đồ giao dịch theo loại -->
            <div class="col-lg-6 mb-4">
                <div class="card shadow mb-4">
                    <div class="card-header py-3" style="background-color: rgb(209, 209, 61)">
                        <h6 class="m-0 font-weight-bold text-primary">Thống Kê Giao Dịch Theo Loại</h6>
                    </div>
                    <div class="card-body">
                        <div class="chart-container pt-4 pb-2" style="position: relative; height: 300px;">
                            <canvas id="transactionTypeChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Biểu đồ hoa hồng theo trạng thái -->
            <div class="col-lg-6 mb-4">
                <div class="card shadow mb-4">
                    <div class="card-header py-3" style="background-color: rgb(209, 209, 61)">
                        <h6 class="m-0 font-weight-bold text-primary">Thống Kê Hoa Hồng Theo Trạng Thái</h6>
                    </div>
                    <div class="card-body">
                        <div class="chart-container pt-4 pb-2" style="position: relative; height: 300px;">
                            <canvas id="commissionStatusChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Content Row - Monthly Statistics -->
        <div class="row">
            <!-- Biểu đồ thống kê theo tháng -->
            <div class="col-lg-12 mb-4">
                <div class="card shadow mb-4" >
                    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between" style="background-color: rgb(209, 209, 61)">
                        <h6 class="m-0 font-weight-bold text-primary">Thống Kê Giao Dịch Theo Tháng</h6>
                        <div class="dropdown no-arrow">
                            <a class="dropdown-toggle" href="#" role="button" id="monthlyChartDropdown"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                            </a>
                            <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in"
                                aria-labelledby="monthlyChartDropdown">
                                <div class="dropdown-header">Tùy chọn biểu đồ:</div>
                                <a class="dropdown-item chart-type" data-type="bar" href="#">Biểu đồ cột</a>
                                <a class="dropdown-item chart-type" data-type="line" href="#">Biểu đồ đường</a>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" href="#" id="refreshMonthlyChart">Làm mới dữ liệu</a>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="chart-container pt-4 pb-2" style="position: relative; height: 350px;">
                            <canvas id="monthlyTransactionChart" width="800" height="350"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Bảng thống kê chi tiết -->
        <div class="row">
            <!-- Bảng Giao Dịch Mới Nhất -->
            <div class="col-lg-6 mb-4">
                <div class="card shadow mb-4">
                    <div class="card-header py-3" style="background-color: rgb(209, 209, 61)">
                        <h6 class="m-0 font-weight-bold text-primary">Giao Dịch Mới Nhất</h6>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered" width="100%" cellspacing="0">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Loại</th>
                                        <th>Tổng Tiền</th>
                                        <th>Ngày Giao Dịch</th>
                                        <th>Trạng Thái</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach(\App\Models\Transaction::orderBy('TransactionID', 'desc')->take(5)->get() as $transaction)
                                    <tr>
                                        <td>{{ $transaction->TransactionID }}</td>
                                        <td>{{ $transaction->TransactionType }}</td>
                                        <td>{{ number_format($transaction->TotalPrice, 0, ',', '.') }} VND</td>
                                        <td>{{ $transaction->TransactionDate ? date('d/m/Y', strtotime($transaction->TransactionDate)) : 'N/A' }}</td>
                                        <td>
                                            @if($transaction->TranStatus == 'Paid')
                                                <span class="badge text-bg-success">Đã thanh toán</span>
                                            @elseif($transaction->TranStatus == 'Pending')
                                                <span class="badge text-bg-warning">Chờ xử lý</span>
                                            @elseif($transaction->TranStatus == 'Cancel')
                                                <span class="badge text-bg-danger">Đã hủy</span>
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Bảng Hoa Hồng Mới Nhất -->
            <div class="col-lg-6 mb-4">
                <div class="card shadow mb-4">
                    <div class="card-header py-3" style="background-color: rgb(209, 209, 61)">
                        <h6 class="m-0 font-weight-bold text-primary">Hoa Hồng Mới Nhất</h6>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered" width="100%" cellspacing="0">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Số Tiền</th>
                                        <th>Loại</th>
                                        <th>Ngày Thanh Toán</th>
                                        <th>Trạng Thái</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach(\App\Models\Commission::orderBy('CommissionID', 'desc')->take(5)->get() as $commission)
                                    <tr>
                                        <td>{{ $commission->CommissionID }}</td>
                                        <td>{{ number_format($commission->Amount ?? 0, 0, ',', '.') }} VND</td>
                                        <td>{{ $commission->TypeCom }}</td>
                                        <td>{{ $commission->PaidDate ? date('d/m/Y', strtotime($commission->PaidDate)) : 'N/A' }}</td>
                                        <td>
                                            @if($commission->StatusCommission == 'Success')
                                                <span class="badge text-bg-success">Đã thanh toán</span>
                                            @elseif($commission->StatusCommission == 'Pending')
                                                <span class="badge text-bg-warning">Chờ xử lý</span>
                                            @elseif($commission->StatusCommission == 'Cancel')
                                                <span class="badge text-bg-danger">Đã hủy</span>
                                            @endif
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
    </div>

@push('scripts')
    <!-- Sử dụng Chart.js v4.4.9 -->
    {{-- <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.9/dist/chart.umd.min.js"></script> --}}
    <script>
        // Hàm render biểu đồ giao dịch
        function renderTransactionChart() {
            const transTypeCtx = document.getElementById('transactionTypeChart');
            if (!transTypeCtx) return;
            // Hủy chart cũ nếu có (Chart.js v4)
            if (Chart.getChart(transTypeCtx)) Chart.getChart(transTypeCtx).destroy();
            new Chart(transTypeCtx, {
                type: 'pie',
                data: {
                    labels: ['Thuê', 'Bán'],
                    datasets: [{
                        data: [3, 5],
                        backgroundColor: ['#4e73df', '#1cc88a']
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false
                }
            });
        }

        // Hàm render biểu đồ hoa hồng
        function renderCommissionChart() {
            const comStatusCtx = document.getElementById('commissionStatusChart');
            if (!comStatusCtx) return;
            if (Chart.getChart(comStatusCtx)) Chart.getChart(comStatusCtx).destroy();
            new Chart(comStatusCtx, {
                type: 'pie',
                data: {
                    labels: ['Đã Thanh Toán', 'Chờ Xử Lý', 'Đã Hủy'],
                    datasets: [{
                        data: [7, 3, 1],
                        backgroundColor: ['#1cc88a', '#f6c23e', '#e74a3b']
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false
                }
            });
        }

        // Hàm render biểu đồ thống kê theo tháng
        function renderMonthlyTransactionChart(chartType = 'bar') {
            const monthlyCtx = document.getElementById('monthlyTransactionChart');
            if (!monthlyCtx) return;
            if (Chart.getChart(monthlyCtx)) Chart.getChart(monthlyCtx).destroy();
            const months = ['Tháng 1', 'Tháng 2', 'Tháng 3', 'Tháng 4', 'Tháng 5', 'Tháng 6',
                'Tháng 7', 'Tháng 8', 'Tháng 9', 'Tháng 10', 'Tháng 11', 'Tháng 12'];
            const transactionCounts = [5, 7, 10, 8, 12, 15, 18, 14, 10, 12, 8, 6];
            new Chart(monthlyCtx, {
                type: chartType,
                data: {
                    labels: months,
                    datasets: [{
                        label: 'Số Giao Dịch',
                        data: transactionCounts,
                        backgroundColor: 'rgba(78, 115, 223, 0.5)',
                        borderColor: 'rgb(78, 115, 223)',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false
                }
            });
        }

        document.addEventListener('DOMContentLoaded', function() {
            if (typeof Chart === 'undefined') {
                alert('Không thể tải thư viện Chart.js. Vui lòng làm mới trang.');
            } else {
                renderTransactionChart();
                renderCommissionChart();
                renderMonthlyTransactionChart();
            }
            document.querySelectorAll('.chart-type').forEach(function(element) {
                element.addEventListener('click', function(e) {
                    e.preventDefault();
                    const chartType = this.getAttribute('data-type');
                    renderMonthlyTransactionChart(chartType);
                });
            });
            const refreshButton = document.getElementById('refreshMonthlyChart');
            if (refreshButton) {
                refreshButton.addEventListener('click', function(e) {
                    e.preventDefault();
                    renderMonthlyTransactionChart();
                });
            }
        });
    </script>
@endpush
@endsection
