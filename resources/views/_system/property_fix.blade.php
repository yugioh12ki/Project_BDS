@extends('_layout._layadmin.app')

@section('property')
<div class="property-page-wrapper" style="margin:0;padding:0;">
    <ul class="nav nav-tabs" id="mainPropertyTabs" style="margin-top: 0;">
        <li class="nav-item">
            <a class="nav-link active" id="approve-tab" data-bs-toggle="tab" href="#approve-content">Kiểm Duyệt</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="assign-tab" data-bs-toggle="tab" href="#assign-content">Phân Công Môi Giới</a>
        </li>
    </ul>

    <div class="tab-content">
        <div class="tab-pane fade show active" id="approve-content">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5>
                    Kiểm Duyệt Bất Động Sản
                    {{ isset($typePro) ? '- ' . $typePro : '' }}
                    @if(isset($status) && $status == 'pending')
                        <span class="badge bg-warning">Đang chờ duyệt</span>
                    @endif
                </h5>

                <div class="view-controls">
                    <div class="btn-group" role="group" aria-label="View mode">
                        <button type="button" class="btn btn-outline-secondary active" id="listViewBtn">
                            <i class="fas fa-list"></i>
                        </button>
                        <button type="button" class="btn btn-outline-secondary" id="mapViewBtn">
                            <i class="fas fa-map-marked-alt"></i>
                        </button>
                    </div>
                </div>
            </div>

            <div class="row">
                <!-- Left column - Property list -->
                <div class="col-lg-7 property-list-column">
                    <div class="card">
                        <div class="card-body">
                            @if(isset($properties))
                                @include('_system.partialview.property_table', ['properties' => $properties, 'columns' => $columns])
                            @else
                                <div class="alert alert-info">Không có dữ liệu BĐS</div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Right column - Google Map -->
                <div class="col-lg-5 map-column">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h6 class="mb-0">Vị trí trên bản đồ</h6>
                            <div class="map-controls">
                                <button class="btn btn-sm btn-outline-primary" id="fitAllMarkersBtn" title="Hiển thị tất cả vị trí">
                                    <i class="fas fa-compress-arrows-alt"></i>
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="alert alert-info mb-2 py-2">
                                <small><i class="fas fa-info-circle"></i> Nhấn vào bất kỳ bất động sản nào trong danh sách để xem vị trí trên bản đồ. Nếu không có tọa độ, hệ thống sẽ tự động tìm kiếm theo địa chỉ.</small>
                            </div>
                            <div id="googleMap" style="width:100%;height:500px;"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="tab-pane fade" id="assign-content">
            <h5>Phân Công Môi Giới {{ isset($typePro) ? '- ' . $typePro : '' }}</h5>
            <div class="row">
                <!-- Danh sách môi giới bên trái -->
                <div class="col-lg-4">
                    <div class="card">
                        <div class="card-header">
                            <div class="d-flex justify-content-between align-items-center">
                                <h6 class="mb-0">Danh sách môi giới</h6>
                                <div class="input-group" style="width: 180px;">
                                    <input type="text" class="form-control form-control-sm" id="agentSearch" placeholder="Tìm kiếm...">
                                    <button class="btn btn-sm btn-outline-secondary" type="button">
                                        <i class="fas fa-search"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="small text-muted mt-1">
                                <i class="fas fa-sort-amount-up-alt me-1"></i> Sắp xếp theo số lượng phân công (ít → nhiều)
                            </div>
                        </div>
                        <div class="card-body p-0" style="max-height: 600px; overflow-y: auto;">
                            <div class="list-group list-group-flush" id="agentsList">
                                @foreach($agents as $agent)
                                <div class="list-group-item agent-item d-flex align-items-start p-3"
                                     data-agent-id="{{ $agent->UserID }}"
                                     style="cursor: pointer;">
                                    <div class="me-3 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px; border-radius: 50%; background-color: #e9f5ff; color: #0d6efd;">
                                        <i class="fas fa-user"></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        <div class="d-flex justify-content-between">
                                            <h6 class="mb-0">{{ $agent->Name }}</h6>
                                            <span class="badge bg-{{ $agent->active_property_count >= 10 ? 'danger' : ($agent->active_property_count >= 7 ? 'warning' : ($agent->active_property_count > 0 ? 'primary' : 'secondary')) }} rounded-pill"
                                                data-tooltip="{{ $agent->active_property_count }} bất động sản được quản lý">
                                                {{ $agent->active_property_count }}/10
                                            </span>
                                        </div>
                                        <small class="text-muted">{{ $agent->Email }}</small>
                                        <div class="small">{{ $agent->Phone ?? 'Không có SĐT' }}</div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                            <!-- Phân trang cho danh sách môi giới -->
                            <div class="agent-pagination p-3 border-top">
                                {{ $agents->links('pagination::bootstrap-5') }}
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Khu vực phân công bất động sản bên phải -->
                <div class="col-lg-8">
                    <div class="card">
                        <div class="card-header">
                            <div class="d-flex justify-content-between align-items-center">
                                <h6 class="mb-0"> Phân công bất động sản của <span id="selectedAgentName" class="text-primary">Chưa chọn môi giới</span></h6>
                                <div>
                                    <div class="btn-group" role="group" aria-label="Basic example">
                                        <button type="button" class="btn btn-sm btn-outline-primary" id="showAssignedBtn">
                                            Đã phân công
                                        </button>
                                        <button type="button" class="btn btn-sm btn-outline-secondary" id="showAvailableBtn">
                                            Sẵn sàng phân công
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-body p-0">
                            <!-- Hiển thị bất động sản đã phân công -->
                            <div id="assignedProperties" class="p-3">
                                <div id="loadingAssigned" class="text-center p-4">
                                    <div class="spinner-border text-primary" role="status">
                                        <span class="visually-hidden">Đang tải...</span>
                                    </div>
                                    <p class="mt-2">Vui lòng chọn môi giới để xem danh sách bất động sản</p>
                                </div>
                                <div class="table-responsive">
                                    <table class="table table-bordered table-hover align-middle mb-0" id="assignedPropertiesTable">
                                        <thead>
                                            <tr>
                                                <th width="40">ID</th>
                                                <th>Tiêu đề</th>
                                                <th>Chủ sở hữu</th>
                                                <th>Khu vực</th>
                                                <th>Trạng thái</th>
                                                <th width="100">Thao tác</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <!-- Sẽ được điền bởi JavaScript -->
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <!-- Hiển thị bất động sản có thể phân công -->
                            <div id="availableProperties" class="p-3" style="display: none;">
                                <div id="loadingAvailable" class="text-center p-4">
                                    <div class="spinner-border text-primary" role="status">
                                        <span class="visually-hidden">Đang tải...</span>
                                    </div>
                                    <p class="mt-2">Đang tải danh sách bất động sản khả dụng...</p>
                                </div>

                                <div class="mb-3">
                                    <div class="alert alert-info">
                                        Chọn bất động sản để phân công cho môi giới <strong id="assignAgentName"></strong>
                                    </div>
                                    <div class="d-flex justify-content-between">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="selectAllProperties">
                                            <label class="form-check-label" for="selectAllProperties">
                                                Chọn tất cả
                                            </label>
                                        </div>
                                        <button type="button" class="btn btn-primary btn-sm" id="assignSelectedBtn" disabled>
                                            Phân công (<span id="selectedCount">0</span>)
                                        </button>
                                    </div>
                                </div>
                                <div class="table-responsive">
                                    <table class="table table-bordered table-hover align-middle mb-0" id="availablePropertiesTable">
                                        <thead>
                                            <tr>
                                                <th width="40">
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" id="headerCheckbox">
                                                    </div>
                                                </th>
                                                <th width="40">ID</th>
                                                <th>Tiêu đề</th>
                                                <th>Chủ sở hữu</th>
                                                <th>Khu vực</th>
                                                <th>Trạng thái</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($properties->where('Status', 'active')->whereNull('AgentID') as $property)
                                            <tr class="property-row" data-property-id="{{ $property->PropertyID }}">
                                                <td>
                                                    <div class="form-check">
                                                        <input class="form-check-input property-checkbox"
                                                               type="checkbox"
                                                               value="{{ $property->PropertyID }}"
                                                               data-property-id="{{ $property->PropertyID }}">
                                                    </div>
                                                </td>
                                                <td>{{ $property->PropertyID }}</td>
                                                <td>{{ $property->Title }}</td>
                                                <td>{{ optional($property->chusohuu)->Name ?? 'Không có' }}</td>
                                                <td>{{ $property->District }}, {{ $property->Province }}</td>
                                                <td>
                                                    <span class="badge bg-success">{{ $property->Status }}</span>
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <script>
            document.addEventListener('DOMContentLoaded', function() {
                let selectedAgentId = null;
                let selectedAgentName = null;
                let selectedAgentCount = 0;

                // Debug - kiểm tra các phần tử DOM chính
                console.log('DOM Loading check:');
                console.log('assignedProperties:', document.getElementById('assignedProperties'));
                console.log('availableProperties:', document.getElementById('availableProperties'));
                console.log('assignedPropertiesTable:', document.getElementById('assignedPropertiesTable'));
                console.log('availablePropertiesTable:', document.getElementById('availablePropertiesTable'));
                console.log('showAssignedBtn:', document.getElementById('showAssignedBtn'));
                console.log('showAvailableBtn:', document.getElementById('showAvailableBtn'));

                // Xử lý khi click vào môi giới
                const agentItems = document.querySelectorAll('.agent-item');
                agentItems.forEach(item => {
                    item.addEventListener('click', function() {
                        // Loại bỏ trạng thái đã chọn từ tất cả các item
                        agentItems.forEach(agentItem => {
                            agentItem.classList.remove('active', 'bg-light');
                        });

                        // Đánh dấu item hiện tại là đã chọn
                        this.classList.add('active', 'bg-light');

                        // Thêm hiệu ứng "pulse" cho item được chọn
                        this.animate([
                            { transform: 'scale(0.98)', opacity: 0.9, easing: 'ease-in' },
                            { transform: 'scale(1)', opacity: 1, easing: 'ease-out' }
                        ], {
                            duration: 300
                        });
                  // Lưu thông tin môi giới được chọn                        selectedAgentId = this.getAttribute('data-agent-id');
                        selectedAgentName = this.querySelector('h6').textContent;

                        // Đặt giá trị mặc định là 0
                        selectedAgentCount = 0;

                        // Cố gắng lấy giá trị từ badge nếu có
                        const badge = this.querySelector('.badge');
                        if (badge) {
                            const badgeText = badge.textContent.trim();
                            console.log('Badge text:', badgeText);

                            try {
                                // Đảm bảo chỉ lấy con số đầu tiên từ chuỗi bằng regex
                                const matches = badgeText.match(/(\d+)/);
                                if (matches && matches[1]) {
                                    const parsedValue = parseInt(matches[1], 10);
                                    // Kiểm tra để đảm bảo là một số hợp lệ
                                    if (!isNaN(parsedValue)) {
                                        selectedAgentCount = parsedValue;
                                    } else {
                                        console.warn('Không thể chuyển đổi badge text thành số:', badgeText);
                                    }
                                } else {
                                    console.warn('Không tìm thấy số trong badge text:', badgeText);
                                }
                            } catch (error) {
                                console.error('Lỗi khi xử lý badge text:', error);
                            }
                        } else {
                            console.warn('Không tìm thấy badge element');
                        }

                        console.log('Parsed agent count:', selectedAgentCount);

                        // Cập nhật tên môi giới trong các phần hiển thị
                        document.getElementById('selectedAgentName').textContent = selectedAgentName;
                        document.getElementById('assignAgentName').textContent = selectedAgentName;

                        const debugBadge = this.querySelector('.badge');
                        console.log('Badge element:', debugBadge);
                        console.log('Badge HTML content:', debugBadge ? debugBadge.outerHTML : 'No badge found');
                        console.log('Selected agent:', { id: selectedAgentId, name: selectedAgentName, count: selectedAgentCount }); // Debug info

                        // Hiển thị bất động sản đã phân công
                        loadAssignedProperties(selectedAgentId);

                        // Hiển thị tab đã phân công
                        document.getElementById('showAssignedBtn').click();
                    });
                });

                // Tìm kiếm môi giới
                const agentSearch = document.getElementById('agentSearch');
                if (agentSearch) {
                    agentSearch.addEventListener('input', function() {
                        const searchTerm = this.value.toLowerCase();
                        agentItems.forEach(item => {
                            const agentName = item.querySelector('h6').textContent.toLowerCase();
                            const agentEmail = item.querySelector('.text-muted').textContent.toLowerCase();
                            if (agentName.includes(searchTerm) || agentEmail.includes(searchTerm)) {
                                item.style.display = '';
                            } else {
                                item.style.display = 'none';
                            }
                        });
                    });
                }

                // Chuyển đổi tab giữa danh sách đã phân công và sẵn sàng phân công
                const showAssignedBtn = document.getElementById('showAssignedBtn');
                const showAvailableBtn = document.getElementById('showAvailableBtn');
                const assignedProperties = document.getElementById('assignedProperties');
                const availableProperties = document.getElementById('availableProperties');

                if (showAssignedBtn) {
                    showAssignedBtn.addEventListener('click', function() {
                        if (!selectedAgentId) {
                            alert('Vui lòng chọn môi giới trước');
                            return;
                        }
                        showAssignedBtn.classList.add('btn-outline-primary');
                        showAssignedBtn.classList.remove('btn-outline-secondary');
                        showAvailableBtn.classList.add('btn-outline-secondary');
                        showAvailableBtn.classList.remove('btn-outline-primary');
                        assignedProperties.style.display = '';
                        availableProperties.style.display = 'none';

                        // Đảm bảo bảng và dữ liệu hiển thị đúng
                        const tableDiv = document.querySelector('#assignedProperties .table-responsive');
                        if (tableDiv) tableDiv.style.display = 'block';
                    });
                }

                if (showAvailableBtn) {
                    showAvailableBtn.addEventListener('click', function() {
                        if (!selectedAgentId) {
                            alert('Vui lòng chọn môi giới trước');
                            return;
                        }
                        showAvailableBtn.classList.add('btn-outline-primary');
                        showAvailableBtn.classList.remove('btn-outline-secondary');
                        showAssignedBtn.classList.add('btn-outline-secondary');
                        showAssignedBtn.classList.remove('btn-outline-primary');
                        assignedProperties.style.display = 'none';
                        availableProperties.style.display = '';

                        // Tải bất động sản khả dụng (không có AgentID)
                        loadAvailableProperties();
                    });
                }

                // Chọn tất cả các bất động sản
                const headerCheckbox = document.getElementById('headerCheckbox');
                if (headerCheckbox) {
                    headerCheckbox.addEventListener('change', function() {
                        const propertyCheckboxes = document.querySelectorAll('.property-checkbox');
                        propertyCheckboxes.forEach(checkbox => {
                            checkbox.checked = this.checked;
                        });
                        updateSelectedCount();
                    });
                }

                // Cập nhật số lượng đã chọn
                function updateSelectedCount() {
                    const count = document.querySelectorAll('.property-checkbox:checked').length;
                    const selectedCountElem = document.getElementById('selectedCount');
                    const assignSelectedBtn = document.getElementById('assignSelectedBtn');

                    if (selectedCountElem) {
                        selectedCountElem.textContent = count;
                    }

                    if (assignSelectedBtn) {
                        assignSelectedBtn.disabled = count === 0;
                    }
                }

                // Xử lý sự kiện khi chọn từng bất động sản
                document.querySelectorAll('.property-checkbox').forEach(checkbox => {
                    checkbox.addEventListener('change', updateSelectedCount);
                });

                // Phân công bất động sản đã chọn
                const assignSelectedBtn = document.getElementById('assignSelectedBtn');
                if (assignSelectedBtn) {
                    assignSelectedBtn.addEventListener('click', function() {
                        if (!selectedAgentId) {
                            alert('Vui lòng chọn môi giới trước');
                            return;
                        }

                        const selectedProperties = Array.from(document.querySelectorAll('.property-checkbox:checked')).map(cb => cb.value);

                        if (selectedProperties.length === 0) {
                            alert('Vui lòng chọn ít nhất một bất động sản');
                            return;
                        }

                        // Kiểm tra số lượng bất động sản active đã phân công
                        if (selectedAgentCount + selectedProperties.length > 10) {
                            alert(`Môi giới đã quản lý ${selectedAgentCount} bất động sản active. Không thể thêm ${selectedProperties.length} bất động sản nữa (giới hạn 10).`);
                            return;
                        }

                        // Xác nhận phân công
                        if (confirm(`Xác nhận phân công ${selectedProperties.length} bất động sản cho môi giới ${selectedAgentName}?`)) {
                            // Tạo form data
                            const formData = new FormData();
                            formData.append('agentId', selectedAgentId);
                            selectedProperties.forEach(propId => {
                                formData.append('propertyIds[]', propId);
                            });
                            formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));

                            // Gửi yêu cầu phân công
                            fetch('{{ route("admin.assign.properties") }}', {
                                method: 'POST',
                                body: formData
                            })
                            .then(response => response.json())
                            .then(data => {
                                if (data.success) {
                                    // Cập nhật UI
                                    showNotification('success', 'Phân công thành công!');

                                    // Cập nhật số lượng bất động sản của môi giới
                                    const badge = document.querySelector(`.agent-item[data-agent-id="${selectedAgentId}"] .badge`);

                                    // Xử lý khi không tìm thấy badge
                                    if (!badge) {
                                        console.warn('Không tìm thấy badge element cho agent ID:', selectedAgentId);
                                        return;
                                    }

                                    console.log('Badge element found:', badge);
                                    console.log('Current badge text:', badge.textContent);

                                    // Giá trị mặc định
                                    let badgeValue = 0;

                                    try {
                                        // Sử dụng regex để lấy con số đầu tiên từ chuỗi
                                        const badgeText = badge.textContent.trim();
                                        const matches = badgeText.match(/(\d+)/);

                                        if (matches && matches[1]) {
                                            const parsedValue = parseInt(matches[1], 10);
                                            // Kiểm tra để đảm bảo là một số hợp lệ
                                            if (!isNaN(parsedValue)) {
                                                badgeValue = parsedValue;
                                            } else {
                                                console.warn('Không thể chuyển đổi badge text thành số:', badgeText);
                                            }
                                        } else {
                                            console.warn('Không tìm thấy số trong badge text:', badgeText);
                                        }
                                    } catch (error) {
                                        console.error('Lỗi khi xử lý badge text:', error);
                                    }

                                    console.log('Parsed badge value:', badgeValue);
                                    const newCount = badgeValue + selectedProperties.length;
                                    console.log('New count calculated:', newCount);
                                    badge.textContent = `${newCount}/10`;

                                    // Cập nhật style của badge dựa trên số lượng
                                    if (newCount >= 10) {
                                        badge.classList.remove('bg-primary', 'bg-warning', 'bg-success', 'bg-secondary');
                                        badge.classList.add('bg-danger');
                                    } else if (newCount >= 7) {
                                        badge.classList.remove('bg-primary', 'bg-success', 'bg-danger', 'bg-secondary');
                                        badge.classList.add('bg-warning');
                                    } else if (newCount > 0) {
                                        badge.classList.remove('bg-warning', 'bg-danger', 'bg-success', 'bg-secondary');
                                        badge.classList.add('bg-primary');
                                    } else {
                                        badge.classList.remove('bg-primary', 'bg-warning', 'bg-danger');
                                        badge.classList.add('bg-secondary');
                                    }

                                    // Thêm hiệu ứng highlight cho badge khi được cập nhật
                                    badge.animate([
                                        { transform: 'scale(1.2)', opacity: 0.9 },
                                        { transform: 'scale(1)', opacity: 1 }
                                    ], {
                                        duration: 300,
                                        easing: 'ease-out'
                                    });

                                    // Cập nhật biến toàn cục
                                    selectedAgentCount = newCount;

                                    // Xóa các dòng đã phân công khỏi bảng
                                    selectedProperties.forEach(propId => {
                                        const row = document.querySelector(`.property-row[data-property-id="${propId}"]`);
                                        if (row) row.remove();
                                    });

                                    // Reset các checkbox
                                    if (headerCheckbox) {
                                        headerCheckbox.checked = false;
                                    }
                                    updateSelectedCount();

                                    // Hiển thị lại tab đã phân công và cập nhật danh sách
                                    if (showAssignedBtn) {
                                        showAssignedBtn.click();
                                    }
                                    loadAssignedProperties(selectedAgentId);
                                } else {
                                    showNotification('error', data.message || 'Có lỗi xảy ra khi phân công');
                                }
                            })
                            .catch(error => {
                                console.error(error);
                                showNotification('error', 'Có lỗi xảy ra khi phân công');
                            });
                        }
                    });
                }

                // Hàm tải danh sách bất động sản đã phân công cho môi giới                function loadAssignedProperties(agentId) {
                    const loadingDiv = document.getElementById('loadingAssigned');
                    const tableDiv = document.querySelector('#assignedProperties .table-responsive');
                    const tableBody = document.querySelector('#assignedPropertiesTable tbody');

                    if (!loadingDiv || !tableDiv || !tableBody) {
                        console.error('Missing DOM elements for assigned properties');
                        return;
                    }

                    // Hiển thị loading
                    loadingDiv.style.display = 'block';

                    // Đảm bảo bảng hiển thị nhưng nội dung rỗng trong khi chờ tải
                    tableDiv.style.display = 'none'; // Ẩn đi trước để tránh hiển thị dữ liệu cũ
                    tableBody.innerHTML = '';

                    // Đảm bảo phần tử cha có thể nhìn thấy được (trong trường hợp bị ẩn)
                    const parentElement = tableDiv.closest('#assignedProperties');
                    if (parentElement) {
                        parentElement.style.display = 'block';
                    }

                    console.log('Current selectedAgentCount before fetching:', selectedAgentCount);

                    // Kiểm tra vị trí hiển thị của phần tử
                    if (loadingDiv.offsetParent === null) {
                        console.warn('loadingDiv không hiển thị trong DOM, có thể do phần tử cha bị ẩn');
                    }

                    // Không kiểm tra tableDiv vì chúng ta chủ động ẩn nó trong quá trình tải

                    console.log('Fetching properties for agent ID:', agentId);

                    // Gọi API lấy bất động sản đã phân công - FIX: Sửa URL để đảm bảo đúng định dạng ID của agent
                    // Thêm / ở cuối URL để đảm bảo không có lỗi định dạng
                    fetch(`{{ url('/admin/agent') }}/${agentId}/properties`)
                        .then(response => {
                            console.log('API Response status:', response.status);

                            // Kiểm tra nếu response không thành công
                            if (!response.ok) {
                                throw new Error(`Network response was not ok: ${response.status}`);
                            }
                            return response.json();
                        })                        .then(data => {
                            loadingDiv.style.display = 'none';
                            tableDiv.style.display = 'block';

                            console.log('Assigned properties data:', data); // Thêm log để debug
                              // Cập nhật lại selectedAgentCount dựa trên số lượng bất động sản active
                            let activeProperties = [];

                            // Kiểm tra nếu data không phải là null hoặc undefined
                            if (data && Array.isArray(data)) {
                                // Lọc các property có Status là "active" (không phân biệt hoa thường)
                                activeProperties = data.filter(prop =>
                                    prop && prop.Status &&
                                    typeof prop.Status === 'string' &&
                                    prop.Status.toLowerCase() === 'active'
                                );
                            }

                            // Cập nhật selectedAgentCount và đảm bảo là một số
                            selectedAgentCount = activeProperties.length || 0;
                            console.log('Updated selectedAgentCount from API:', selectedAgentCount);

                            // Cập nhật badge hiển thị
                            const badge = document.querySelector(`.agent-item[data-agent-id="${selectedAgentId}"] .badge`);
                            if (badge) {
                                badge.textContent = `${selectedAgentCount}/10`;

                                // Cập nhật style của badge dựa trên số lượng
                                if (selectedAgentCount >= 10) {
                                    badge.classList.remove('bg-primary', 'bg-warning', 'bg-success');
                                    badge.classList.add('bg-danger');
                                } else if (selectedAgentCount >= 7) {
                                    badge.classList.remove('bg-primary', 'bg-success', 'bg-danger');
                                    badge.classList.add('bg-warning');
                                } else if (selectedAgentCount > 0) {
                                    badge.classList.remove('bg-warning', 'bg-danger', 'bg-success');
                                    badge.classList.add('bg-primary');
                                } else {
                                    badge.classList.remove('bg-primary', 'bg-warning', 'bg-danger');
                                    badge.classList.add('bg-secondary');
                                }

                                // Thêm hiệu ứng highlight cho badge khi được cập nhật
                                badge.animate([
                                    { transform: 'scale(1.2)', opacity: 0.9 },
                                    { transform: 'scale(1)', opacity: 1 }
                                ], {
                                    duration: 300,
                                    easing: 'ease-out'
                                });
                            }

                            if (data.length === 0) {
                                tableBody.innerHTML = `
                                    <tr>
                                        <td colspan="6" class="text-center py-3">
                                            <i class="fas fa-info-circle text-info me-2"></i>
                                            Không có bất động sản nào được phân công cho môi giới này
                                        </td>
                                    </tr>
                                `;
                            } else {
                                tableBody.innerHTML = data.map(property => `
                                    <tr>
                                        <td>${property.PropertyID}</td>
                                        <td>${property.Title}</td>
                                        <td>${property.OwnerName || 'Không có'}</td>
                                        <td>${property.District}, ${property.Province}</td>
                                        <td><span class="badge bg-${getBadgeColor(property.Status)}">${property.Status}</span></td>
                                        <td>
                                            <button class="btn btn-sm btn-outline-danger unassign-btn"
                                                    data-property-id="${property.PropertyID}">
                                                <i class="fas fa-times"></i> Hủy
                                            </button>
                                        </td>
                                    </tr>
                                `).join('');

                                // Xử lý sự kiện hủy phân công
                                document.querySelectorAll('.unassign-btn').forEach(btn => {
                                    btn.addEventListener('click', function() {
                                        const propertyId = this.getAttribute('data-property-id');
                                        if (confirm('Xác nhận hủy phân công bất động sản này?')) {
                                            // Gọi API hủy phân công
                                            const formData = new FormData();
                                            formData.append('propertyId', propertyId);
                                            formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));

                                            fetch('{{ route("admin.unassign.property") }}', {
                                                method: 'POST',
                                                body: formData
                                            })
                                            .then(response => response.json())
                                            .then(data => {
                                                if (data.success) {
                                                    // Cập nhật UI
                                                    showNotification('success', 'Hủy phân công thành công!');
                                                    // Cập nhật số lượng
                                                    const badge = document.querySelector(`.agent-item[data-agent-id="${selectedAgentId}"] .badge`);

                                                    // Xử lý khi không tìm thấy badge
                                                    if (!badge) {
                                                        console.warn('Không tìm thấy badge element cho agent ID:', selectedAgentId);
                                                        return;
                                                    }

                                                    console.log('Unassign - badge text:', badge.textContent);

                                                    // Giá trị mặc định
                                                    let currentCount = 0;

                                                    try {
                                                        // Sử dụng regex để lấy con số đầu tiên từ chuỗi
                                                        const badgeText = badge.textContent.trim();
                                                        const matches = badgeText.match(/(\d+)/);

                                                        if (matches && matches[1]) {
                                                            const parsedValue = parseInt(matches[1], 10);
                                                            // Kiểm tra để đảm bảo là một số hợp lệ
                                                            if (!isNaN(parsedValue)) {
                                                                currentCount = parsedValue;
                                                            } else {
                                                                console.warn('Không thể chuyển đổi badge text thành số:', badgeText);
                                                            }
                                                        } else {
                                                            console.warn('Không tìm thấy số trong badge text:', badgeText);
                                                        }
                                                    } catch (error) {
                                                        console.error('Lỗi khi xử lý badge text:', error);
                                                    }

                                                    console.log('Unassign - parsed count:', currentCount);
                                                    const newCount = Math.max(0, currentCount - 1);
                                                    console.log('Unassign - new count:', newCount);
                                                    badge.textContent = `${newCount}/10`;

                                                    // Cập nhật style của badge dựa trên số lượng
                                                    if (newCount >= 10) {
                                                        badge.classList.remove('bg-primary', 'bg-warning', 'bg-success', 'bg-secondary');
                                                        badge.classList.add('bg-danger');
                                                    } else if (newCount >= 7) {
                                                        badge.classList.remove('bg-primary', 'bg-success', 'bg-danger', 'bg-secondary');
                                                        badge.classList.add('bg-warning');
                                                    } else if (newCount > 0) {
                                                        badge.classList.remove('bg-warning', 'bg-danger', 'bg-success', 'bg-secondary');
                                                        badge.classList.add('bg-primary');
                                                    } else {
                                                        badge.classList.remove('bg-primary', 'bg-warning', 'bg-danger');
                                                        badge.classList.add('bg-secondary');
                                                    }

                                                    // Thêm hiệu ứng highlight cho badge khi được cập nhật
                                                    badge.animate([
                                                        { transform: 'scale(1.2)', opacity: 0.9 },
                                                        { transform: 'scale(1)', opacity: 1 }
                                                    ], {
                                                        duration: 300,
                                                        easing: 'ease-out'
                                                    });

                                                    // Cập nhật biến toàn cục
                                                    selectedAgentCount = newCount;

                                                    // Xóa dòng khỏi bảng
                                                    this.closest('tr').remove();

                                                    // Nếu không còn dòng nào, hiển thị thông báo
                                                    if (tableBody.children.length === 0) {
                                                        tableBody.innerHTML = `
                                                            <tr>
                                                                <td colspan="6" class="text-center py-3">
                                                                    <i class="fas fa-info-circle text-info me-2"></i>
                                                                    Không có bất động sản nào được phân công cho môi giới này
                                                                </td>
                                                            </tr>
                                                        `;
                                                    }
                                                } else {
                                                    showNotification('error', data.message || 'Có lỗi xảy ra khi hủy phân công');
                                                }
                                            })
                                            .catch(error => {
                                                console.error(error);
                                                showNotification('error', 'Có lỗi xảy ra khi hủy phân công');
                                            });
                                        }
                                    });
                                });
                            }
                        })
                        .catch(error => {
                            console.error('Error fetching assigned properties:', error);
                            loadingDiv.style.display = 'none';
                            tableDiv.style.display = 'block';
                            tableBody.innerHTML = `
                                <tr>
                                    <td colspan="6" class="text-center py-3">
                                        <i class="fas fa-exclamation-triangle text-danger me-2"></i>
                                        Có lỗi xảy ra khi tải dữ liệu: ${error.message}
                                    </td>
                                </tr>
                            `;
                        });
                }

                // Hàm tải danh sách bất động sản khả dụng (status = "active" và AgentID = NULL hoặc AgentID = "")
                function loadAvailableProperties() {
                    const loadingDiv = document.getElementById('loadingAvailable');
                    const tableDiv = document.querySelector('#availableProperties .table-responsive');
                    const tableBody = document.querySelector('#availablePropertiesTable tbody');

                    // Kiểm tra các phần tử DOM
                    if (!loadingDiv) console.error('loadingAvailable element not found');
                    if (!tableDiv) console.error('availableProperties .table-responsive element not found');
                    if (!tableBody) {
                        // Nếu tbody chưa tồn tại, tạo mới
                        const table = document.getElementById('availablePropertiesTable');
                        if (table) {
                            if (!table.querySelector('tbody')) {
                                const newTbody = document.createElement('tbody');
                                table.appendChild(newTbody);
                                console.log('Created new tbody element for availablePropertiesTable');
                                tableBody = newTbody;
                            }
                        } else {
                            console.error('availablePropertiesTable element not found');
                        }
                    }

                    // Hiển thị loading
                    if (loadingDiv) loadingDiv.style.display = 'block';
                    if (tableDiv) tableDiv.style.display = 'none';
                    if (tableBody) tableBody.innerHTML = '';

                    console.log('Fetching available properties with status=active');

                    // Gọi API lấy bất động sản khả dụng (chỉ lấy status="active" và AgentID=null hoặc AgentID="")
                    fetch('{{ route("admin.available.properties") }}?status=active')
                        .then(response => {
                            console.log('API Response status for available properties:', response.status);
                            // Kiểm tra nếu response không thành công
                            if (!response.ok) {
                                throw new Error(`Network response was not ok: ${response.status}`);
                            }
                            return response.json();
                        })
                        .then(data => {
                            if (loadingDiv) loadingDiv.style.display = 'none';
                            if (tableDiv) tableDiv.style.display = 'block';

                            if (!tableBody) return;

                            console.log('Available properties data:', data); // Thêm log để debug

                            if (data.length === 0) {
                                tableBody.innerHTML = `
                                    <tr>
                                        <td colspan="6" class="text-center py-3">
                                            <i class="fas fa-info-circle text-info me-2"></i>
                                            Không có bất động sản nào khả dụng để phân công
                                        </td>
                                    </tr>
                                `;
                            } else {
                                tableBody.innerHTML = data.map(property => `
                                    <tr class="property-row" data-property-id="${property.PropertyID}">
                                        <td>
                                            <div class="form-check">
                                                <input class="form-check-input property-checkbox" type="checkbox" value="${property.PropertyID}">
                                            </div>
                                        </td>
                                        <td>${property.PropertyID}</td>
                                        <td>${property.Title}</td>
                                        <td>${property.OwnerName || 'Không có'}</td>
                                        <td>${property.District}, ${property.Province}</td>
                                        <td><span class="badge bg-${getBadgeColor(property.Status)}">${property.Status}</span></td>
                                    </tr>
                                `).join('');

                                // Gán lại sự kiện cho các checkbox
                                document.querySelectorAll('.property-checkbox').forEach(checkbox => {
                                    checkbox.addEventListener('change', updateSelectedCount);
                                });

                                // Gán lại sự kiện cho cả hai selectAll checkbox
                                const selectAllCheckbox = document.getElementById('headerCheckbox');
                                const selectAllProperties = document.getElementById('selectAllProperties');

                                // Đồng bộ cả hai checkbox "Chọn tất cả"
                                const syncCheckboxes = (isChecked) => {
                                    const propertyCheckboxes = document.querySelectorAll('.property-checkbox');
                                    propertyCheckboxes.forEach(checkbox => {
                                        checkbox.checked = isChecked;
                                    });

                                    // Đồng bộ trạng thái của cả hai checkbox
                                    if (selectAllCheckbox) selectAllCheckbox.checked = isChecked;
                                    if (selectAllProperties) selectAllProperties.checked = isChecked;

                                    updateSelectedCount();
                                };

                                // Gán sự kiện cho checkbox trong header
                                if (selectAllCheckbox) {
                                    selectAllCheckbox.checked = false;
                                    selectAllCheckbox.addEventListener('change', function() {
                                        syncCheckboxes(this.checked);
                                    });
                                }

                                // Gán sự kiện cho checkbox "Chọn tất cả" bên trên
                                if (selectAllProperties) {
                                    selectAllProperties.checked = false;
                                    selectAllProperties.addEventListener('change', function() {
                                        syncCheckboxes(this.checked);
                                    });
                                }
                            }
                        })
                        .catch(error => {
                            console.error('Error fetching available properties:', error);
                            if (loadingDiv) loadingDiv.style.display = 'none';
                            if (tableDiv) tableDiv.style.display = 'block';
                            if (tableBody) {
                                tableBody.innerHTML = `
                                    <tr>
                                        <td colspan="6" class="text-center py-3 text-danger">
                                            <i class="fas fa-exclamation-triangle me-2"></i>
                                            Đã xảy ra lỗi khi tải dữ liệu: ${error.message}
                                        </td>
                                    </tr>
                                `;
                            }
                        });
                }

                // Xác định màu cho badge dựa trên trạng thái
                function getBadgeColor(status) {
                    switch(status.toLowerCase()) {
                        case 'active': return 'success';
                        case 'pending': return 'warning';
                        case 'inactive': return 'secondary';
                        case 'rejected': return 'danger';
                        default: return 'info';
                    }
                }
            });
            </script>
        </div>
    </div>
