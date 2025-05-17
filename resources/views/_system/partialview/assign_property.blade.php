<div class="assignment-container">
    <!-- Thanh tìm kiếm và bộ lọc -->
    <div class="filter-section">
        <div class="search-box">
            <input type="text" id="searchInput" placeholder="Tìm kiếm...">
            <button id="searchButton"><i class="fa fa-search"></i></button>
        </div>
        <div class="filter-options">
            <select id="statusFilter">
                <option value="">-- Trạng thái BĐS --</option>
                <option value="active">Đang hoạt động</option>
                <option value="pending">Chờ duyệt</option>
                <option value="inactive">Không hoạt động</option>
            </select>
            <select id="areaFilter">
                <option value="">-- Khu vực --</option>
                <!-- Sẽ được điền bằng JavaScript -->
            </select>
        </div>
    </div>

    <div class="assignment-layout">
        <!-- Panel bên trái: Danh sách Agents -->
        <div class="agents-panel">
            <h3>Danh sách môi giới</h3>
            <div class="agent-list">
                @foreach($agents as $agent)
                <div class="agent-card" data-agent-id="{{ $agent->UserID }}">
                    <div class="agent-info">
                        <div class="agent-details">
                            <h4>{{ $agent->Name }}</h4>
                            <p class="agent-email">{{ $agent->Email }}</p>
                            <p class="agent-area">
                                Khu vực: {{ optional($agent->profile_agent)->AreaAgent ?? 'Chưa có' }}
                            </p>
                            <p class="property-count" id="count-{{ $agent->UserID }}">
                                Đang quản lý: <span class="{{ $agent->activePropertyCount >= 10 ? 'text-danger' : '' }}">{{ $agent->activePropertyCount }}/10</span>
                            </p>
                        </div>
                    </div>
                    <button class="select-agent-btn" data-agent-id="{{ $agent->UserID }}"
                            data-agent-name="{{ $agent->Name }}"
                            data-property-count="{{ $agent->activePropertyCount }}">Chọn</button>
                </div>
                @endforeach
            </div>
        </div>

        <!-- Panel bên phải: Danh sách Bất động sản -->
        <div class="properties-panel">
            <h3>Danh sách bất động sản</h3>
            <div class="property-list">
                <table class="property-table">
                    <thead >
                        <tr>
                            <th><input type="checkbox" id="selectAll"></th>
                            <th>ID</th>
                            <th>Tiêu đề</th>
                            <th>Chủ sở hữu</th>
                            <th>Môi giới</th>
                            <th>Quận/Huyện</th>
                            <th>Tỉnh/TP</th>
                            <th>Trạng thái</th>
                        </tr>
                    </thead>
                    <tbody id="propertyTableBody">
                        @foreach($properties as $property)
                        <tr data-property-id="{{ $property->PropertyID }}" data-status="{{ $property->Status }}">
                            <td><input type="checkbox" class="property-checkbox" value="{{ $property->PropertyID }}"></td>
                            <td>{{ $property->PropertyID }}</td>
                            <td>{{ $property->Title }}</td>
                            <td>{{ optional($property->chusohuu)->Name ?? 'Không có' }}</td>
                            <td class="agent-cell" data-agent-id="{{ $property->AgentID }}">
                                {{ optional($property->moigioi)->Name ?? 'Chưa phân công' }}
                            </td>
                            <td>{{ $property->District }}</td>
                            <td>{{ $property->Province }}</td>
                            <td>
                                <span class="status-badge status-{{ $property->Status }}">
                                    {{ $property->Status }}
                                </span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="pagination-container">
                @if($properties instanceof \Illuminate\Pagination\LengthAwarePaginator)
                    {{ $properties->links() }}
                @endif
            </div>
        </div>
    </div>

    <!-- Panel dưới: Nút lưu thay đổi -->
    <div class="action-panel">
        <div class="selected-info">
            <p>Đã chọn: <span id="selectedCount">0</span> bất động sản</p>
            <p>Môi giới: <span id="selectedAgent">Chưa chọn</span></p>
        </div>
        <form id="assignForm" style="display: inline;">
            @csrf
            <input type="hidden" name="agentId" id="agentIdInput">
            <div id="propertyIdsContainer"></div>
            <button type="button" id="saveAssignments" class="save-btn" disabled>Lưu thay đổi</button>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    let selectedAgentId = null;
    let selectedAgentActiveCount = 0;
    const propertyCheckboxes = document.querySelectorAll('.property-checkbox');
    const agentButtons = document.querySelectorAll('.select-agent-btn');
    const saveBtn = document.getElementById('saveAssignments');

    // Xử lý chọn agent
    agentButtons.forEach(button => {
        button.addEventListener('click', function() {
            const agentId = this.dataset.agentId;
            const agentName = this.dataset.agentName;
            const propertyCount = parseInt(this.dataset.propertyCount);

            // Bỏ chọn nút trước đó
            agentButtons.forEach(btn => {
                btn.parentElement.classList.remove('selected');
                btn.textContent = 'Chọn';
            });

            // Chọn nút hiện tại
            this.parentElement.classList.add('selected');
            this.textContent = 'Đã chọn';
            selectedAgentId = agentId;
            selectedAgentActiveCount = propertyCount;

            // Cập nhật thông tin agent được chọn
            document.getElementById('selectedAgent').textContent = agentName;

            // Kích hoạt nút lưu nếu có bất động sản được chọn
            updateSaveButtonState();
        });
    });

    // Xử lý chọn tất cả
    document.getElementById('selectAll').addEventListener('change', function() {
        propertyCheckboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
        });
        updateSelectedCount();
        updateSaveButtonState();
    });

    // Xử lý chọn từng bất động sản
    propertyCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            updateSelectedCount();
            updateSaveButtonState();
        });
    });

    // Cập nhật số lượng đã chọn
    function updateSelectedCount() {
        const checkedCount = document.querySelectorAll('.property-checkbox:checked').length;
        document.getElementById('selectedCount').textContent = checkedCount;
    }

    // Cập nhật trạng thái nút lưu
    function updateSaveButtonState() {
        const hasSelectedProperties = document.querySelectorAll('.property-checkbox:checked').length > 0;
        saveBtn.disabled = !(selectedAgentId && hasSelectedProperties);
    }

    // Xử lý lưu phân công
    saveBtn.addEventListener('click', function() {
        const selectedProperties = Array.from(document.querySelectorAll('.property-checkbox:checked'))
            .map(checkbox => checkbox.value);

        if (!selectedAgentId || selectedProperties.length === 0) {
            alert('Vui lòng chọn môi giới và ít nhất một bất động sản');
            return;
        }

        // Kiểm tra số lượng bất động sản active sẽ được thêm
        const selectedActiveCount = Array.from(document.querySelectorAll('.property-checkbox:checked'))
            .filter(checkbox => {
                const row = checkbox.closest('tr');
                return row.dataset.status === 'active' &&
                      (!row.querySelector('.agent-cell').dataset.agentId ||
                       row.querySelector('.agent-cell').dataset.agentId !== selectedAgentId);
            }).length;

        // Kiểm tra nếu vượt quá giới hạn
        if (selectedAgentActiveCount + selectedActiveCount > 10) {
            alert(`Môi giới đã quản lý ${selectedAgentActiveCount} bất động sản active. Không thể thêm ${selectedActiveCount} bất động sản active nữa (giới hạn 10).`);
            return;
        }

        // Hiển thị xác nhận
        if (!confirm(`Bạn có chắc chắn muốn phân công ${selectedProperties.length} bất động sản cho môi giới đã chọn?`)) {
            return;
        }

        // Cập nhật form với dữ liệu
        document.getElementById('agentIdInput').value = selectedAgentId;

        // Xóa các input propertyIds cũ
        document.getElementById('propertyIdsContainer').innerHTML = '';

        // Tạo các input propertyIds mới
        selectedProperties.forEach(propId => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'propertyIds[]';
            input.value = propId;
            document.getElementById('propertyIdsContainer').appendChild(input);
        });

        // Khởi tạo form data từ form (bao gồm CSRF token)
        const formData = new FormData(document.getElementById('assignForm'));

        // Gửi POST request đến route đã được định nghĩa
        fetch('{{ route("admin.assign.property.store") }}', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Phân công thành công!');
                // Cập nhật UI hiển thị môi giới mới
                selectedProperties.forEach(propId => {
                    const cell = document.querySelector(`tr[data-property-id="${propId}"] .agent-cell`);
                    if (cell) {
                        cell.textContent = document.getElementById('selectedAgent').textContent;
                        cell.dataset.agentId = selectedAgentId;
                    }
                });

                // Cập nhật số lượng BĐS mà agent đang quản lý
                const countSpan = document.querySelector(`.agent-card[data-agent-id="${selectedAgentId}"] .property-count span`);
                if (countSpan) {
                    const newCount = selectedAgentActiveCount + selectedActiveCount;
                    countSpan.textContent = `${newCount}/10`;
                    if (newCount >= 10) {
                        countSpan.classList.add('text-danger');
                    }
                }

                // Reset chọn
                document.querySelectorAll('.property-checkbox:checked').forEach(cb => {
                    cb.checked = false;
                });
                updateSelectedCount();
                updateSaveButtonState();
            } else {
                alert('Lỗi: ' + (data.message || 'Không thể phân công bất động sản'));
            }
        })
        .catch(error => {
            alert('Đã xảy ra lỗi: ' + error.message);
        });
    });

    // Xử lý tìm kiếm
    document.getElementById('searchButton').addEventListener('click', function() {
        const keyword = document.getElementById('searchInput').value.trim().toLowerCase();
        const rows = document.querySelectorAll('#propertyTableBody tr');

        rows.forEach(row => {
            const text = row.textContent.toLowerCase();
            row.style.display = text.includes(keyword) ? '' : 'none';
        });
    });

    // Xử lý lọc theo trạng thái
    document.getElementById('statusFilter').addEventListener('change', function() {
        const status = this.value.toLowerCase();
        const rows = document.querySelectorAll('#propertyTableBody tr');

        rows.forEach(row => {
            if (!status) {
                row.style.display = '';
                return;
            }

            const statusCell = row.querySelector('td:last-child span').textContent.toLowerCase();
            row.style.display = statusCell.includes(status) ? '' : 'none';
        });
    });
});
</script>
