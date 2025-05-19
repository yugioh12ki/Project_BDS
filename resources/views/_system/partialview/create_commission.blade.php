<div class="container">
    <form action="{{ route('admin.commission.store') }}" method="POST">
        @csrf
        <div class="form-group">
            <label for="TransactionID">Mã giao dịch</label>
            <select class="form-control" id="TransactionID" name="TransactionID" required>
                <option value="">-- Chọn giao dịch --</option>
                @foreach($transactions as $transaction)
                    <option value="{{ $transaction->TransactionID }}">{{ $transaction->TransactionID }} </option>
                @endforeach
            </select>
        </div>

        <div class="form-group">
            <label for="AgentID">Môi giới</label>
            <input type="hidden" id="AgentID" name="AgentID">
                <span id="AgentName"></span>
        </div>

        <div class="form-group">
            <label for="Percentage">Phần trăm hoa hồng (%)</label>
            <input type="number" class="form-control" id="Percentage" name="Percentage" min="0" max="100" step="0.01" required>
        </div>

        <div class="form-group">
            <label for="StatusCommission">Trạng thái</label>
            <select class="form-control" id="StatusCommission" name="StatusCommission" required>
                <option value="Pending">Đang chờ</option>
                <option value="Success">Thành công</option>
                <option value="Cancelled">Đã hủy</option>
            </select>
        </div>

        <button type="submit" class="btn btn-primary mt-3">Lưu</button>
    </form>
</div>

<script>
    document.getElementById('TransactionID').addEventListener('change', function() {
        const transactionId = this.value;
        const agentIdInput = document.getElementById('AgentID');
        const agentNameSpan = document.getElementById('AgentName');

        agentIdInput.value = '';
        agentNameSpan.textContent = '';

        if (transactionId) {
            fetch(`/api/agents-by-transaction/${transactionId}`)
                .then(response => response.json())
                .then(data => {
                    if (data && data.AgentID) {
                        agentIdInput.value = data.AgentID;
                        agentNameSpan.textContent = data.Name + ' (' + data.AgentID + ')';
                    } else if (Array.isArray(data) && data.length > 0) {
                        // fallback nếu API trả về dạng mảng
                        agentIdInput.value = data[0].AgentID;
                        agentNameSpan.textContent = data[0].Name + ' (' + data[0].AgentID + ')';
                    } else {
                        agentNameSpan.textContent = 'Không tìm thấy môi giới';
                    }
                })
                .catch(() => {
                    agentNameSpan.textContent = 'Không tìm thấy môi giới';
                });
        }
    });
</script>


