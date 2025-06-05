@extends('_layout._layadmin.app')

@section('title', 'Demo Responsive Table - Enhanced')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-gradient text-white" style="background: var(--primary-gradient);">
                    <h4 class="card-title mb-0">
                        <i class="fas fa-table me-2"></i>
                        Demo Responsive Table Enhancements
                    </h4>
                    <p class="card-text mb-0 opacity-75">
                        Bảng cải tiến với khả năng cuộn ngang/dọc và responsive design
                    </p>
                </div>
                
                <div class="card-body">
                    <!-- Instructions -->
                    <div class="alert alert-info d-flex align-items-center mb-4">
                        <i class="fas fa-info-circle me-3 fs-4"></i>
                        <div>
                            <h6 class="mb-1">Hướng dẫn test responsive table:</h6>
                            <ul class="mb-0 small">
                                <li>Thu nhỏ cửa sổ browser để test responsive breakpoints</li>
                                <li>Click vào header để sort bảng</li>
                                <li>Cuộn ngang trên mobile để xem thêm cột</li>
                                <li>Ở màn hình rất nhỏ (&lt;576px), bảng sẽ chuyển thành card layout</li>
                            </ul>
                        </div>
                    </div>

                    <!-- Responsive Control Buttons -->
                    <div class="mb-3">
                        <div class="btn-group" role="group">
                            <button type="button" class="btn btn-outline-primary btn-sm" onclick="setViewport(1200)">
                                <i class="fas fa-desktop"></i> Desktop
                            </button>
                            <button type="button" class="btn btn-outline-primary btn-sm" onclick="setViewport(768)">
                                <i class="fas fa-tablet-alt"></i> Tablet
                            </button>
                            <button type="button" class="btn btn-outline-primary btn-sm" onclick="setViewport(576)">
                                <i class="fas fa-mobile-alt"></i> Mobile Large
                            </button>
                            <button type="button" class="btn btn-outline-primary btn-sm" onclick="setViewport(400)">
                                <i class="fas fa-mobile"></i> Mobile Small
                            </button>
                        </div>
                    </div>

                    <!-- Enhanced Table -->
                    <div class="table-responsive-enhanced">
                        <table class="table table-enhanced table-hover table-striped">
                            <thead class="sticky-header">
                                <tr>
                                    <th class="sortable" data-column="id">
                                        <div class="d-flex align-items-center">
                                            <i class="fas fa-hashtag text-primary me-2"></i>
                                            <strong>ID</strong>
                                            <i class="fas fa-sort ms-auto sort-icon"></i>
                                        </div>
                                    </th>
                                    <th class="sortable" data-column="name">
                                        <div class="d-flex align-items-center">
                                            <i class="fas fa-user text-primary me-2"></i>
                                            <strong>Tên</strong>
                                            <i class="fas fa-sort ms-auto sort-icon"></i>
                                        </div>
                                    </th>
                                    <th class="sortable hide-on-mobile" data-column="email">
                                        <div class="d-flex align-items-center">
                                            <i class="fas fa-envelope text-primary me-2"></i>
                                            <strong>Email</strong>
                                            <i class="fas fa-sort ms-auto sort-icon"></i>
                                        </div>
                                    </th>
                                    <th class="hide-on-tablet" data-column="phone">
                                        <div class="d-flex align-items-center">
                                            <i class="fas fa-phone text-primary me-2"></i>
                                            <strong>Điện thoại</strong>
                                        </div>
                                    </th>
                                    <th class="sortable" data-column="role">
                                        <div class="d-flex align-items-center">
                                            <i class="fas fa-user-tag text-primary me-2"></i>
                                            <strong>Vai trò</strong>
                                            <i class="fas fa-sort ms-auto sort-icon"></i>
                                        </div>
                                    </th>
                                    <th class="hide-on-tablet" data-column="created">
                                        <div class="d-flex align-items-center">
                                            <i class="fas fa-calendar text-primary me-2"></i>
                                            <strong>Ngày tạo</strong>
                                        </div>
                                    </th>
                                    <th>
                                        <div class="d-flex align-items-center">
                                            <i class="fas fa-cogs text-primary me-2"></i>
                                            <strong>Thao tác</strong>
                                        </div>
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                @for($i = 1; $i <= 15; $i++)
                                <tr>
                                    <td>
                                        <code class="bg-light px-2 py-1 rounded text-dark fw-bold">{{ str_pad($i, 3, '0', STR_PAD_LEFT) }}</code>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 32px; height: 32px;">
                                                <span class="text-white fw-bold">{{ chr(64 + $i) }}</span>
                                            </div>
                                            <span class="fw-bold text-dark">Người dùng {{ $i }}</span>
                                        </div>
                                    </td>
                                    <td class="hide-on-mobile">
                                        <span class="text-dark fw-medium">user{{ $i }}@example.com</span>
                                    </td>
                                    <td class="hide-on-tablet">
                                        <a href="tel:0123456789" class="text-decoration-none text-dark fw-medium">
                                            <i class="fas fa-phone-alt text-success me-1"></i>
                                            012 345 67{{ str_pad($i, 2, '0', STR_PAD_LEFT) }}
                                        </a>
                                    </td>
                                    <td>
                                        @php
                                            $roles = ['Admin', 'Agent', 'Owner', 'Customer'];
                                            $colors = ['danger', 'primary', 'success', 'info'];
                                            $roleIndex = ($i - 1) % 4;
                                        @endphp
                                        <span class="badge bg-{{ $colors[$roleIndex] }} fw-bold">
                                            {{ $roles[$roleIndex] }}
                                        </span>
                                    </td>
                                    <td class="hide-on-tablet">
                                        <small class="text-muted">
                                            <i class="fas fa-clock me-1"></i>
                                            {{ now()->subDays($i)->format('d/m/Y') }}
                                        </small>
                                    </td>
                                    <td>
                                        <div class="btn-group action-buttons-mobile" role="group">
                                            <button type="button" class="btn btn-outline-primary btn-sm" title="Xem chi tiết">
                                                <i class="fas fa-eye"></i>
                                                <span class="d-none d-md-inline ms-1">Xem</span>
                                            </button>
                                            <button type="button" class="btn btn-outline-warning btn-sm" title="Chỉnh sửa">
                                                <i class="fas fa-edit"></i>
                                                <span class="d-none d-md-inline ms-1">Sửa</span>
                                            </button>
                                            <button type="button" class="btn btn-outline-danger btn-sm" title="Xóa">
                                                <i class="fas fa-trash"></i>
                                                <span class="d-none d-md-inline ms-1">Xóa</span>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                @endfor
                            </tbody>
                        </table>
                    </div>

                    <!-- Table Stats -->
                    <div class="row mt-4">
                        <div class="col-md-6">
                            <div class="card border-0 bg-light">
                                <div class="card-body text-center py-3">
                                    <i class="fas fa-chart-bar text-primary fs-3 mb-2"></i>
                                    <h6 class="mb-1">Tổng số dòng</h6>
                                    <span class="fs-4 fw-bold text-primary">15</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card border-0 bg-light">
                                <div class="card-body text-center py-3">
                                    <i class="fas fa-columns text-success fs-3 mb-2"></i>
                                    <h6 class="mb-1">Số cột hiển thị</h6>
                                    <span class="fs-4 fw-bold text-success" id="visibleColumns">7</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Features List -->
                    <div class="mt-4">
                        <h5 class="mb-3">
                            <i class="fas fa-star text-warning me-2"></i>
                            Tính năng đã cải tiến
                        </h5>
                        <div class="row">
                            <div class="col-md-6">
                                <ul class="list-group list-group-flush">
                                    <li class="list-group-item border-0 px-0">
                                        <i class="fas fa-check text-success me-2"></i>
                                        Cuộn ngang mượt mà với custom scrollbar
                                    </li>
                                    <li class="list-group-item border-0 px-0">
                                        <i class="fas fa-check text-success me-2"></i>
                                        Header cố định khi cuộn dọc (sticky header)
                                    </li>
                                    <li class="list-group-item border-0 px-0">
                                        <i class="fas fa-check text-success me-2"></i>
                                        Sắp xếp theo cột với visual feedback
                                    </li>
                                    <li class="list-group-item border-0 px-0">
                                        <i class="fas fa-check text-success me-2"></i>
                                        Ẩn cột tự động theo kích thước màn hình
                                    </li>
                                </ul>
                            </div>
                            <div class="col-md-6">
                                <ul class="list-group list-group-flush">
                                    <li class="list-group-item border-0 px-0">
                                        <i class="fas fa-check text-success me-2"></i>
                                        Chuyển sang card layout ở mobile nhỏ
                                    </li>
                                    <li class="list-group-item border-0 px-0">
                                        <i class="fas fa-check text-success me-2"></i>
                                        Action buttons tối ưu cho touch device
                                    </li>
                                    <li class="list-group-item border-0 px-0">
                                        <i class="fas fa-check text-success me-2"></i>
                                        Hỗ trợ accessibility và keyboard navigation
                                    </li>
                                    <li class="list-group-item border-0 px-0">
                                        <i class="fas fa-check text-success me-2"></i>
                                        Scroll indicators và loading states
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Demo viewport simulation function
    function setViewport(width) {
        const currentWidth = window.innerWidth;
        
        if (width < currentWidth) {
            // Simulate smaller viewport by adding custom CSS
            let style = document.getElementById('viewport-simulation');
            if (!style) {
                style = document.createElement('style');
                style.id = 'viewport-simulation';
                document.head.appendChild(style);
            }
            
            style.textContent = `
                .container-fluid { max-width: ${width}px !important; margin: 0 auto; }
                .card { max-width: ${width}px !important; }
            `;
            
            // Update visible columns counter
            setTimeout(() => updateVisibleColumns(), 100);
            
            // Show notification
            showNotification(`Simulating ${width}px viewport`, 'info');
        } else {
            // Remove simulation
            const style = document.getElementById('viewport-simulation');
            if (style) style.remove();
            updateVisibleColumns();
            showNotification('Reset to normal viewport', 'success');
        }
    }
    
    function updateVisibleColumns() {
        const hiddenCols = document.querySelectorAll('.table-enhanced th.hide-on-mobile, .table-enhanced th.hide-on-tablet');
        const totalCols = document.querySelectorAll('.table-enhanced th').length;
        let visibleCols = totalCols;
        
        hiddenCols.forEach(col => {
            const computed = window.getComputedStyle(col);
            if (computed.display === 'none') {
                visibleCols--;
            }
        });
        
        document.getElementById('visibleColumns').textContent = visibleCols;
    }
    
    function showNotification(message, type = 'info') {
        const toast = document.createElement('div');
        toast.className = `alert alert-${type} alert-dismissible fade show position-fixed`;
        toast.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
        toast.innerHTML = `
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        
        document.body.appendChild(toast);
        
        setTimeout(() => {
            toast.remove();
        }, 3000);
    }
    
    // Initialize
    document.addEventListener('DOMContentLoaded', function() {
        updateVisibleColumns();
        
        // Listen for window resize to update counters
        window.addEventListener('resize', debounce(updateVisibleColumns, 250));
        
        // Add demo data refresh button functionality
        const refreshBtn = document.createElement('button');
        refreshBtn.className = 'btn btn-outline-secondary btn-sm me-2';
        refreshBtn.innerHTML = '<i class="fas fa-sync-alt"></i> Refresh Data';
        refreshBtn.onclick = () => {
            if (window.ResponsiveTableEnhancements) {
                window.ResponsiveTableEnhancements.showLoading(document.querySelector('.table-responsive-enhanced'));
                setTimeout(() => {
                    window.ResponsiveTableEnhancements.hideLoading(document.querySelector('.table-responsive-enhanced'));
                    showNotification('Data refreshed successfully!', 'success');
                }, 1500);
            }
        };
        
        // Add button to controls
        const controlGroup = document.querySelector('.btn-group');
        if (controlGroup && controlGroup.parentNode) {
            controlGroup.parentNode.insertBefore(refreshBtn, controlGroup);
        }
    });
    
    function debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }
</script>
@endsection
