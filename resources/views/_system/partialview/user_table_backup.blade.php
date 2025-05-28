@if ($errors->any())
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-triangle me-2"></i>
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

{{-- Thống kê nhanh --}}
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card bg-primary text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h6 class="card-title">Tổng số User</h6>
                        <h3 class="mb-0">{{ $users->count() }}</h3>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-users fa-2x opacity-75"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-success text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h6 class="card-title">Đang hoạt động</h6>
                        <h3 class="mb-0">{{ $users->filter(fn($u) => strtolower($u->StatusUser) === 'active')->count() }}</h3>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-user-check fa-2x opacity-75"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-warning text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h6 class="card-title">Ngừng hoạt động</h6>
                        <h3 class="mb-0">{{ $users->filter(fn($u) => strtolower($u->StatusUser) === 'inactive')->count() }}</h3>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-user-times fa-2x opacity-75"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-info text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h6 class="card-title">Role hiện tại</h6>
                        <h3 class="mb-0">{{ ucfirst($users->first()->Role ?? 'Tất cả') }}</h3>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-user-tag fa-2x opacity-75"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Search and Filter Bar --}}
<div class="row mb-4">
    <div class="col-md-8">
        <div class="input-group">
            <span class="input-group-text bg-info text-white">
                <i class="fas fa-search"></i>
            </span>
            <input type="text" 
                   id="searchUsers" 
                   class="form-control" 
                   placeholder="Tìm kiếm theo tên, email hoặc số điện thoại..." 
                   value="{{ request('keyword') }}"
                   onkeyup="debounceSearch()">
        </div>
    </div>
    <div class="col-md-2">
        <select id="roleFilter" class="form-select" onchange="searchUsers()">
            <option value="all" {{ request('role') === 'all' ? 'selected' : '' }}>Tất cả role</option>
            <option value="Admin" {{ request('role') === 'Admin' ? 'selected' : '' }}>Admin</option>
            <option value="Agent" {{ request('role') === 'Agent' ? 'selected' : '' }}>Agent</option>
            <option value="Owner" {{ request('role') === 'Owner' ? 'selected' : '' }}>Owner</option>
            <option value="Customer" {{ request('role') === 'Customer' ? 'selected' : '' }}>Customer</option>
        </select>
    </div>
    <div class="col-md-2">
        <div class="btn-group w-100" role="group">
            <button type="button" class="btn btn-outline-success" onclick="createUserByRole('Agent')" title="Tạo Agent">
                <i class="fas fa-user-tie"></i>
            </button>
            <button type="button" class="btn btn-outline-info" onclick="createUserByRole('Owner')" title="Tạo Owner">
                <i class="fas fa-home"></i>
            </button>
            <button type="button" class="btn btn-outline-secondary" onclick="createUserByRole('Customer')" title="Tạo Customer">
                <i class="fas fa-user-tag"></i>
            </button>
        </div>
    </div>
</div>

