@extends('_layout._layadmin.app')

@section('user')




<div class="users-container">
    <h1 class="users-title">Danh sách User</h1>

    {{-- Header với Tìm kiếm bên trái và Thêm mới bên phải --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        {{-- Tìm kiếm bên trái --}}
        <div class="flex-grow-1 me-3">
            <form id="search-form" action="{{ route('admin.users.search', ['role' => request()->route('role') ?? (isset($users[0]) ? $users[0]->Role : 'all')]) }}" method="GET" class="d-flex align-items-center">
                <div class="input-group" style="max-width: 400px;">
                    <input type="text" name="keyword" id="search-input" class="form-control" placeholder="Tìm kiếm theo tên, email, số điện thoại..." value="{{ request('keyword') }}">
                    <button type="submit" class="btn btn-outline-secondary" id="search-button">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </form>
        </div>

        {{-- Button thêm mới bên phải --}}
        <div class="flex-shrink-0">
            <button type="button" class="btn btn-success btn-lg" data-bs-toggle="modal" data-bs-target="#addUserModal">
                <i class="fas fa-plus me-2"></i>Thêm người dùng mới
            </button>
        </div>
    </div>




{{-- Modal thêm user với profile management --}}
<div class="modal fade" id="addUserModal" aria-hidden="true" aria-labelledby="addUserModalLabel" tabindex="-1">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header bg-gradient-primary text-white">
                <h5 class="modal-title" id="addUserModalLabel">
                    <i class="fas fa-user-plus me-2"></i>
                    Tạo tài khoản mới
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                @include('_system.partialview.create_user_new', ['user' => null])
            </div>
        </div>
    </div>
</div>

{{-- Khu vực hiển thị danh sách user --}}
<div id="user-list">
    @if(isset($error))
    <div class="alert alert-danger">{{ $error }}</div>
    @else
    @if(isset($error))
    <div class="alert alert-danger">{{ $error }}</div>
    @else
    @include('_system.partialview.user_table', ['users' => $users, 'columns' => $columns])
    @endif
    @endif
</div>

@if(session('showModal') || request('showCreateModal'))
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const myModal = new bootstrap.Modal(document.getElementById('addUserModal'));
        myModal.show();
    });
</script>
@endif

@if(session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger">
        {{ session('error') }}
    </div>
@endif

{{-- Include CSS and JavaScript for enhanced user management --}}
@push('styles')
<style>
.users-container {
    padding: 1.5rem;
}

.users-title {
    color: #2c3e50;
    font-weight: 600;
    margin-bottom: 2rem;
    border-bottom: 2px solid #3498db;
    padding-bottom: 0.5rem;
}

.users-search-input {
    min-width: 300px;
    border-radius: 0.375rem;
    border: 1px solid #dee2e6;
    transition: all 0.3s ease;
}

.users-search-input:focus {
    border-color: #3498db;
    box-shadow: 0 0 0 0.2rem rgba(52, 152, 219, 0.25);
}

.users-table-container {
    margin-bottom: 1rem;
}

.user-modal-visible {
    display: block !important;
}
</style>
@endpush

@push('scripts')
<script src="{{ asset('js/user-management.js') }}"></script>
<script>
// Initialize user management functionality
document.addEventListener('DOMContentLoaded', function() {
    // Enhanced search functionality
    const searchForm = document.getElementById('search-form');
    const searchInput = document.getElementById('search-input');

    if (searchInput) {
        // Replace default search with our enhanced search
        searchInput.addEventListener('input', function() {
            // Use the enhanced search from user-management.js
            const searchValue = this.value;
            document.getElementById('searchUsers').value = searchValue;
            debounceSearch();
        });
    }

    // Handle form submission to prevent page reload
    if (searchForm) {
        searchForm.addEventListener('submit', function(e) {
            e.preventDefault();
            searchUsers();
        });
    }
});
</script>
@endpush

@endsection
