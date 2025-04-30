@extends('_layout._layadmin.app')

@section('user')
<h1>Danh sách User</h1>
<div class="table-container">
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Tên</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Addres</th>
                <th>Role</th>
                <th>Chức năng</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($users as $user)
                <tr>
                    <td>{{ $user->UserID }}</td>
                    <td>{{ $user->Name }}</td>
                    <td>{{ $user->Email }}</td>
                    <td>{{ $user->Phone }}</td>
                    <td>{{ $user->Address }}</td>
                    <td>{{ $user->Role }}</td>
                    <td>
                        <a href="{{--  route('users.edit', $user->UserID) --}}" class="action-icon"><i class="fas fa-edit"></i></a>
                        <a href="{{-- route('users.delete', $user->UserID) --}}" class="action-icon" onclick="return confirm('Bạn có chắc chắn muốn xóa?')"><i class="fas fa-trash"></i></a>
                    </td>
                </tr>
            @endforeach
            <tr>
                <td>a</td>
                <td>b</td>
                <td>c</td>
                <td>d</td>
                <td>e</td>
                <td>f</td>
                <td>
                    <a href="#" class="action-icon"><i class="fas fa-edit"></i></a>
                    <a href="#" class="action-icon"><i class="fas fa-trash"></i></a>
                    <a href="#" class="action-icon"><i class="fas fa-eye"></i></a>
                </td>
            </tr>
        </tbody>
    </table>
</div>
@endsection
