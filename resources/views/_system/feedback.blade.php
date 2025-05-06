@extends('_layout._layadmin.app')
@section('feedback')
@if(isset($error))
    <div class="fa fa-danger">
        {{ $error }}
    </div>
@else
<h1>Danh sách Feedback</h1>
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
            @foreach ($feedbacks as $feedback)
                <tr>
                    @foreach ($columns as $column)
                            <td>{{ $feedback->$column }}</td>
                    @endforeach
                    <td>
                        <a href="{{--  route('users.edit', $user->UserID) --}}" class="action-icon"><i class="fas fa-edit"></i></a>
                        <a href="{{-- route('users.delete', $user->UserID) --}}" class="action-icon" onclick="return confirm('Bạn có chắc chắn muốn xóa?')"><i class="fas fa-trash"></i></a>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endif
@endSection()