</div>

<!-- Load app.js with PropertyManagement module first -->
<script src="{{ mix('js/app.js') }}"></script>

<!-- Notification function -->
<script>
// Function to show notification
function showNotification(type, message) {
    // Create notification element
    const notification = document.createElement('div');
    notification.className = `alert alert-${type === 'success' ? 'success' : type === 'info' ? 'info' : 'danger'} alert-dismissible fade show notification-toast`;
    notification.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    `;

    // Add to document
    document.body.appendChild(notification);

    // Auto-dismiss after 5 seconds
    setTimeout(() => {
        notification.classList.remove('show');
        setTimeout(() => {
            notification.remove();
        }, 300);
    }, 5000);
}
</script>

<!-- Additional styles for the agent assignment section -->
<style>
.agent-item {
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
    border-left: 3px solid transparent;
    cursor: pointer;
}

.agent-item:hover {
    background-color: #f0f7ff;
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.05);
    border-left: 3px solid #83b4ff;
}

.agent-item.active {
    background-color: #e9f5ff;
    border-left: 3px solid #0d6efd;
    box-shadow: 0 0 15px rgba(13, 110, 253, 0.15);
}

.agent-item:before {
    content: '';
    position: absolute;
    left: 0;
    bottom: 0;
    height: 3px;
    width: 0;
    background-color: #0d6efd;
    transition: width 0.3s ease;
}

.agent-item:hover:before {
    width: 100%;
}

.agent-item h6 {
    transition: color 0.3s ease;
}

.agent-item:hover h6 {
    color: #0d6efd;
}

.agent-item.active h6 {
    color: #0d6efd;
    font-weight: 600;
}

.property-row {
    cursor: pointer;
    transition: all 0.2s ease;
}

.property-row:hover {
    background-color: #f8f9fa;
}

.notification-toast {
    position: fixed;
    top: 20px;
    right: 20px;
    min-width: 300px;
    z-index: 9999;
    box-shadow: 0 5px 15px rgba(0,0,0,0.2);
    border-radius: 6px;
    animation: slide-in 0.3s ease-out forwards;
}

@keyframes slide-in {
    from { transform: translateX(100%); opacity: 0; }
    to { transform: translateX(0); opacity: 1; }
}

/* Styling cho các badge */
.badge {
    transition: all 0.3s ease;
    position: relative;
    cursor: default;
    display: inline-block;
    white-space: nowrap;
    text-align: center;
    min-width: 40px;
}

.badge:hover {
    transform: scale(1.1);
    box-shadow: 0 2px 5px rgba(0,0,0,0.1);
}

.badge:active {
    transform: scale(0.95);
}

.badge.bg-primary {
    background-color: #0d6efd !important;
}

.badge.bg-primary:hover {
    background-color: #0a58ca !important;
}

.badge.bg-danger {
    background-color: #dc3545 !important;
}

.badge.bg-danger:hover {
    background-color: #bb2d3b !important;
}

.badge.bg-success {
    background-color: #198754 !important;
}

.badge.bg-success:hover {
    background-color: #146c43 !important;
}

.badge.bg-warning {
    background-color: #ffc107 !important;
    color: #212529;
}

.badge.bg-warning:hover {
    background-color: #ffca2c !important;
}

.badge.bg-secondary {
    background-color: #6c757d !important;
}

.badge.bg-secondary:hover {
    background-color: #565e64 !important;
}

/* Thêm tooltip cho badge khi hover */
.badge::after {
    content: attr(data-tooltip);
    position: absolute;
    bottom: 100%;
    left: 50%;
    transform: translateX(-50%) scale(0);
    background-color: rgba(0,0,0,0.8);
    color: white;
    padding: 4px 8px;
    border-radius: 4px;
    font-size: 12px;
    white-space: nowrap;
    opacity: 0;
    transition: all 0.2s ease;
    pointer-events: none;
    z-index: 1000;
}

.badge:hover::after {
    transform: translateX(-50%) scale(1);
    opacity: 1;
}

/* Style cho agent-item khi được hover */
.agent-item .small,
.agent-item small {
    transition: color 0.3s ease;
}

.agent-item:hover .small,
.agent-item:hover small {
    color: #0d6efd;
}

/* Agent pagination styling */
.agent-pagination {
    margin-top: 15px;
}

.agent-pagination .pagination {
    justify-content: center;
    margin-bottom: 0;
}

.agent-pagination .page-item.active .page-link {
    background-color: #0d6efd;
    border-color: #0d6efd;
}

.agent-pagination .page-link {
    color: #0d6efd;
    transition: all 0.2s ease;
}

.agent-pagination .page-link:hover {
    background-color: #e9f5ff;
    transform: translateY(-1px);
    box-shadow: 0 2px 5px rgba(0,0,0,0.05);
}
</style>

@endsection
