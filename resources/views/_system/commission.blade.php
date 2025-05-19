@extends('_layout._layadmin.app')
@section('commission')
@if(isset($error))
    <div class="fa fa-danger">
        {{ $error }}
    </div>
@else
<h1>Danh sách Hoa hồng</h1>

<div class="row mb-3">
    <div class="col-md-6">
        <div class="action-buttons">
            <!-- Button trigger modal -->
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createCommissionModal" data-commission-id="{{ $commission->CommissionID ?? '' }}">
                <i class="fa fa-plus"></i> Thêm mới
            </button>

            <!-- Modal -->
            <div class="modal fade" id="createCommissionModal" tabindex="-1" aria-labelledby="createCommissionModalLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="createCommissionModalLabel">Thêm mới Hoa hồng</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            @include('_system.partialview.create_commission', ['commission' => null, 'transactions' => $transactions ?? []])
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="d-flex justify-content-end" >

            <div class="search-box">
                <form action="{{ route('admin.commission.search') }}" method="GET" class="d-flex">
                    <input type="date" name="paid_date" class="form-control" placeholder="Tìm kiếm theo ngày thanh toán" value="{{ isset($paidDate) ? $paidDate : '' }}">
                    <button type="submit" class="btn btn-secondary ml-2"><i class="fa fa-search"></i></button>
                </form>
            </div>
        </div>
    </div>
</div>

<div id="commission-table-container" class="table-container">
    @include('_system.partialview.commission_table', ['commissions' => $commissions, 'columns' => $columns])
</div>


@endif
@endsection
