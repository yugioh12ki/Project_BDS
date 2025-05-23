<div class="table-container">
    <table>
        <thead>
            <tr>
                @foreach ($columns as $column )
                @if($column == 'TransactionID')
                    <th>ID giao dịch</th>
                @elseif($column == 'PropertyID')
                    <th>Tin đăng BĐS</th>
                @elseif($column == 'TransactionDate')
                    <th>Ngày giao dịch</th>
                @elseif($column == 'AgentID')
                    <th>Người môi giới</th>

                @elseif($column == 'TranStatus')
                    <th>Trạng thái</th>
                @elseif($column == 'TotalPrice')
                    <th>Tổng giá trị </th>
                @elseif($column == 'TransactionType')
                    @continue
                @else
                    @continue
                @endif
                @endforeach
                <th>Chức năng</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($transactions as $transaction)
                <tr>
                    @foreach ($columns as $column)
                    @if($column !== 'TransactionType' && $column !== 'OwnerID' && $column !== 'CusID')
                        @if($column =='AgentID')
                            <td>{{ optional($transaction->trans_agent)->Name ?? 'No data' }}</td>
                    @else
                        <td>{{ $transaction->$column }}</td>
                        @endif
                    @endif

                    @endforeach
                    <td>
                        <div class ="d-flex gap-2">
                            <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#editModal{{ $transaction->TransactionID }}">
                                <i class="fas fa-edit"></i> Sửa
                            </button>

                            <button type="button" class="btn btn-danger btn-sm btn-delete" data-bs-toggle="modal" data-bs-target="#deleteModal{{ $transaction->TransactionID }}">
                                <i class="fas fa-trash"></i> Xóa
                            </button>

                            {{-- Modal chinh sua --}}

                            <div class="modal fade" id="editModal{{ $transaction->TransactionID }}" tabindex="-1" aria-labelledby="editModalLabel{{ $transaction->TransactionID }}" aria-hidden="true">
                                <div class="modal-dialog modal-lg transaction-modal">
                                    <div class="modal-content">


                                            <div class="modal-header">
                                                <h5 class="modal-title" id="editModalLabel{{ $transaction->TransactionID }}">Chỉnh sửa giao dịch</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="transaction-edit-container">
                                                @include('_system.partialview.edit_trans', [
                                                    'transaction' => $transaction,
                                                    ])
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>

                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>

                            {{-- Modal xóa --}}

                            <div class="modal fade" id="deleteModal{{ $transaction->TransactionID }}" tabindex="-1" aria-labelledby="deleteModalLabel{{ $transaction->TransactionID }}" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="deleteModalLabel{{ $transaction->TransactionID }}">Xóa giao dịch</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <p>Bạn có chắc chắn muốn xóa giao dịch {{ $transaction->TransactionID }} không?</p>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                                            <form method="POST" action="{{ route('admin.transaction.delete', $transaction->TransactionID) }}">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger">Xóa</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