{{-- Tabs cho Active/Inactive --}}
<div class="card shadow-sm">
    <div class="card-header bg-white">
        <ul class="nav nav-tabs card-header-tabs" id="userTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="active-tab" data-bs-toggle="tab" data-bs-target="#active-users" type="button" role="tab">
                    <i class="fas fa-user-check text-success me-2"></i>
                    Đang hoạt động
                    <span class="badge bg-success ms-1">{{ $users->filter(fn($u) => strtolower($u->StatusUser) === 'active')->count() }}</span>
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="inactive-tab" data-bs-toggle="tab" data-bs-target="#inactive-users" type="button" role="tab">
                    <i class="fas fa-user-times text-warning me-2"></i>
                    Ngừng hoạt động
                    <span class="badge bg-warning ms-1">{{ $users->filter(fn($u) => strtolower($u->StatusUser) === 'inactive')->count() }}</span>
                </button>
            </li>
        </ul>
    </div>

    <div class="card-body">
        <div class="tab-content" id="userTabsContent">
            {{-- Tab Active Users --}}
            <div class="tab-pane fade show active" id="active-users" role="tabpanel">
                @php
                    $activeUsers = $users->filter(fn($u) => strtolower($u->StatusUser) === 'active');
                @endphp
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th scope="col">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-id-card text-primary me-2"></i>
                                        User ID
                                    </div>
                                </th>
                                <th scope="col">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-user text-primary me-2"></i>
                                        Họ tên
                                    </div>
                                </th>
                                <th scope="col">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-envelope text-primary me-2"></i>
                                        Email
                                    </div>
                                </th>
                                <th scope="col">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-phone text-primary me-2"></i>
                                        Điện thoại
                                    </div>
                                </th>
                                <th scope="col">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-user-tag text-primary me-2"></i>
                                        Role
                                    </div>
                                </th>
                                <th scope="col" style="width: 150px;">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-cogs text-primary me-2"></i>
                                        Thao tác
                                    </div>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($activeUsers as $user)
                                <tr class="user-row" data-user-id="{{ $user->UserID }}" data-status="active">
                                    <td>
                                        <code class="bg-light px-2 py-1 rounded">{{ $user->UserID }}</code>
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
                                            <span class="fw-medium">{{ $user->Name }}</span>
                                        </div>
                                    </td>
                                    <td>{{ $user->Email }}</td>
                                    <td>
                                        @if($user->Phone)
                                            <a href="tel:{{ $user->Phone }}" class="text-decoration-none">
                                                <i class="fas fa-phone-alt text-success me-1"></i>
                                                {{ $user->Phone }}
                                            </a>
                                        @else
                                            <span class="text-muted">Chưa có</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $user->Role === 'Admin' ? 'danger' : ($user->Role === 'Agent' ? 'primary' : ($user->Role === 'Owner' ? 'success' : 'secondary')) }}">
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
                                            <button type="button" class="btn btn-outline-danger btn-sm"
                                                    onclick="toggleUserStatus('{{ $user->UserID }}', '{{ strtolower($user->StatusUser) }}')"
                                                    title="Vô hiệu hóa">
                                                <i class="fas fa-user-times"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4">
                                        <div class="text-muted">
                                            <i class="fas fa-users fa-3x mb-3"></i>
                                            <h5>Không có user nào đang hoạt động</h5>
                                            <p>Hãy thêm user mới hoặc kích hoạt user đã tồn tại.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Tab Inactive Users --}}
            <div class="tab-pane fade" id="inactive-users" role="tabpanel">
                @php
                    $inactiveUsers = $users->filter(fn($u) => strtolower($u->StatusUser) === 'inactive');
                @endphp
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th scope="col">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-id-card text-warning me-2"></i>
                                        User ID
                                    </div>
                                </th>
                                <th scope="col">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-user text-warning me-2"></i>
                                        Họ tên
                                    </div>
                                </th>
                                <th scope="col">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-envelope text-warning me-2"></i>
                                        Email
                                    </div>
                                </th>
                                <th scope="col">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-phone text-warning me-2"></i>
                                        Điện thoại
                                    </div>
                                </th>
                                <th scope="col">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-user-tag text-warning me-2"></i>
                                        Role
                                    </div>
                                </th>
                                <th scope="col" style="width: 150px;">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-cogs text-warning me-2"></i>
                                        Thao tác
                                    </div>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($inactiveUsers as $user)
                                <tr class="user-row opacity-75" data-user-id="{{ $user->UserID }}" data-status="inactive">
                                    <td>
                                        <code class="bg-light px-2 py-1 rounded">{{ $user->UserID }}</code>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            @if($user->Avatar)
                                                <img src="{{ asset('storage/' . $user->Avatar) }}" alt="Avatar" class="rounded-circle me-2" width="32" height="32">
                                            @else
                                                <div class="bg-secondary rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 32px; height: 32px;">
                                                    <span class="text-white fw-bold">{{ strtoupper(substr($user->Name, 0, 1)) }}</span>
                                                </div>
                                            @endif
                                            <span class="fw-medium">{{ $user->Name }}</span>
                                        </div>
                                    </td>
                                    <td>{{ $user->Email }}</td>
                                    <td>
                                        @if($user->Phone)
                                            <span class="text-muted">{{ $user->Phone }}</span>
                                        @else
                                            <span class="text-muted">Chưa có</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge bg-secondary">
                                            {{ $user->Role }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <button type="button" class="btn btn-outline-secondary btn-sm"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#viewModal{{ $user->UserID }}"
                                                    title="Xem chi tiết">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            <button type="button" class="btn btn-outline-success btn-sm"
                                            <button type="button" class="btn btn-outline-success btn-sm"
                                                    onclick="toggleUserStatus('{{ $user->UserID }}', '{{ strtolower($user->StatusUser) }}')"
                                                    title="Kích hoạt lại">
                                                <i class="fas fa-user-check"></i>
                                            </button>ype="button" class="btn btn-outline-danger btn-sm"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#deleteModal{{ $user->UserID }}"
                                                    title="Xóa vĩnh viễn">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4">
                                        <div class="text-muted">
                                            <i class="fas fa-check-circle fa-3x mb-3 text-success"></i>
                                            <h5>Tuyệt vời! Không có user nào bị vô hiệu hóa</h5>
                                            <p>Tất cả user đều đang hoạt động bình thường.</p>
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
    <div class="pagination-wrapper mt-3">
        {{ $users->appends(request()->except('page'))->links() }}
    </div>
@endif

{{-- Search and Filter Bar --}}
<div class="row mb-4">
    <div class="col-md-8">
        <div class="input-group">
            <span class="input-group-text bg-info text-white">
                <i class="fas fa-search"></i>
            </span>
            <input type="text" 
                   id="searchUsers" 
                   class="form-control" 
                   placeholder="Tìm kiếm theo tên, email hoặc số điện thoại..." 
                   value="{{ request('keyword') }}"
                   onkeyup="debounceSearch()">
        </div>
    </div>
    <div class="col-md-2">
        <select id="roleFilter" class="form-select" onchange="searchUsers()">
            <option value="all" {{ request('role') === 'all' ? 'selected' : '' }}>Tất cả role</option>
            <option value="Admin" {{ request('role') === 'Admin' ? 'selected' : '' }}>Admin</option>
            <option value="Agent" {{ request('role') === 'Agent' ? 'selected' : '' }}>Agent</option>
            <option value="Owner" {{ request('role') === 'Owner' ? 'selected' : '' }}>Owner</option>
            <option value="Customer" {{ request('role') === 'Customer' ? 'selected' : '' }}>Customer</option>
        </select>
    </div>
    <div class="col-md-2">
        <div class="btn-group w-100" role="group">
            <button type="button" class="btn btn-outline-success" onclick="createUserByRole('Agent')" title="Tạo Agent">
                <i class="fas fa-user-tie"></i>
            </button>
            <button type="button" class="btn btn-outline-info" onclick="createUserByRole('Owner')" title="Tạo Owner">
                <i class="fas fa-home"></i>
            </button>
            <button type="button" class="btn btn-outline-secondary" onclick="createUserByRole('Customer')" title="Tạo Customer">
                <i class="fas fa-user-tag"></i>
            </button>
        </div>
    </div>
</div>

{{-- Tất cả các modals cho tất cả users --}}
@foreach ($users as $user)
    @include('_system.partialview.user_modals_new', ['user' => $user])
@endforeach

<style>
/* Enhanced User Management Styling for Better Readability */
:root {
    --primary-color: #2c3e50;
    --secondary-color: #34495e;
    --success-color: #27ae60;
    --warning-color: #f39c12;
    --danger-color: #e74c3c;
    --info-color: #3498db;
    --light-bg: #f8f9fa;
    --dark-text: #2c3e50;
    --border-color: #dee2e6;
    --shadow: 0 2px 4px rgba(0,0,0,0.1);
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

/* Table Styling */
.table {
    margin-bottom: 0;
    color: var(--dark-text);
}

.table th {
    background-color: var(--light-bg);
    color: var(--dark-text);
    font-weight: 600;
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
}

.table tbody tr {
    transition: all 0.3s ease;
}

.table tbody tr:hover {
    background-color: #f8f9fa;
    transform: translateY(-1px);
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

/* Avatar Styling */
.avatar {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    object-fit: cover;
    border: 2px solid #ffffff;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

/* Badge Enhancements */
.badge {
    font-size: 0.75rem;
    font-weight: 500;
    padding: 0.375rem 0.75rem;
    border-radius: 0.375rem;
}

.badge.bg-success {
    background-color: var(--success-color) !important;
    color: white;
}

.badge.bg-warning {
    background-color: var(--warning-color) !important;
    color: white;
}

.badge.bg-danger {
    background-color: var(--danger-color) !important;
    color: white;
}

.badge.bg-info {
    background-color: var(--info-color) !important;
    color: white;
}

.badge.bg-secondary {
    background-color: var(--secondary-color) !important;
    color: white;
}

.badge.bg-primary {
    background-color: var(--primary-color) !important;
    color: white;
}

/* Button Enhancements */
.btn {
    border-radius: 0.375rem;
    font-weight: 500;
    transition: all 0.3s ease;
    border: 1px solid transparent;
}

.btn:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.15);
}

.btn-outline-success {
    color: var(--success-color);
    border-color: var(--success-color);
}

.btn-outline-success:hover {
    background-color: var(--success-color);
    border-color: var(--success-color);
}

.btn-outline-info {
    color: var(--info-color);
    border-color: var(--info-color);
}

.btn-outline-info:hover {
    background-color: var(--info-color);
    border-color: var(--info-color);
}

.btn-outline-warning {
    color: var(--warning-color);
    border-color: var(--warning-color);
}

.btn-outline-warning:hover {
    background-color: var(--warning-color);
    border-color: var(--warning-color);
}

.btn-outline-danger {
    color: var(--danger-color);
    border-color: var(--danger-color);
}

.btn-outline-danger:hover {
    background-color: var(--danger-color);
    border-color: var(--danger-color);
}

/* Tab Styling */
.nav-tabs .nav-link {
    color: var(--dark-text);
    border: 1px solid transparent;
    border-radius: 0.375rem 0.375rem 0 0;
    font-weight: 500;
    padding: 0.75rem 1.5rem;
    transition: all 0.3s ease;
}

.nav-tabs .nav-link:hover {
    border-color: var(--border-color);
    background-color: var(--light-bg);
}

.nav-tabs .nav-link.active {
    color: var(--primary-color);
    background-color: #ffffff;
    border-color: var(--border-color) var(--border-color) #ffffff;
    font-weight: 600;
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
    font-weight: 500;
}

.form-control, .form-select {
    border: none;
    padding: 0.75rem 1rem;
    font-size: 0.875rem;
    transition: all 0.3s ease;
}

.form-control:focus, .form-select:focus {
    box-shadow: 0 0 0 0.2rem rgba(52, 152, 219, 0.25);
    border-color: var(--info-color);
}

/* Alert Styling */
.alert {
    border: none;
    border-radius: 0.5rem;
    font-weight: 500;
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
}

/* Loading and Animation States */
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

/* Status-specific Row Styling */
tr[data-status="active"] {
    border-left: 3px solid var(--success-color);
}

tr[data-status="inactive"] {
    border-left: 3px solid var(--warning-color);
    background-color: rgba(243, 156, 18, 0.05);
}

/* Action Button Group */
.action-buttons .btn {
    margin: 0 0.125rem;
    padding: 0.375rem 0.5rem;
}

.action-buttons .btn i {
    font-size: 0.875rem;
}

/* Empty State Styling */
.empty-state {
    text-align: center;
    padding: 3rem 1rem;
    color: #6c757d;
}

.empty-state i {
    font-size: 3rem;
    margin-bottom: 1rem;
    opacity: 0.5;
}

/* Improved Text Contrast */
.text-muted {
    color: #6c757d !important;
}

.text-dark {
    color: var(--dark-text) !important;
}

/* Enhanced Form Controls */
.form-control::placeholder {
    color: #adb5bd;
    font-style: italic;
}

/* Improved Pagination */
.pagination {
    margin-bottom: 0;
}

.page-link {
    color: var(--primary-color);
    border-color: var(--border-color);
    padding: 0.5rem 0.75rem;
}

.page-link:hover {
    color: white;
    background-color: var(--primary-color);
    border-color: var(--primary-color);
}

.page-item.active .page-link {
    background-color: var(--primary-color);
    border-color: var(--primary-color);
}
</style>


