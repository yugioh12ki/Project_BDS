

    <div class="form-group">
        <label for="status">Trạng thái kiểm duyệt</label>
        <select class="form-control" id="status" name="status" required>
            <option value="pending" {{ old('status', $property->Status) == 'pending' ? 'selected' : '' }}>Chờ duyệt</option>
            <option value="active" {{ old('status', $property->Status) == 'active' ? 'selected' : '' }}>Đã duyệt</option>
            <option value="inactive" {{ old('status', $property->Status) == 'inactive' ? 'selected' : '' }}>Ngừng hoạt động</option>
            <option value="rented" {{ old('status', $property->Status) == 'rented' ? 'selected' : '' }}>Đã cho thuê</option>
            <option value="sold" {{ old('status', $property->Status) == 'sold' ? 'selected' : '' }}>Đã bán</option>
        </select>
    </div>

    @if($property->Status == 'pending' || $property->Status == '')
    <div class="form-group mt-3">
        <label for="approvedBy">Người duyệt</label>
        <input type="text" class="form-control" value="{{ Auth::user()->Name }}" readonly>
        <input type="hidden" name="approvedBy" value="{{ Auth::id() }}">
    </div>
    @endif

