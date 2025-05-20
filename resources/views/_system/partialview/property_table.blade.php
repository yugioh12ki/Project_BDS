@if ($errors->any())
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        @foreach ($errors->all() as $error)
            <div>{{ $error }}</div>
        @endforeach
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if (session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if(empty($properties))
    <div class="alert alert-danger">
        <strong>Không có dữ liệu</strong>
    </div>
@else
<div class="property-table-header d-flex justify-content-between align-items-center mb-3">
    <div>
        <h6 class="mb-0">
            Danh sách BĐS
            <span class="badge bg-info" id="propertyCount">{{ count($properties) }}</span>
        </h6>
    </div>
    @if(isset($status) && $status == 'pending')
    <div class="batch-actions">
        <button class="btn btn-sm btn-outline-primary" id="selectAllBtn">
            <i class="fas fa-check-square"></i> Chọn tất cả
        </button>
        <button class="btn btn-sm btn-success batch-approve-btn" disabled>
            <i class="fas fa-check"></i> Duyệt <span class="selected-count">(0)</span>
        </button>
        <button class="btn btn-sm btn-warning batch-reject-btn" disabled>
            <i class="fas fa-times"></i> Từ chối <span class="selected-count">(0)</span>
        </button>
    </div>
    @endif
</div>

<div id="noPropertiesMessage" style="display: none;" class="alert alert-info">
    <i class="fas fa-info-circle"></i> Không tìm thấy bất động sản nào phù hợp với điều kiện lọc.
</div>

<script>
// Global functions for property approval and rejection
function approveProperty(propertyId) {
    if (confirm('Bạn có chắc chắn muốn duyệt bất động sản này không?')) {
        PropertyManagement.updatePropertyStatus(propertyId, 'approved');
    }
}

function rejectProperty(propertyId) {
    if (confirm('Bạn có chắc chắn muốn từ chối bất động sản này không?')) {
        PropertyManagement.updatePropertyStatus(propertyId, 'rejected');
    }
}
</script>

<div class="table-responsive">
    <table class="table table-hover table-striped">
        <thead>
            <tr>
                @if(isset($status) && $status == 'pending')
                <th class="checkbox-column">
                    <div class="form-check">
                        <input class="form-check-input select-all-checkbox" type="checkbox" id="selectAll">
                    </div>
                </th>
                @endif
                <th>ID</th>
                <th>Tiêu đề</th>
                <th class="d-none d-md-table-cell">Loại hình</th>
                <th class="d-none d-md-table-cell">Địa chỉ</th>
                <th class="d-none d-lg-table-cell">Ngày đăng</th>
                <th class="d-none d-lg-table-cell">Chủ sở hữu</th>
                <th>Thao tác</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($properties as $property)
                <tr id="property-row-{{ $property->PropertyID }}"
                    class="property-row {{ isset($status) && $status == 'pending' ? 'pending-property' : '' }}"
                    data-lat="{{ $property->Latitude ?? '' }}"
                    data-lng="{{ $property->Longitude ?? '' }}"
                    data-address="{{ $property->Address ?? '' }}"
                    data-ward="{{ $property->Ward ?? '' }}"
                    data-district="{{ $property->District ?? '' }}"
                    data-province="{{ $property->Province ?? '' }}"
                    data-full-address="@php
                        $addressParts = [];
                        if(!empty($property->Address)) $addressParts[] = $property->Address;
                        if(!empty($property->Ward)) $addressParts[] = $property->Ward;
                        if(!empty($property->District)) $addressParts[] = $property->District;
                        if(!empty($property->Province)) $addressParts[] = $property->Province;
                        echo implode(', ', $addressParts);
                    @endphp"
                    data-category="{{ optional($property->danhMuc)->CategoryID ?? '' }}"
                    data-price="{{ $property->Price ?? 0 }}"
                    data-date="{{ $property->PostedDate !== '0000-00-00' ? $property->PostedDate : '' }}">

                    @if(isset($status) && $status == 'pending')
                    <td class="checkbox-column" onclick="event.stopPropagation();">
                        <div class="form-check">
                            <input class="form-check-input property-checkbox" type="checkbox" data-property-id="{{ $property->PropertyID }}">
                        </div>
                    </td>
                    @endif

                    <td onclick="selectPropertyOnMap('{{ $property->PropertyID }}', '{{ $property->Latitude ?? '' }}', '{{ $property->Longitude ?? '' }}', '@php
                        $addressParts = [];
                        if(!empty($property->Address)) $addressParts[] = $property->Address;
                        if(!empty($property->Ward)) $addressParts[] = $property->Ward;
                        if(!empty($property->District)) $addressParts[] = $property->District;
                        if(!empty($property->Province)) $addressParts[] = $property->Province;
                        echo implode(', ', $addressParts);
                    @endphp')">{{ $property->PropertyID }}</td>
                    <td onclick="selectPropertyOnMap('{{ $property->PropertyID }}', '{{ $property->Latitude ?? '' }}', '{{ $property->Longitude ?? '' }}', '{{ ($property->Address ?? '') . ($property->Ward ? ', ' . $property->Ward : '') . ($property->District ? ', ' . $property->District : '') . ($property->Province ? ', ' . $property->Province : '') }}')">
                        <div class="property-title">{{ $property->Title }}</div>
                        <div class="d-block d-md-none">
                            <small class="text-muted">{{ optional($property->danhMuc)->ten_pro ?? 'N/A' }} - {{ $property->Address }}</small>
                        </div>
                    </td>
                    <td class="d-none d-md-table-cell" onclick="selectPropertyOnMap('{{ $property->PropertyID }}', '{{ $property->Latitude ?? '' }}', '{{ $property->Longitude ?? '' }}', '{{ ($property->Address ?? '') . ($property->Ward ? ', ' . $property->Ward : '') . ($property->District ? ', ' . $property->District : '') . ($property->Province ? ', ' . $property->Province : '') }}')">{{ optional($property->danhMuc)->ten_pro ?? 'N/A' }}</td>
                    <td class="d-none d-md-table-cell" onclick="selectPropertyOnMap('{{ $property->PropertyID }}', '{{ $property->Latitude ?? '' }}', '{{ $property->Longitude ?? '' }}', '{{ ($property->Address ?? '') . ($property->Ward ? ', ' . $property->Ward : '') . ($property->District ? ', ' . $property->District : '') . ($property->Province ? ', ' . $property->Province : '') }}')">{{ $property->Address }}</td>
                    <td class="d-none d-lg-table-cell" onclick="selectPropertyOnMap('{{ $property->PropertyID }}', '{{ $property->Latitude ?? '' }}', '{{ $property->Longitude ?? '' }}', '{{ ($property->Address ?? '') . ($property->Ward ? ', ' . $property->Ward : '') . ($property->District ? ', ' . $property->District : '') . ($property->Province ? ', ' . $property->Province : '') }}')">{{ $property->PostedDate === '0000-00-00' ? 'N/A' : date('d/m/Y', strtotime($property->PostedDate)) }}</td>
                    <td class="d-none d-lg-table-cell" onclick="selectPropertyOnMap('{{ $property->PropertyID }}', '{{ $property->Latitude ?? '' }}', '{{ $property->Longitude ?? '' }}', '{{ ($property->Address ?? '') . ($property->Ward ? ', ' . $property->Ward : '') . ($property->District ? ', ' . $property->District : '') . ($property->Province ? ', ' . $property->Province : '') }}')">{{ optional($property->chusohuu)->Name ?? 'N/A' }}</td>
                    <td onclick="event.stopPropagation();">
                        <div class="d-flex gap-2">
                            <button type="button" class="btn btn-info btn-sm" data-bs-toggle="modal" data-bs-target="#viewModal{{ $property->PropertyID }}">
                                <i class="fas fa-eye"></i> <span class="d-none d-sm-inline">Xem</span>
                            </button>

                            @if(isset($status) && $status == 'pending')
                            <div class="btn-group">
                                <button type="button" class="btn btn-success btn-sm approve-btn" data-property-id="{{ $property->PropertyID }}" onclick="approveProperty('{{ $property->PropertyID }}')">
                                    <i class="fas fa-check"></i> <span class="d-none d-sm-inline">Duyệt</span>
                                </button>
                                <button type="button" class="btn btn-warning btn-sm reject-btn" data-property-id="{{ $property->PropertyID }}" onclick="rejectProperty('{{ $property->PropertyID }}')">
                                    <i class="fas fa-times"></i> <span class="d-none d-sm-inline">Từ chối</span>
                                </button>
                            </div>
                            @endif
                        </div>
                    </td>
                </tr>

                <!-- Modal xem chi tiết -->
                <div class="modal fade" id="viewModal{{ $property->PropertyID }}" tabindex="-1" aria-labelledby="viewModalLabel{{ $property->PropertyID }}" aria-hidden="true">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="viewModalLabel{{ $property->PropertyID }}">Chi tiết Bất Động Sản</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                @include('_system.partialview.info_property', [
                                    'property' => $property,
                                    'owners' => $owners,
                                    'agents' => $agents,
                                    'admins' => $admins,
                                    'categories' => $categories,
                                    'modalId' => $property->PropertyID // truyền id để dùng cho tab-pane
                                ])
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </tbody>
    </table>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Handle select all checkbox
    const selectAllCheckbox = document.querySelector('.select-all-checkbox');
    if (selectAllCheckbox) {
        selectAllCheckbox.addEventListener('change', function() {
            const isChecked = this.checked;
            const checkboxes = document.querySelectorAll('.property-checkbox');
            checkboxes.forEach(checkbox => {
                checkbox.checked = isChecked;
            });
            updateSelectedCount();
        });
    }

    // Handle select all button
    const selectAllBtn = document.getElementById('selectAllBtn');
    if (selectAllBtn) {
        selectAllBtn.addEventListener('click', function() {
            const checkboxes = document.querySelectorAll('.property-checkbox');
            const allChecked = Array.from(checkboxes).every(checkbox => checkbox.checked);

            checkboxes.forEach(checkbox => {
                checkbox.checked = !allChecked;
            });

            if (selectAllCheckbox) {
                selectAllCheckbox.checked = !allChecked;
            }

            updateSelectedCount();
        });
    }

    // Handle individual checkboxes
    const propertyCheckboxes = document.querySelectorAll('.property-checkbox');
    propertyCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', updateSelectedCount);
    });

    // Update selected count
    function updateSelectedCount() {
        const checkedCount = document.querySelectorAll('.property-checkbox:checked').length;
        const countElements = document.querySelectorAll('.selected-count');

        countElements.forEach(element => {
            element.textContent = `(${checkedCount})`;
        });

        // Enable/disable batch action buttons
        const batchButtons = document.querySelectorAll('.batch-approve-btn, .batch-reject-btn');
        batchButtons.forEach(button => {
            button.disabled = checkedCount === 0;
        });
    }

    // Handle batch approve button
    const batchApproveBtn = document.querySelector('.batch-approve-btn');
    if (batchApproveBtn) {
        batchApproveBtn.addEventListener('click', function() {
            const selectedIds = getSelectedPropertyIds();
            if (selectedIds.length > 0 && confirm(`Bạn có chắc chắn muốn duyệt ${selectedIds.length} bất động sản này không?`)) {
                PropertyManagement.updateBatchPropertyStatus(selectedIds, 'approved');
            }
        });
    }

    // Handle batch reject button
    const batchRejectBtn = document.querySelector('.batch-reject-btn');
    if (batchRejectBtn) {
        batchRejectBtn.addEventListener('click', function() {
            const selectedIds = getSelectedPropertyIds();
            if (selectedIds.length > 0 && confirm(`Bạn có chắc chắn muốn từ chối ${selectedIds.length} bất động sản này không?`)) {
                PropertyManagement.updateBatchPropertyStatus(selectedIds, 'rejected');
            }
        });
    }

    // Get selected property IDs
    function getSelectedPropertyIds() {
        const checkboxes = document.querySelectorAll('.property-checkbox:checked');
        return Array.from(checkboxes).map(checkbox => checkbox.getAttribute('data-property-id'));
    }

    /* Moved the individual approval and rejection functions to global scope */

    // Update batch property status
    function updateBatchPropertyStatus(propertyIds, status, reason = null) {
        // Add loading state to selected rows
        propertyIds.forEach(id => {
            const row = document.getElementById(`property-row-${id}`);
            if (row) {
                row.classList.add('updating');
            }
        });

        // Create form data
        const formData = new FormData();
        formData.append('property_ids', JSON.stringify(propertyIds));
        formData.append('status', status);
        if (reason) {
            formData.append('reason', reason);
        }
        formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));

        // Send AJAX request
        fetch('/admin/property/update-batch-status', {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Remove rows with animation
                propertyIds.forEach(id => {
                    const row = document.getElementById(`property-row-${id}`);
                    if (row) {
                        row.style.opacity = '0';
                        row.style.transform = 'translateX(100px)';
                        setTimeout(() => {
                            row.remove();
                        }, 500);
                    }
                });

                // Update property count
                const countElement = document.getElementById('propertyCount');
                if (countElement) {
                    const currentCount = parseInt(countElement.textContent);
                    countElement.textContent = currentCount - propertyIds.length;
                }

                // Update menu badge counts
                PropertyManagement.updatePendingCountInMenu();

                // Show success notification
                PropertyManagement.showNotification('success', data.message || `Đã cập nhật trạng thái cho ${propertyIds.length} bất động sản`);
            } else {
                PropertyManagement.showNotification('error', data.message || 'Đã xảy ra lỗi khi cập nhật trạng thái');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            PropertyManagement.showNotification('error', 'Đã xảy ra lỗi khi cập nhật trạng thái');
        });
    }
});
</script>
@endif
