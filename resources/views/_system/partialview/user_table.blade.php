{{-- Tabs cho tài khoản hoạt động và không hoạt động --}}
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white border-0 pb-0">
        <ul class="nav nav-tabs" id="userTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="active-users-tab" data-bs-toggle="tab" data-bs-target="#active-users" type="button" role="tab" aria-controls="active-users" aria-selected="true">
                    <i class="fas fa-user-check text-success me-2"></i>
                    <strong>Tài khoản hoạt động</strong>
                    <span class="badge bg-success ms-2">{{ $users->filter(function($user) { return $user->StatusUser == 'active'; })->count() }}</span>
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="inactive-users-tab" data-bs-toggle="tab" data-bs-target="#inactive-users" type="button" role="tab" aria-controls="inactive-users" aria-selected="false">
                    <i class="fas fa-user-times text-warning me-2"></i>
                    <strong>Tài khoản không hoạt động</strong>
                    <span class="badge bg-warning ms-2">{{ $users->filter(function($user) { return $user->StatusUser == 'inactive'; })->count() }}</span>
                </button>
            </li>
        </ul>
    </div>

    <div class="card-body p-0">
        <div class="tab-content" id="userTabsContent">
            {{-- Tab tài khoản hoạt động --}}
            <div class="tab-pane fade show active" id="active-users" role="tabpanel" aria-labelledby="active-users-tab">
                <div class="table-responsive-enhanced">
                    <table class="table table-enhanced table-hover align-middle mb-0">
                        <thead class="table-light sticky-header">
                            <tr>
                                <th scope="col" class="sortable" data-column="UserID">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-id-card text-primary me-2"></i>
                                        <strong>User ID</strong>
                                        <i class="fas fa-sort ms-auto sort-icon"></i>
                                    </div>
                                </th>
                                <th scope="col" class="sortable" data-column="Name">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-user text-primary me-2"></i>
                                        <strong>Họ tên</strong>
                                        <i class="fas fa-sort ms-auto sort-icon"></i>
                                    </div>
                                </th>
                                <th scope="col" class="sortable hide-on-mobile" data-column="Email">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-envelope text-primary me-2"></i>
                                        <strong>Email</strong>
                                        <i class="fas fa-sort ms-auto sort-icon"></i>
                                    </div>
                                </th>
                                <th scope="col" class="hide-on-tablet" data-column="Phone">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-phone text-primary me-2"></i>
                                        <strong>Điện thoại</strong>
                                    </div>
                                </th>
                                <th scope="col" class="sortable" data-column="Role">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-user-tag text-primary me-2"></i>
                                        <strong>Role</strong>
                                        <i class="fas fa-sort ms-auto sort-icon"></i>
                                    </div>
                                </th>
                                <th scope="col" style="width: 150px;">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-cogs text-primary me-2"></i>
                                        <strong>Thao tác</strong>
                                    </div>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($users->filter(function($user) { return $user->StatusUser == 'active'; }) as $user)
                                <tr class="user-row border-start border-success border-3" data-user-id="{{ $user->UserID }}" data-status="active">
                                    <td>
                                        <code class="bg-light px-2 py-1 rounded text-dark fw-bold">{{ $user->UserID }}</code>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            @if($user->Avatar)
                                                <img src="{{ asset('storage/' . $user->Avatar) }}" alt="Avatar" class="rounded-circle me-2" width="32" height="32">
                                            @else
                                                <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 32px; height: 32px;">
                                                    <span class="text-white fw-bold">{{ strtoupper(substr($user->Name, 0, 1)) }}</span>
                                                </div>
                                            @endif
                                            <span class="fw-bold text-dark">{{ $user->Name }}</span>
                                        </div>
                                    </td>
                                    <td class="hide-on-mobile"><span class="text-dark fw-medium">{{ $user->Email }}</span></td>
                                    <td class="hide-on-tablet">
                                        @if($user->Phone)
                                            <a href="tel:{{ $user->Phone }}" class="text-decoration-none text-dark fw-medium">
                                                <i class="fas fa-phone-alt text-success me-1"></i>
                                                {{ $user->Phone }}
                                            </a>
                                        @else
                                            <span class="text-muted fw-medium">Chưa có</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $user->Role === 'Admin' ? 'danger' : ($user->Role === 'Agent' ? 'primary' : ($user->Role === 'Owner' ? 'success' : 'secondary')) }} fw-bold">
                                            {{ $user->Role }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="btn-group action-buttons-mobile" role="group">
                                            <button type="button" class="btn btn-outline-primary btn-sm"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#viewModal{{ $user->UserID }}"
                                                    title="Xem chi tiết">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            <button type="button" class="btn btn-outline-warning btn-sm"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#editModal{{ $user->UserID }}"
                                                    title="Chỉnh sửa">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button type="button" class="btn btn-outline-danger btn-sm"
                                                    onclick="toggleUserStatus('{{ $user->UserID }}', 'active')"
                                                    title="Vô hiệu hóa">
                                                <i class="fas fa-user-times"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4">
                                        <div class="empty-state">
                                            <i class="fas fa-user-check fa-3x mb-3 text-success"></i>
                                            <h5 class="text-dark">Không có tài khoản hoạt động nào</h5>
                                            <p class="text-muted">Tất cả tài khoản hiện đang bị vô hiệu hóa.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Tab tài khoản không hoạt động --}}
            <div class="tab-pane fade" id="inactive-users" role="tabpanel" aria-labelledby="inactive-users-tab">
                <div class="table-responsive-enhanced">
                    <table class="table table-enhanced table-hover align-middle mb-0">
                        <thead class="table-light sticky-header">
                            <tr>
                                <th scope="col" class="sortable" data-column="UserID">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-id-card text-primary me-2"></i>
                                        <strong>User ID</strong>
                                        <i class="fas fa-sort ms-auto sort-icon"></i>
                                    </div>
                                </th>
                                <th scope="col" class="sortable" data-column="Name">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-user text-primary me-2"></i>
                                        <strong>Họ tên</strong>
                                        <i class="fas fa-sort ms-auto sort-icon"></i>
                                    </div>
                                </th>
                                <th scope="col" class="sortable hide-on-mobile" data-column="Email">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-envelope text-primary me-2"></i>
                                        <strong>Email</strong>
                                        <i class="fas fa-sort ms-auto sort-icon"></i>
                                    </div>
                                </th>
                                <th scope="col" class="hide-on-tablet" data-column="Phone">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-phone text-primary me-2"></i>
                                        <strong>Điện thoại</strong>
                                    </div>
                                </th>
                                <th scope="col" class="sortable" data-column="Role">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-user-tag text-primary me-2"></i>
                                        <strong>Role</strong>
                                        <i class="fas fa-sort ms-auto sort-icon"></i>
                                    </div>
                                </th>
                                <th scope="col" style="width: 150px;">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-cogs text-primary me-2"></i>
                                        <strong>Thao tác</strong>
                                    </div>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($users->filter(function($user) { return $user->StatusUser == 'inactive'; }) as $user)
                                <tr class="user-row border-start border-warning border-3 bg-light" data-user-id="{{ $user->UserID }}" data-status="inactive">
                                    <td>
                                        <code class="bg-secondary px-2 py-1 rounded text-white fw-bold">{{ $user->UserID }}</code>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            @if($user->Avatar)
                                                <img src="{{ asset('storage/' . $user->Avatar) }}" alt="Avatar" class="rounded-circle me-2 opacity-50" width="32" height="32">
                                            @else
                                                <div class="bg-secondary rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 32px; height: 32px;">
                                                    <span class="text-white fw-bold">{{ strtoupper(substr($user->Name, 0, 1)) }}</span>
                                                </div>
                                            @endif
                                            <span class="fw-bold text-muted">{{ $user->Name }}</span>
                                        </div>
                                    </td>
                                    <td><span class="text-muted fw-medium">{{ $user->Email }}</span></td>
                                    <td>
                                        @if($user->Phone)
                                            <span class="text-muted fw-medium">
                                                <i class="fas fa-phone-alt text-muted me-1"></i>
                                                {{ $user->Phone }}
                                            </span>
                                        @else
                                            <span class="text-muted fw-medium">Chưa có</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge bg-secondary fw-bold">
                                            {{ $user->Role }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="btn-group action-buttons" role="group">
                                            <button type="button" class="btn btn-outline-primary btn-sm"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#viewModal{{ $user->UserID }}"
                                                    title="Xem chi tiết">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            <button type="button" class="btn btn-outline-warning btn-sm"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#editModal{{ $user->UserID }}"
                                                    title="Chỉnh sửa">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button type="button" class="btn btn-outline-success btn-sm"
                                                    onclick="toggleUserStatus('{{ $user->UserID }}', 'inactive')"
                                                    title="Kích hoạt lại">
                                                <i class="fas fa-user-check"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4">
                                        <div class="empty-state">
                                            <i class="fas fa-user-times fa-3x mb-3 text-warning"></i>
                                            <h5 class="text-dark">Không có tài khoản bị vô hiệu hóa</h5>
                                            <p class="text-muted">Tất cả tài khoản đều đang hoạt động.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Phân trang --}}
