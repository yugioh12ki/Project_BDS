<div class="container mt-6">
    <ul class="nav nav-tabs" id="transactionTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="transaction-info-tab" data-bs-toggle="tab" data-bs-target="#transaction-info" type="button" role="tab" aria-controls="transaction-info" aria-selected="true">
                Thông tin giao dịch
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="transaction-documents-tab" data-bs-toggle="tab" data-bs-target="#transaction-documents" type="button" role="tab" aria-controls="transaction-documents" aria-selected="false">
                Tài liệu giao dịch
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="transaction-num-pay-tab" data-bs-toggle="tab" data-bs-target="#transaction-num-pay" type="button" role="tab" aria-controls="transaction-num-pay" aria-selected="false">
                Lịch sử giao dịch
            </button>

        </li>
    </ul>
    <div class="tab-content" id="transactionTabsContent">
        <!-- Tab Thông tin giao dịch -->
        <div class="tab-pane fade show active" id="transaction-info" role="tabpanel" aria-labelledby="transaction-info-tab">
            <form action="{{--  --}}" method="POST">
                @csrf
                <div class="mb-3">
                    <label for="transaction-id" class="form-label">Mã giao dịch</label>
                    <input type="text" class="form-control" id="transaction-id" name="transaction_id" placeholder="Nhập mã giao dịch">
                </div>
                <div class="mb-3">
                    <label for="transaction-amount" class="form-label">Số tiền</label>
                    <input type="number" class="form-control" id="transaction-amount" name="transaction_amount" placeholder="Nhập số tiền">
                </div>
                <div class="mb-3">
                    <label for="transaction-date" class="form-label">Ngày giao dịch</label>
                    <input type="date" class="form-control" id="transaction-date" name="transaction_date">
                </div>
                <button type="submit" class="btn btn-primary">Lưu thông tin</button>
            </form>
        </div>

        <!-- Tab Tài liệu giao dịch -->
        <div class="tab-pane fade" id="transaction-documents" role="tabpanel" aria-labelledby="transaction-documents-tab">
            <form action="{{--  --}}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="mb-3">
                    <label for="transaction-documents" class="form-label">Tải lên tài liệu</label>
                    <input type="file" class="form-control" id="transaction-documents" name="documents[]" multiple>
                </div>
                <button type="submit" class="btn btn-primary">Tải lên</button>
            </form>
        </div>
        <!-- Tab Lịch sử giao dịch -->
        <div class="tab-pane fade" id="transaction-num-pay" role="tabpanel" aria-labelledby="transaction-num-pay-tab">
            <form action="{{--  --}}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="mb-3">
                    <label for="transaction-num-pay" class="form-label">Lịch sử giao dịch</label>
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th scope="col">Số lần</th>
                                <th scope="col">Số tiền</th>
                                <th scope="col">Tình trạng thanh toán</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>

                            </tr>
                            <!-- Add more rows as needed -->
                        </tbody>
                    </table>
                </div>
                <button type="submit" class="btn btn-primary">Tải lên</button>
            </form>
        </div>
    </div>
</div>
