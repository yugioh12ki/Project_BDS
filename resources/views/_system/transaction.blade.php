@extends('_layout._layadmin.app')

@section('transaction')
@if(isset($error))
    <div class="fa fa-danger">
        {{ $error }}
    </div>
@else
<h1>Danh sách Giao dịch</h1>
<div class="action-buttons">
    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addTransactionModal">
        <i class="fa fa-plus"></i> Thêm mới
    </button>

    <!-- Modal -->
    <div class="modal fade" id="addTransactionModal" tabindex="-1" aria-labelledby="addTransactionModalLabel" aria-hidden="true">
        <div class="modal-dialog" style="max-width: 90%;">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addTransactionModalLabel">Thêm mới Giao dịch</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- Form content for adding a new transaction -->
                    @include('_system.partialview.edit_trans', [
                        'transactions' => $transactions,
                    ])
                </div>
            </div>
        </div>
    </div>
</div>


@include('_system.partialview.trans_table', ['transactions' => $transactions, 'columns' => $columns])
@endif

@endsection
