@extends('_layout._layadmin.app')

@section('user')




<h1>Danh sách User</h1>

<div class="d-flex justify-content-between align-items-center mb-3">
    {{-- Combobox chọn role --}}
    <div class="d-flex align-items-center">
        <label for="role-select" class="me-2">Lọc danh sách:</label>
        <select id="role-select" class="form-control" style="width: 200px;">
            <option value="all">Tất cả</option>
            <option value="Admin">Admin</option>
            <option value="Agent">Agent</option>
            <option value="Owner">Owner</option>
            <option value="Customer">Customer</option>
            <option value="Active">Hoạt động</option>
            <option value="Inactive">Ngừng hoạt động</option>
        </select>
    </div>

    {{-- Ô input và button tìm kiếm --}}
    <div class="d-flex align-items-center">
        <form action="{{ route('admin.users.search') }}" method="GET" class="d-flex align-items-center">
            <input type="text" name="keyword" id="search-input" class="form-control" placeholder="Nhập từ khóa tìm kiếm" style="width: 300px;">
            <button type="submit" class="btn btn-secondary ms-2">
                <i class="fa fa-search"></i> Tìm kiếm
            </button>
        </form>
    </div>
</div>

{{-- Button thêm mới --}}
<button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addUserModal">
    <i class="fa fa-plus"></i> Thêm mới
</button>



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

<script>
    document.getElementById('role-select').addEventListener('change', function () {
        const value = this.value;

        let url = '';
        if (value === 'Active' || value === 'Inactive') {
            url = `/admin/user/status/${value}`;
        } else {
            url = `/admin/user/role/${value}`;
        }

        fetch(url)
            .then(response => {
                if (!response.ok) {
                    throw new Error('Không tìm thấy dữ liệu.');
                }
                return response.text();
            })
            .then(html => {
                document.getElementById('user-list').innerHTML = html;
            })
            .catch(error => {
                console.error('Error:', error);
                document.getElementById('user-list').innerHTML = `
                    <div class="alert alert-danger">Không tìm thấy user nào.</div>
                `;
            });
    });

    document.querySelectorAll('.column-checkbox').forEach(checkbox => {
    checkbox.addEventListener('change', function () {
        const selectedColumns = Array.from(document.querySelectorAll('.column-checkbox:checked'))
            .map(cb => cb.value);

        const role = document.getElementById('role-select').value || 'all'; // Lấy role hiện tại

        // Gửi yêu cầu AJAX để cập nhật danh sách user
        fetch(`/admin/user/role/${role}?columns=${selectedColumns.join(',')}`)
            .then(response => {
                if (!response.ok) {
                    throw new Error('Không tìm thấy dữ liệu.');
                }
                return response.text();
            })
            .then(html => {
                document.getElementById('user-list').innerHTML = html;
            })
            .catch(error => {
                console.error('Error:', error);
                document.getElementById('user-list').innerHTML = `
                    <div class="alert alert-danger">Không tìm thấy user nào.</div>
                `;
            });
        });
    });

    document.getElementById('search-button').addEventListener('click', function () {
        const keyword = document.getElementById('search-input').value;


        fetch(`/admin/user/search?keyword=${encodeURIComponent(keyword)}`)
            .then(response => {
                if (!response.ok) {
                    throw new Error('Không tìm thấy dữ liệu.');
                }
                return response.text();
            })
            .then(html => {
                document.getElementById('user-list').innerHTML = html;
            })
            .catch(error => {
                console.error('Error:', error);
                document.getElementById('user-list').innerHTML = `
                    <div class="alert alert-danger">Không tìm thấy user nào.</div>
                `;
            });
    });


</script>
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
