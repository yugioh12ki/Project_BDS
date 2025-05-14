<form action="{{--  --}}" method="POST">
    @csrf
        <div class="mb-3">
            <label for="transactionName" class="form-label">Tên Giao dịch</label>
            <input type="text" class="form-control" id="transactionName" name="transactionName" required>
        </div>
        <div class="mb-3">
            <label for="transactionAmount" class="form-label">Số tiền</label>
            <input type="number" class="form-control" id="transactionAmount" name="transactionAmount" required>
        </div>
        <button type="submit" class="btn btn-primary">Lưu</button>
</form>
