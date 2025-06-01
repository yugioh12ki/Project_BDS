<div class="modal fade" id="createAppointmentModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tạo lịch hẹn mới</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="appointmentForm" method="POST" action="{{ route('agent.appointments.create') }}" novalidate>
                @csrf
                <div class="modal-body">

                    <div class="mb-3 position-relative">
                        <label class="form-label">Chủ sở hữu <span class="text-danger">*</span></label>
                        <input type="text" id="ownerInput" class="form-control" autocomplete="off" placeholder="Nhập tên chủ sở hữu...">
                        <input type="hidden" id="ownerIdInput" name="OwnerID">
                        <div class="list-group position-absolute w-100" id="ownerSuggestions" style="z-index:1055; display:none"></div>
                    </div>

                    <div class="mb-3" id="propertySelectionSection" style="display:none;">
                        <label class="form-label">Bất động sản <span class="text-danger">*</span></label>
                        <select class="form-select" id="propertySelect" name="PropertyID" required>
                            <option value="">-- Vui lòng chọn chủ sở hữu trước --</option>
                        </select>
                    </div>

                    <div class="mb-3 position-relative">
                        <label class="form-label">Khách hàng <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="customerInput" autocomplete="off" placeholder="Nhập tên khách hàng...">
                        <input type="hidden" name="CusID" id="customerIdInput">
                        <div class="list-group position-absolute w-100" id="customerSuggestions" style="z-index:1055; display:none;"></div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Thời gian bắt đầu <span class="text-danger">*</span></label>
                            <input type="datetime-local" class="form-control" name="AppointmentDateStart" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Thời gian kết thúc <span class="text-danger">*</span></label>
                            <input type="datetime-local" class="form-control" name="AppointmentDateEnd" required>
                        </div>
                    </div>

                    <!-- Tiêu đề -->
                    <div class="mb-3">
                        <label class="form-label">Tiêu đề <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="TitleAppoint" placeholder="Nhập tiêu đề lịch hẹn..." required>
                    </div>

                    <!-- Nội dung -->
                    <div class="mb-3">
                        <label class="form-label">Nội dung <span class="text-danger">*</span></label>
                        <textarea class="form-control" name="DescAppoint" rows="3" placeholder="Nhập nội dung cuộc hẹn..." required></textarea>
                    </div>

                    <div class="alert alert-info">
                        <i class="bi bi-info-circle-fill me-2"></i>
                        <strong>Lưu ý:</strong> Thời gian hẹn sẽ được tự động thông báo cho chủ sở hữu và khách hàng.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-primary">Tạo lịch hẹn</button>
                </div>
            </form>
        </div>
    </div>
</div>
