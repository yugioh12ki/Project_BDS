@extends('_layout._layadmin.app')

@section('transaction')
@if(isset($error))
    <div class="fa fa-danger">
        {{ $error }}
    </div>
@else
<h1>Danh sách Giao dịch</h1>
<div class="action-buttons">
    <a href="{{-- route('users.create') --}}" class="btn btn-primary"><i class="fa fa-plus"></i> Thêm mới</a>
</div>
<div class="table-container">
    <table>
        <thead>
            <tr>
                @foreach ($columns as $column )
                    <th>{{ $column }}</th>
                @endforeach
                <th>Chức năng</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($transactions as $transaction)
                <tr>
                    @foreach ($columns as $column)
                            <td>{{ $transaction->$column }}</td>
                    @endforeach
                    <td>
                        <a href="{{--  route('users.edit', $user->UserID) --}}" class="action-icon"><i class="fas fa-edit"></i></a>
                        <a href="{{-- route('users.delete', $user->UserID) --}}" class="action-icon" onclick="return confirm('Bạn có chắc chắn muốn xóa?')"><i class="fas fa-trash"></i></a>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endif

@endsection
