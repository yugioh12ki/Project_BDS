<div class="container mt-6">
    <ul class="nav nav-tabs" id="transactionTabs{{ $transaction->TransactionID }}" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="transaction-info-tab{{ $transaction->TransactionID }}" data-bs-toggle="tab" data-bs-target="#transaction-info{{ $transaction->TransactionID }}" type="button" role="tab" aria-controls="transaction-info{{ $transaction->TransactionID }}" aria-selected="true">
                Thông tin giao dịch
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="transaction-num-pay-tab{{ $transaction->TransactionID }}" data-bs-toggle="tab" data-bs-target="#transaction-num-pay{{ $transaction->TransactionID }}" type="button" role="tab" aria-controls="transaction-num-pay{{ $transaction->TransactionID }}" aria-selected="false">
                Lịch sử giao dịch
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="transaction-documents-tab{{ $transaction->TransactionID }}" data-bs-toggle="tab" data-bs-target="#transaction-documents{{ $transaction->TransactionID }}" type="button" role="tab" aria-controls="transaction-documents{{ $transaction->TransactionID }}" aria-selected="false">
                Tài liệu giao dịch
            </button>
        </li>
    </ul>

    <!-- Không dùng form ở đây, chỉ giữ nội dung -->
    <div class="tab-content" id="transactionTabsContent{{ $transaction->TransactionID }}">
        <!-- Tab Thông tin giao dịch -->
        <div class="tab-pane fade show active" id="transaction-info{{ $transaction->TransactionID }}" role="tabpanel" aria-labelledby="transaction-info-tab{{ $transaction->TransactionID }}">
            <div class="card mt-3">
                <div class="card-body">
                    <h5 class="card-title">Thông tin giao dịch</h5>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <p><strong>Mã BĐS:</strong> {{ $transaction->PropertyID }}</p>
                            <p><strong>Người môi giới:</strong> {{ $transaction->trans_agent->Name }}</p>
                            <p><strong>Chủ sở hữu:</strong> {{ $transaction->trans_owner->Name }}</p>
                            <p><strong>Khách hàng:</strong> {{ $transaction->trans_cus->Name }}</p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Tổng giá trị:</strong> {{ number_format($transaction->TotalPrice, 0, ',', '.') }} VNĐ</p>
                            <p><strong>Ngày giao dịch:</strong> {{ date('d/m/Y H:i', strtotime($transaction->TransactionDate)) }}</p>
                            <p><strong>Trạng thái:</strong> {{ $transaction->TranStatus }}</p>
                            <p><strong>Loại giao dịch:</strong> {{ $transaction->TransactionType }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tab Lịch sử giao dịch -->
        <div class="tab-pane fade" id="transaction-num-pay{{ $transaction->TransactionID }}" role="tabpanel" aria-labelledby="transaction-num-pay-tab{{ $transaction->TransactionID }}">
            <div class="card mt-3">
                <div class="card-body">
                    <h5 class="card-title">Lịch sử thanh toán</h5>
                    <form action="{{ route('admin.transaction.updatePaymentStatuses', $transaction->TransactionID) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Số lần</th>
                                    <th>Số tiền</th>
                                    <th>Ngày thanh toán</th>
                                    <th>Tình trạng thanh toán</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($transaction->detailTransaction as $detail)
                                    <tr>
                                        <td>{{ $detail->Num_Pay }}</td>
                                        <td>{{ number_format($detail->Price, 0, ',', '.') }}</td>
                                        <td>{{ $detail->DTran_Date ? date('d/m/Y H:i', strtotime($detail->DTran_Date)) : '' }}</td>
                                        <td>
                                            <input type="hidden" name="payment_ids[]" value="{{ $detail->Num_Pay }}">
                                            <select name="statuses[]" class="form-select form-select-sm" >
                                                <option value="Chờ đợi" {{ $detail->DTran_Status == 'Chờ đợi' ? 'selected' : '' }}>Chờ duyệt</option>
                                                <option value="Hoàn Thành" {{ $detail->DTran_Status == 'Hoàn Thành' ? 'selected' : '' }}>Hoàn Thành</option>
                                                <option value="Hủy" {{ $detail->DTran_Status == 'Hủy' ? 'selected' : '' }}>Đã hủy</option>
                                            </select>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center">Không có dữ liệu lịch sử giao dịch.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                        <button type="submit" class="btn btn-primary">Lưu trạng thái thanh toán</button>
                    </form>

                    <hr>
                    <h5 class="mt-4">Thêm khoản thanh toán mới</h5>
                    <form action="{{ route('admin.transaction.addPayment', $transaction->TransactionID) }}" method="POST">
                        @csrf
                        <div class="row">
                            <div class="col-md-5 mb-3">
                                <label class="form-label">Số tiền</label>
                                <input type="number" name="Price" class="form-control " required min="1" ">
                            </div>
                            {{-- <div class="col-md-5 mb-3">
                                <label class="form-label">Ngày thanh toán</label>
                                <input type="datetime-local" name="PaymentDate" class="form-control" value="{{ date('Y-m-d H:i ') }}" required>
                            </div> --}}
                            <div class="col-md-2 mb-3 d-flex align-items-end">
                                <button type="submit" class="btn btn-success w-100">Thêm mới</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Tab Tài liệu giao dịch -->
        <div class="tab-pane fade" id="transaction-documents{{ $transaction->TransactionID }}" role="tabpanel" aria-labelledby="transaction-documents-tab{{ $transaction->TransactionID }}">
            <div class="card mt-3">
                <div class="card-body">
                    <h5 class="card-title">Danh sách tài liệu</h5>
                    <div class="mb-2 text-muted">
                        <small>Tài liệu được lưu trữ trong thư mục trans_{{ $transaction->TransactionID }}</small>
                    </div>
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Mã tài liệu</th>
                                <th>Loại file</th>
                                <th>Ngày upload</th>
                                <th>Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($transaction->document as $doc)
                                <tr>
                                    <td>{{ $doc->DocumentID }}</td>
                                    <td>{{ $doc->DocumentType }}</td>
                                    <td>{{ date('d/m/Y H:i', strtotime($doc->UploadedDate)) }}</td>
                                    <td>
                                        <div class="btn-group">
                                            <a href="{{ route('document.view', $doc->DocumentID) }}" target="_blank" class="btn btn-sm btn-info">
                                                <i class="fas fa-eye"></i> Xem
                                            </a>
                                            <a href="{{ route('document.download', $doc->DocumentID) }}" class="btn btn-sm btn-success">
                                                <i class="fas fa-download"></i> Tải xuống
                                            </a>

                                            @if(Auth::user()->Role == 'Admin' || (Auth::user()->Role == 'Agent' && $transaction->AgentID == Auth::user()->UserID))
                                                <form action="{{ route('admin.document.delete', $doc->DocumentID) }}" method="POST" class="d-inline" onsubmit="return confirm('Bạn có chắc muốn xóa tài liệu này?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger">
                                                        <i class="fas fa-trash"></i> Xóa
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center">Không có tài liệu.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>

                    @if(Auth::user()->Role == 'Admin' || (Auth::user()->Role == 'Agent' && $transaction->AgentID == Auth::user()->UserID))
                        <hr>
                        <h5 class="mt-4">Tải tài liệu mới</h5>
                        <form action="{{ route('admin.transaction.addDocument', $transaction->TransactionID) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="row">
                                <div class="col-md-5 mb-3">
                                    <label class="form-label">Loại tài liệu</label>
                                    <input type="text" name="DocumentType" id="documentType{{ $transaction->TransactionID }}" class="form-control" required>
                                    <small class="text-muted">Định dạng file</small>
                                </div>
                                <div class="col-md-5 mb-3">
                                    <label class="form-label">Chọn file</label>
                                    <input type="file" name="document" id="documentFile{{ $transaction->TransactionID }}" class="form-control" required
                                        accept=".pdf,.doc,.docx,.xls,.xlsx">
                                    <small class="text-muted">Chỉ chấp nhận file PDF, DOC, DOCX, XLS, XLSX</small>
                                </div>
                                <div class="col-md-2 mb-3 d-flex align-items-end">
                                    <button type="submit" id="uploadBtn{{ $transaction->TransactionID }}" class="btn btn-success w-100">Tải lên</button>
                                </div>
                            </div>
                            <div id="fileWarning{{ $transaction->TransactionID }}" class="alert alert-warning mt-2 d-none">
                                <i class="fas fa-exclamation-triangle"></i>
                                <span id="warningMessage{{ $transaction->TransactionID }}">File không được hỗ trợ. Vui lòng chọn file văn bản.</span>
                            </div>
                        </form>
                    @endif

                    <div class="alert alert-info mt-3">
                        <i class="fas fa-info-circle"></i> Tài liệu giao dịch được bảo mật. Chỉ người có quyền truy cập mới có thể xem và tải xuống.
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    $('#editModal{{ $transaction->TransactionID }}').on('shown.bs.modal', function () {
        // Tab handling
        $('#transaction-info-tab{{ $transaction->TransactionID }}').tab('show');

        console.log('Modal shown, setting up file input handler');

        // Sử dụng ID đúng của input file
        $('#documentFile{{ $transaction->TransactionID }}').on('change', function() {
            console.log('File input changed');
            var fileName = $(this).val().split('\\').pop();
            console.log('Selected file:', fileName);

            var uploadBtn = $('#uploadBtn{{ $transaction->TransactionID }}');
            var warningDiv = $('#fileWarning{{ $transaction->TransactionID }}');
            var warningMessage = $('#warningMessage{{ $transaction->TransactionID }}');

            if (fileName) {
                // Lấy phần đuôi file
                var extension = fileName.split('.').pop().toLowerCase();
                console.log('File extension:', extension);

                // Danh sách các file văn bản được chấp nhận
                var allowedTextTypes = ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'txt', 'rtf', 'ppt', 'pptx'];

                // Danh sách các file ảnh cần hiển thị cảnh báo
                var imageTypes = ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'svg', 'webp'];

                // Kiểm tra loại file
                if (allowedTextTypes.includes(extension)) {
                    // File văn bản được chấp nhận
                    uploadBtn.prop('disabled', false);
                    warningDiv.addClass('d-none');

                    // Định dạng theo đuôi file
                    var documentType;

                    switch(extension) {
                        case 'pdf':
                            documentType = 'PDF Document';
                            break;
                        case 'doc':
                        case 'docx':
                            documentType = 'Word Document';
                            break;
                        case 'xls':
                        case 'xlsx':
                            documentType = 'Excel Document';
                            break;
                        default:
                            documentType = extension.toUpperCase() + ' Document';
                    }

                    console.log('Setting document type to:', documentType);
                    // Điền vào trường DocumentType
                    $('#documentType{{ $transaction->TransactionID }}').val(documentType);
                } else if (imageTypes.includes(extension)) {
                    // File ảnh - hiển thị cảnh báo
                    uploadBtn.prop('disabled', true);
                    warningDiv.removeClass('d-none');
                    warningMessage.text('File ảnh không được hỗ trợ. Vui lòng chọn file văn bản.');
                } else {
                    // Các loại file khác - hiển thị cảnh báo chung
                    uploadBtn.prop('disabled', true);
                    warningDiv.removeClass('d-none');
                    warningMessage.text('Loại file không được hỗ trợ. Chỉ chấp nhận file văn bản: PDF, Word, Excel, v.v.');
                }
            }
        });

        // Thêm log để kiểm tra các elements đã được tìm thấy chưa
        console.log('File input exists:', $('#documentFile{{ $transaction->TransactionID }}').length > 0);
        console.log('Document type input exists:', $('#documentType{{ $transaction->TransactionID }}').length > 0);
    });
});



// Đảm bảo ID của tab và nội dung tab không trùng nhau giữa các modal
$(document).ready(function() {
    $('#editModal{{ $transaction->TransactionID }}').on('shown.bs.modal', function () {
        $('#transaction-info-tab{{ $transaction->TransactionID }}').tab('show');
    });
});
</script>
