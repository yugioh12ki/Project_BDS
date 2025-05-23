@extends('_layout._layadmin.app')

@section('transaction')
@if(isset($error))
    <div class="fa fa-danger">
        {{ $error }}
    </div>
@else
<div class="transaction-container">
    <h1 class="transaction-title">Danh sách Giao dịch</h1>
    <div class="action-buttons">
        {{-- Tìm kiếm theo tên môi giới --}}
        <div class="d-flex justify-content-end">
            <form action="{{--  --}}" method="GET" class="d-flex">
                <input type="text" name="keyword" id="search-input" class="form-control transaction-search-input" placeholder="Nhập từ khóa tìm kiếm">
                <button type="submit" class="btn btn-secondary ms-2">
                    <i class="fa fa-search"></i> Tìm kiếm
                </button>
            </form>
        </div>




</div>


@include('_system.partialview.trans_table', ['transactions' => $transactions, 'columns' => $columns])
@endif

@endsection
