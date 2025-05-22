@extends('_layout._layowner.app')

@section('content')
<div class="container">
    <div class="row mb-4">
        <div class="col-md-8">
            <h1 class="h3">Lịch sử Giao dịch</h1>
        </div>
        <div class="col-md-4 text-end">
            <a href="{{ route('export.transactions') }}" class="btn btn-outline-primary">
                <i class="bi bi-download"></i> Xuất báo cáo
            </a>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <!-- Total Value -->
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <h6 class="text-muted">Tổng giá trị giao dịch</h6>
                    <h2 class="mb-2">{{ number_format($totalValue / 1000000000, 1) }} tỷ VND</h2>
                    <small class="text-{{ $valueGrowth >= 0 ? 'success' : 'danger' }}">
                        <i class="bi bi-arrow-{{ $valueGrowth >= 0 ? 'up' : 'down' }}"></i>
                        {{ abs($valueGrowth) }}% so với tháng trước
                    </small>
                </div>
            </div>
        </div>

        <!-- Transaction Count -->
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <h6 class="text-muted">Số lượng giao dịch</h6>
                    <h2 class="mb-2">{{ $transactionCount }}</h2>
                    <small class="text-{{ $countGrowth >= 0 ? 'success' : 'danger' }}">
                        <i class="bi bi-arrow-{{ $countGrowth >= 0 ? 'up' : 'down' }}"></i>
                        {{ $countGrowth > 0 ? '+' : '' }}{{ $countGrowth }} giao dịch mới trong tháng này
                    </small>
                </div>
            </div>
        </div>

        <!-- Amount Paid -->
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <h6 class="text-muted">Hoa hồng đã trả</h6>
                    <h2 class="mb-2">{{ number_format($paidAmount / 1000000, 0) }} triệu VND</h2>
                    <small class="text-{{ $paidGrowth >= 0 ? 'success' : 'danger' }}">
                        <i class="bi bi-arrow-{{ $paidGrowth >= 0 ? 'up' : 'down' }}"></i>
                        {{ abs($paidGrowth) }}% so với tháng trước
                    </small>
                </div>
            </div>
        </div>

        <!-- Success Rate -->
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <h6 class="text-muted">Hiệu suất</h6>
                    <h2 class="mb-2">{{ $successRate }}%</h2>
                    <small class="text-success">
                        <i class="bi bi-check-circle"></i>
                        {{ $successRate }}% tỷ lệ thành công
                    </small>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('owner.transactions.index') }}" method="GET" class="row g-3">
                        <div class="col-md-6">
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-search"></i></span>
                                <input type="text" class="form-control" placeholder="Tìm kiếm giao dịch..." name="search">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-calendar"></i></span>
                                <input type="date" class="form-control" name="from_date" value="{{ $fromDate }}">
                                <span class="input-group-text">-</span>
                                <input type="date" class="form-control" name="to_date" value="{{ $toDate }}">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="d-flex">
                                <button type="submit" class="btn btn-primary me-2">
                                    <i class="bi bi-filter"></i> Bộ lọc
                                </button>
                                <div class="dropdown">
                                    <button class="btn btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                        Tất cả loại
                                    </button>
                                    <ul class="dropdown-menu">
                                        <li><a class="dropdown-item" href="#">Tất cả</a></li>
                                        <li><a class="dropdown-item" href="#">Bán</a></li>
                                        <li><a class="dropdown-item" href="#">Cho thuê</a></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Transaction Tabs -->
    <ul class="nav nav-tabs mb-3">
        <li class="nav-item">
            <a class="nav-link active" href="#">Tất cả</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="#">Đã hoàn thành</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="#">Đang xử lý</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="#">Đã hủy</a>
        </li>
    </ul>

    <!-- Transaction Table -->
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Mã giao dịch</th>
                            <th>Ngày</th>
                            <th>Bất động sản</th>
                            <th>Loại</th>
                            <th>Giá trị</th>
                            <th>Khách hàng</th>
                            <th>Trạng thái</th>
                            <th>Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($transactions as $transaction)
                        <tr>
                            <td>{{ $transaction->TransactionID }}</td>
                            <td>{{ \Carbon\Carbon::parse($transaction->TransactionDate)->format('d/m/Y') }}</td>
                            <td>
                                @if($transaction->property)
                                    <div class="d-flex align-items-center">
                                        <div class="property-img me-2">
                                            <img src="{{ asset('images/properties/' . ($transaction->property->featured_image ?? 'default.jpg')) }}" 
                                                 alt="{{ $transaction->property->Title }}" 
                                                 class="img-thumbnail" style="width: 40px; height: 40px; object-fit: cover;">
                                        </div>
                                        <div>{{ $transaction->property->Title }}</div>
                                    </div>
                                @else
                                    <span class="text-muted">Không có</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-{{ $transaction->TransactionType == 'Bán' ? 'primary' : 'success' }}">
                                    {{ $transaction->TransactionType }}
                                </span>
                            </td>
                            <td>{{ number_format($transaction->Amount / 1000000000, 1) }} tỷ VND</td>
                            <td>
                                @if(isset($transaction->trans_cus))
                                    {{ $transaction->trans_cus->FullName }}
                                @else
                                    <span class="text-muted">Không có</span>
                                @endif
                            </td>
                            <td>
                                @php
                                    $statusClass = [
                                        'Hoàn thành' => 'success',
                                        'Đang xử lý' => 'warning',
                                        'Đã hủy' => 'danger',
                                    ][$transaction->Status] ?? 'secondary';
                                @endphp
                                <span class="badge bg-{{ $statusClass }}">{{ $transaction->Status }}</span>
                            </td>
                            <td>
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-outline-secondary" type="button" data-bs-toggle="dropdown">
                                        <i class="bi bi-three-dots"></i>
                                    </button>
                                    <ul class="dropdown-menu">
                                        <li><a class="dropdown-item" href="{{ route('transaction.view', $transaction->TransactionID) }}">Xem chi tiết</a></li>
                                        <li><a class="dropdown-item" href="{{ route('transaction.print', $transaction->TransactionID) }}">In hóa đơn</a></li>
                                    </ul>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center">Không có giao dịch nào</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Add any JavaScript needed for the transactions page
    $(document).ready(function() {
        // Date range picker initialization if needed
    });
</script>
@endpush
