@extends('_layout._layadmin.app')

@section('user')

<h1>Danh sách User</h1>

{{-- Combobox để chọn role --}}
<div class="action-buttons">
    <label for="role-select">Chọn Role:</label>
    <select id="role-select" class="form-control">
        <option value="all">Tất cả</option> {{-- Thêm tùy chọn "Tất cả" --}}
        <option value="Admin">Admin</option>
        <option value="Agent">Agent</option>
        <option value="Owner">Owner</option>
        <option value="Customer">Customer</option>
    </select>
</div>

{{-- Khu vực hiển thị danh sách user --}}
<div id="user-list">
    @include('_system.partialview.user_table', ['users' => $users, 'columns' => $columns])
</div>

<script>
    document.getElementById('role-select').addEventListener('change', function () {
        const role = this.value;

        // Gửi yêu cầu AJAX để lấy danh sách user theo role
        fetch(`/admin/user/role/${role}`)
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
@endsection
