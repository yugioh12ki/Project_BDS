<form method="POST"">
    @csrf
    @method('PUT')
    {{-- Người tạo lịch hẹn --}}
    <div class="form-group">
        <label>Người tạo lịch hẹn</label>
        <input type="text" class="form-control" value="#" readonly>
    </div>
    {{-- Người hẹn --}}
    <div class="form-group">
        <label>Người hẹn</label>
        <input type="text" class="form-control" value="#" readonly>
    </div>
    {{-- Tiêu đề cuộc hẹn --}}
    <div class="form-group">
        <label>Tiêu đề cuộc hẹn</label>
        <input type="text" class="form-control" value="#" readonly>
    </div>
    {{-- Ngày bắt đầu --}}
    <div class="form-group">
        <label>Ngày bắt đầu</label>
        <input type="datetime-local" class="form-control" value="#" readonly>
    </div>
    {{-- Ngày kết thúc --}}
    <div class="form-group">
        <label>Ngày kết thúc</label>
        <input type="datetime-local" class="form-control" value="#" readonly>
    </div>
    {{-- Status --}}
    <div class="form-group">
        <label>Status</label>
        <input type="text" class="form-control" value="#" readonly>
    </div>

</form>