@if ($users instanceof \Illuminate\Pagination\LengthAwarePaginator)
    <div class="pagination-wrapper mt-4 d-flex justify-content-center">
        {{ $users->appends(request()->except('page'))->links() }}
    </div>
@endif

{{-- Tất cả các modals cho tất cả users --}}
@foreach ($users as $user)
    @include('_system.partialview.user_modals_new', ['user' => $user])
@endforeach

<style>
/* Enhanced User Management Styling for Better Readability - Improved Font Contrast */
:root {
    --primary-color: #2c3e50;
    --secondary-color: #34495e;
    --success-color: #27ae60;
    --warning-color: #f39c12;
    --danger-color: #e74c3c;
    --info-color: #3498db;
    --light-bg: #f8f9fa;
    --dark-text: #1a1a1a;
    --border-color: #dee2e6;
    --shadow: 0 2px 4px rgba(0,0,0,0.1);
}

/* Enhanced Typography for Better Readability */
body {
    font-family: 'Inter', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    color: var(--dark-text);
    line-height: 1.6;
}

/* Table Text Improvements */
.table {
    margin-bottom: 0;
    color: var(--dark-text);
    font-size: 0.95rem;
}

.table th {
    background-color: var(--light-bg);
    color: var(--dark-text);
    font-weight: 700;
    border-top: none;
    border-bottom: 2px solid var(--border-color);
    padding: 1rem 0.75rem;
    font-size: 0.875rem;
    text-transform: uppercase;
    letter-spacing: 0.05em;
}

