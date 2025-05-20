@extends('_layout._layadmin.app')

@section('user')




<h1>Danh sách User</h1>

<div class="d-flex justify-content-between align-items-center mb-3">

    {{-- Ô input và button tìm kiếm --}}
    <div class="d-flex align-items-center">
        <form id="search-form" action="{{ route('admin.users.search', ['role' => request()->route('role') ?? (isset($users[0]) ? $users[0]->Role : 'all')]) }}" method="GET" class="d-flex align-items-center w-100">
            <input type="text" name="keyword" id="search-input" class="form-control" placeholder="Nhập từ khóa tìm kiếm theo tên, email, số điện thoại..." style="width: 300px;" value="{{ request('keyword') }}">
            <button type="submit" class="btn btn-secondary ms-2" id="search-button">
                <i class="fa fa-search"></i> Tìm kiếm
            </button>
        </form>
    </div>
</div>

{{-- Button thêm mới --}}
<div style="margin-top: 20px">
    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addUserModal">
        <i class="fa fa-plus"></i> Thêm mới
    </button>
</div>




{{-- Modal thêm user --}}
<div class="modal fade"
    id="addUserModal"
    aria-hidden="true"
    aria-labelledby="addUserModalLabel"
    tabindex="-1" style="{{ session('showModal') ? 'display: block;' : '' }}">
    <div class="modal-dialog modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addUserModalLabel">Thêm User Mới</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">

                @include('_system.partialview.create_user', ['user' => null])
            </div>
        </div>
    </div>
</div>

{{-- Khu vực hiển thị danh sách user --}}
<div id="user-list">
    @if(isset($error))
    <div class="alert alert-danger">{{ $error }}</div>
    @else
    @include('_system.partialview.user_table', ['users' => $users, 'columns' => $columns])
    @endif
</div>

@if(session('showModal'))
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

@endsection