.table td {
    padding: 1rem 0.75rem;
    vertical-align: middle;
    border-top: 1px solid #f1f3f4;
    font-weight: 500;
    color: var(--dark-text);
}

.table tbody tr {
    transition: all 0.3s ease;
}

.table tbody tr:hover {
    background-color: rgba(52, 152, 219, 0.05);
    transform: translateY(-1px);
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

/* Text Weight and Contrast Improvements */
.fw-bold {
    font-weight: 700 !important;
    color: var(--dark-text) !important;
}

.fw-medium {
    font-weight: 600 !important;
    color: var(--dark-text) !important;
}

.text-dark {
    color: var(--dark-text) !important;
    font-weight: 500;
}

/* Enhanced Badge Styling */
.badge {
    font-size: 0.8rem;
    font-weight: 700;
    padding: 0.4rem 0.8rem;
    border-radius: 0.375rem;
    text-transform: uppercase;
    letter-spacing: 0.025em;
}

.badge.bg-success {
    background-color: var(--success-color) !important;
    color: white !important;
    box-shadow: 0 2px 4px rgba(39, 174, 96, 0.3);
}

.badge.bg-warning {
    background-color: var(--warning-color) !important;
    color: white !important;
    box-shadow: 0 2px 4px rgba(243, 156, 18, 0.3);
}

.badge.bg-danger {
    background-color: var(--danger-color) !important;
    color: white !important;
    box-shadow: 0 2px 4px rgba(231, 76, 60, 0.3);
}

.badge.bg-info {
    background-color: var(--info-color) !important;
    color: white !important;
    box-shadow: 0 2px 4px rgba(52, 152, 219, 0.3);
}

.badge.bg-secondary {
    background-color: var(--secondary-color) !important;
    color: white !important;
    box-shadow: 0 2px 4px rgba(52, 73, 94, 0.3);
}

.badge.bg-primary {
    background-color: var(--primary-color) !important;
    color: white !important;
    box-shadow: 0 2px 4px rgba(44, 62, 80, 0.3);
}

/* Card Enhancements */
.card {
    border: none;
    box-shadow: var(--shadow);
    margin-bottom: 1.5rem;
}

.card-header {
    background: linear-gradient(135deg, var(--light-bg) 0%, #ffffff 100%);
    border-bottom: 1px solid var(--border-color);
    padding: 1rem 1.5rem;
}

/* Statistics Cards */
.card.bg-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
}

.card.bg-success {
    background: linear-gradient(135deg, var(--success-color) 0%, #2ecc71 100%) !important;
}

.card.bg-warning {
    background: linear-gradient(135deg, var(--warning-color) 0%, #e67e22 100%) !important;
}

.card.bg-info {
    background: linear-gradient(135deg, var(--info-color) 0%, #5dade2 100%) !important;
}

/* Button Enhancements */
.btn {
    border-radius: 0.375rem;
    font-weight: 600;
    transition: all 0.3s ease;
    border: 1px solid transparent;
}

.btn:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.15);
}

/* Tab Styling */
.nav-tabs {
    border-bottom: 2px solid var(--border-color);
    margin-bottom: 0;
}

.nav-tabs .nav-link {
    color: var(--dark-text);
    border: none;
    border-radius: 0.375rem 0.375rem 0 0;
    font-weight: 600;
    padding: 1rem 1.5rem;
    transition: all 0.3s ease;
    background: transparent;
    position: relative;
    margin-right: 0.25rem;
}

.nav-tabs .nav-link:hover {
    background-color: rgba(52, 152, 219, 0.1);
    color: var(--info-color);
    transform: translateY(-2px);
}

.nav-tabs .nav-link.active {
    color: var(--primary-color);
    background-color: #ffffff;
    border-bottom: 3px solid var(--info-color);
    font-weight: 700;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.nav-tabs .nav-link.active::after {
    content: '';
    position: absolute;
    bottom: -2px;
    left: 0;
    right: 0;
    height: 3px;
    background: linear-gradient(135deg, var(--info-color) 0%, var(--primary-color) 100%);
    border-radius: 3px 3px 0 0;
}

/* Tab Content */
.tab-content {
    background: #ffffff;
    border-radius: 0 0 0.375rem 0.375rem;
}

.tab-pane {
    min-height: 300px;
}

/* Inactive users styling */
tr[data-status="inactive"] {
    background-color: rgba(248, 249, 250, 0.8);
}

tr[data-status="inactive"] .text-muted {
    color: #6c757d !important;
}

tr[data-status="inactive"]:hover {
    background-color: rgba(243, 156, 18, 0.05) !important;
}

/* Search Bar Styling */
.input-group {
    box-shadow: var(--shadow);
    border-radius: 0.375rem;
    overflow: hidden;
}

.input-group-text {
    background: linear-gradient(135deg, var(--info-color) 0%, #5dade2 100%);
    border: none;
    color: white;
    font-weight: 600;
}

.form-control, .form-select {
    border: none;
    padding: 0.75rem 1rem;
    font-size: 0.95rem;
    font-weight: 500;
    color: var(--dark-text);
    transition: all 0.3s ease;
}

.form-control:focus, .form-select:focus {
    box-shadow: 0 0 0 0.2rem rgba(52, 152, 219, 0.25);
    border-color: var(--info-color);
}

/* Status-specific Row Styling */
tr[data-status="active"] {
    border-left: 3px solid var(--success-color);
}

tr[data-status="inactive"] {
    border-left: 3px solid var(--warning-color);
    background-color: rgba(243, 156, 18, 0.03);
}

/* Action Button Group */
.action-buttons .btn {
    margin: 0 0.125rem;
    padding: 0.375rem 0.5rem;
    font-weight: 600;
}

.action-buttons .btn i {
    font-size: 0.875rem;
}

/* Empty State Styling */
.empty-state {
    text-align: center;
    padding: 3rem 1rem;
}

.empty-state i {
    font-size: 3rem;
    margin-bottom: 1rem;
    opacity: 0.5;
}

.empty-state h5 {
    color: var(--dark-text);
    font-weight: 700;
}

.empty-state p {
    color: #6c757d;
    font-weight: 500;
}

/* Enhanced Code Styling */
code {
    font-weight: 700 !important;
    font-size: 0.875rem;
    padding: 0.25rem 0.5rem;
}

/* Responsive Adjustments */
@media (max-width: 768px) {
    .table-responsive {
        border-radius: 0.375rem;
        box-shadow: var(--shadow);
    }

    .card-header {
        padding: 0.75rem 1rem;
    }

    .btn-group .btn {
        padding: 0.5rem 0.75rem;
        font-size: 0.875rem;
    }

    .nav-tabs .nav-link {
        padding: 0.5rem 1rem;
        font-size: 0.875rem;
    }

    .table th, .table td {
        font-size: 0.875rem;
        padding: 0.75rem 0.5rem;
    }
}

/* Loading States */
.loading {
    opacity: 0.6;
    pointer-events: none;
}

.fade-in {
    animation: fadeIn 0.5s ease-in;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
}

/* Enhanced Form Controls */
.form-control::placeholder {
    color: #6c757d;
    font-style: italic;
    font-weight: 400;
}

/* Improved Text Contrast for Muted Text */
.text-muted {
    color: #495057 !important;
    font-weight: 500;
}

/* Alert Styling */
.alert {
    border: none;
    border-radius: 0.5rem;
    font-weight: 600;
    box-shadow: var(--shadow);
}

.alert-danger {
    background: linear-gradient(135deg, #ff6b6b 0%, var(--danger-color) 100%);
    color: white;
}

.alert-success {
    background: linear-gradient(135deg, #51cf66 0%, var(--success-color) 100%);
    color: white;
}

.alert-warning {
    background: linear-gradient(135deg, #ffd43b 0%, var(--warning-color) 100%);
    color: white;
}

.alert-info {
    background: linear-gradient(135deg, #74c0fc 0%, var(--info-color) 100%);
    color: white;
}
</style>
