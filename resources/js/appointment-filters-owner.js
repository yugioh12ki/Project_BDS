// Owner Appointment Filters
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM loaded, initializing owner appointment functionality...');
    
    let filtersInitialized = false;
    let searchFunctionInitialized = false;
    
    function initializeAppointmentFilters() {
        console.log('Initializing appointment filters...');
        
        // Wait a bit to ensure tab content is loaded
        setTimeout(() => {
            // Xử lý filter cho các nút trạng thái (chỉ trong tab List)
            const filterButtons = document.querySelectorAll('#list [data-filter]');
            const appointmentRows = document.querySelectorAll('#list tbody tr[data-status]');
            
            console.log('Filter buttons found:', filterButtons.length);
            console.log('Appointment rows found:', appointmentRows.length);
            
            // Log data-status values for debugging
            appointmentRows.forEach((row, index) => {
                console.log(`Row ${index + 1} data-status:`, row.getAttribute('data-status'));
            });
            
            // Only proceed if we have filter buttons (means we're in List tab)
            if (filterButtons.length === 0) {
                console.log('No filter buttons found - retrying in 500ms...');
                if (!filtersInitialized) {
                    setTimeout(initializeAppointmentFilters, 500);
                }
                return;
            }
            
            if (filtersInitialized) {
                console.log('Filters already initialized, skipping...');
                return;
            }
            
            filtersInitialized = true;
            console.log('Setting up filter event listeners...');
            
            // Clear any existing event listeners by cloning buttons
            filterButtons.forEach(button => {
                const newButton = button.cloneNode(true);
                button.parentNode.replaceChild(newButton, button);
            });
            
            // Re-select buttons after cloning
            const newFilterButtons = document.querySelectorAll('#list [data-filter]');
              newFilterButtons.forEach(button => {
                button.addEventListener('click', function(e) {
                    e.preventDefault();
                    const filter = this.getAttribute('data-filter');
                    console.log('Filter clicked:', filter);
                    
                    // Show loading state
                    showLoading();
                    
                    // Disable all buttons during AJAX call
                    newFilterButtons.forEach(btn => btn.disabled = true);
                    
                    // Make AJAX request to get filtered appointments
                    fetch(`/owner/appointments/filter/${filter}`, {
                        method: 'GET',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json',
                            'Content-Type': 'application/json',
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Update button states
                            updateButtonStates(filter, newFilterButtons);
                            
                            // Update button counter
                            updateButtonCounter(filter, data.count);
                            
                            // Update table with new data
                            updateAppointmentTable(data.appointments);
                            
                            // Update search if active
                            const searchInput = document.getElementById('searchAppointment');
                            if (searchInput && searchInput.value.trim() !== '') {
                                performSearch();
                            }
                            
                            console.log(`AJAX filter '${filter}' applied, showing ${data.count} appointments`);
                        } else {
                            console.error('Filter error:', data.error);
                            showErrorMessage('Có lỗi xảy ra khi lọc dữ liệu');
                        }
                    })
                    .catch(error => {
                        console.error('AJAX error:', error);
                        showErrorMessage('Có lỗi kết nối. Vui lòng thử lại.');
                    })
                    .finally(() => {
                        // Re-enable buttons
                        newFilterButtons.forEach(btn => btn.disabled = false);
                        hideLoading();
                    });
                });
            });

            // Set default active filter (Chờ Xác Nhận)
            const defaultButton = document.querySelector('#list [data-filter="khoitao"]');
            if (defaultButton && !document.querySelector('#list [data-filter].active')) {
                console.log('Setting default filter to "khoitao"...');
                defaultButton.click();
            }
              // Search functionality - updated for AJAX
            function performSearch() {
                const searchInput = document.getElementById('searchAppointment');
                if (!searchInput) return;
                
                const searchTerm = searchInput.value.toLowerCase();
                const activeFilter = document.querySelector('#list .btn-group [data-filter].active')?.getAttribute('data-filter') || 'khoitao';
                
                console.log('Search term:', searchTerm, 'Active filter:', activeFilter);
                
                const currentRows = document.querySelectorAll('#list tbody tr[data-status]');
                let visibleCount = 0;
                
                currentRows.forEach(row => {
                    // Skip the "no data" row
                    if (row.children.length === 1 && row.children[0].getAttribute('colspan')) {
                        return;
                    }
                    
                    const propertyTitle = row.querySelector('td:first-child .fw-medium')?.textContent?.toLowerCase() || '';
                    const agentName = row.querySelector('td:nth-child(3) .fw-medium')?.textContent?.toLowerCase() || '';
                    const customerInfo = row.querySelector('td:nth-child(4)')?.textContent?.toLowerCase() || '';
                    
                    // Check if matches search term
                    const matchesSearch = searchTerm === '' || 
                        propertyTitle.includes(searchTerm) || 
                        agentName.includes(searchTerm) ||
                        customerInfo.includes(searchTerm);
                    
                    if (matchesSearch) {
                        row.style.display = '';
                        visibleCount++;
                    } else {
                        row.style.display = 'none';
                    }
                });
                
                showFilterMessage(visibleCount, activeFilter, searchTerm);
            }
            
            // Setup search input
            const searchInput = document.getElementById('searchAppointment');
            if (searchInput) {
                // Clear existing listeners
                const newSearchInput = searchInput.cloneNode(true);
                searchInput.parentNode.replaceChild(newSearchInput, searchInput);
                
                newSearchInput.addEventListener('input', performSearch);
            }
            
            function showFilterMessage(count, filter, searchTerm = '') {
                const tableContainer = document.querySelector('#list .table-responsive');
                if (!tableContainer) return;
                
                // Remove existing message
                const existingMessage = tableContainer.querySelector('.filter-message');
                if (existingMessage) {
                    existingMessage.remove();
                }
                
                if (count === 0) {
                    const filterNames = {
                        'khoitao': 'Chờ Xác Nhận',
                        'dangthuchien': 'Đang Thực Hiện', 
                        'hoanthanh': 'Hoàn Thành',
                        'huyhen': 'Đã Hủy'
                    };
                    
                    const message = document.createElement('div');
                    message.className = 'filter-message alert alert-info text-center mt-3';
                    message.innerHTML = `<i class="fa fa-info-circle me-2"></i>Không có lịch hẹn nào ${searchTerm ? `với từ khóa "${searchTerm}" ` : ''}trong trạng thái "${filterNames[filter]}"`;
                    tableContainer.appendChild(message);
                }
            }
            
        }, 100);
    }
    
    // Initialize filters immediately
    initializeAppointmentFilters();
    
    // Re-initialize when List tab is shown
    const listTab = document.getElementById('list-tab');
    if (listTab) {
        listTab.addEventListener('shown.bs.tab', function (e) {
            console.log('List tab shown, re-initializing filters...');
            filtersInitialized = false; // Reset flag
            setTimeout(initializeAppointmentFilters, 200);
        });
    }
    
    // Also initialize when tab content becomes visible
    const listTabPane = document.getElementById('list');
    if (listTabPane) {
        const observer = new MutationObserver(function(mutations) {
            mutations.forEach(function(mutation) {
                if (mutation.type === 'attributes' && mutation.attributeName === 'class') {
                    if (listTabPane.classList.contains('active') && !filtersInitialized) {
                        console.log('List tab pane is now active, initializing filters...');
                        setTimeout(initializeAppointmentFilters, 200);
                    }
                }
            });
        });
        observer.observe(listTabPane, { attributes: true });
    }
});

// Function to update appointment status - with AJAX
function updateAppointmentStatus(appointmentId, status) {
    if (confirm('Bạn có chắc chắn muốn thay đổi trạng thái cuộc hẹn này?')) {
        // Show loading
        const button = event.target;
        const originalText = button.innerHTML;
        button.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Đang xử lý...';
        button.disabled = true;
        
        // AJAX request to update status
        fetch('/owner/appointments/update-status', {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                appointment_id: appointmentId,
                status: status
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Refresh the current filter view
                const activeButton = document.querySelector('#list [data-filter].active');
                if (activeButton) {
                    activeButton.click();
                }
                
                // Show success message
                showSuccessMessage('Cập nhật trạng thái thành công!');
            } else {
                console.error('Status update error:', data.error);
                showErrorMessage(data.error || 'Có lỗi xảy ra khi cập nhật trạng thái');
                
                // Restore button
                button.innerHTML = originalText;
                button.disabled = false;
            }
        })
        .catch(error => {
            console.error('AJAX error:', error);
            showErrorMessage('Có lỗi kết nối. Vui lòng thử lại.');
            
            // Restore button
            button.innerHTML = originalText;
            button.disabled = false;
        });
    }
}

function updateButtonStates(activeFilter, buttons) {
    buttons.forEach(btn => {
        btn.classList.remove('active');
        btn.classList.remove('btn-warning', 'btn-info', 'btn-success', 'btn-danger');
        
        // Reset to correct outline color based on filter type
        const filterType = btn.getAttribute('data-filter');
        btn.classList.remove('btn-outline-warning', 'btn-outline-info', 'btn-outline-success', 'btn-outline-danger');
        if (filterType === 'khoitao') {
            btn.classList.add('btn-outline-warning');
        } else if (filterType === 'dangthuchien') {
            btn.classList.add('btn-outline-info');
        } else if (filterType === 'hoanthanh') {
            btn.classList.add('btn-outline-success');
        } else if (filterType === 'huyhen') {
            btn.classList.add('btn-outline-danger');
        }
    });
    
    // Set active button
    const activeButton = document.querySelector(`#list [data-filter="${activeFilter}"]`);
    if (activeButton) {
        activeButton.classList.add('active');
        activeButton.classList.remove('btn-outline-warning', 'btn-outline-info', 'btn-outline-success', 'btn-outline-danger');
        if (activeFilter === 'khoitao') {
            activeButton.classList.add('btn-warning');
        } else if (activeFilter === 'dangthuchien') {
            activeButton.classList.add('btn-info');
        } else if (activeFilter === 'hoanthanh') {
            activeButton.classList.add('btn-success');
        } else if (activeFilter === 'huyhen') {
            activeButton.classList.add('btn-danger');
        }
    }
}

function updateButtonCounter(activeFilter, count) {
    const activeButton = document.querySelector(`#list [data-filter="${activeFilter}"]`);
    if (activeButton) {
        const filterNames = {
            'khoitao': 'Chờ Xác Nhận',
            'dangthuchien': 'Đang Thực Hiện', 
            'hoanthanh': 'Hoàn Thành',
            'huyhen': 'Đã Hủy'
        };
        
        const filterName = filterNames[activeFilter];
        activeButton.textContent = `${filterName} (${count})`;
        activeButton.setAttribute('data-count', count);
    }
}

function updateAppointmentTable(appointments) {
    const tbody = document.querySelector('#list tbody');
    if (!tbody) return;
    
    if (appointments.length === 0) {
        tbody.innerHTML = '<tr><td colspan="5" class="text-center py-3">Không có lịch hẹn nào</td></tr>';
        return;
    }
    
    let html = '';
    appointments.forEach(appointment => {
        html += `
            <tr data-status="${appointment.data_status}">
                <td>
                    <div class="d-flex align-items-center">
                        <div class="property-thumb me-2">
                            ${appointment.property_image ? 
                                `<img src="${appointment.property_image}" class="rounded" style="width: 40px; height: 40px; object-fit: cover;" alt="Property">` :
                                `<div class="bg-light rounded d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                    <i class="fa fa-home text-muted"></i>
                                </div>`
                            }
                        </div>
                        <div>
                            <div class="fw-medium">${appointment.property_title}</div>
                            <small class="text-muted">${appointment.property_address}</small>
                        </div>
                    </div>
                </td>
                <td>
                    <div><strong>${appointment.date}</strong></div>
                    <small class="text-muted">${appointment.start_time} - ${appointment.end_time}</small>
                </td>
                <td>
                    <div class="d-flex align-items-center">
                        <div class="agent-avatar me-2">
                            <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center text-white" style="width: 32px; height: 32px; font-size: 12px;">
                                ${appointment.agent_initials}
                            </div>
                        </div>
                        <div>
                            <div class="fw-medium">${appointment.agent_name}</div>
                            <small class="text-muted">${appointment.agent_phone}</small>
                        </div>
                    </div>
                </td>
                <td>${appointment.status_badge}</td>
                <td class="text-end">
                    <div class="btn-group btn-group-sm">
                        <button class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#appointmentDetailModal" data-appointment-id="${appointment.id}">
                            <i class="fa fa-eye"></i> Chi tiết
                        </button>
                        ${appointment.can_confirm ? 
                            `<button class="btn btn-outline-success btn-sm" onclick="updateAppointmentStatus(${appointment.id}, 'Đang Thực Hiện')">
                                <i class="fa fa-check"></i> Xác nhận
                            </button>
                            <button class="btn btn-outline-danger btn-sm" onclick="updateAppointmentStatus(${appointment.id}, 'Hủy Hẹn')">
                                <i class="fa fa-times"></i> Hủy
                            </button>` : ''
                        }
                    </div>
                </td>
            </tr>
        `;
    });
    
    tbody.innerHTML = html;
}

function showLoading() {
    const tableContainer = document.querySelector('#list .table-responsive');
    if (tableContainer) {
        const loadingHtml = `
            <div class="loading-overlay position-absolute w-100 h-100 d-flex align-items-center justify-content-center" style="background: rgba(255,255,255,0.8); top: 0; left: 0; z-index: 1000;">
                <div class="text-center">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Đang tải...</span>
                    </div>
                    <div class="mt-2">Đang tải dữ liệu...</div>
                </div>
            </div>
        `;
        
        if (!tableContainer.querySelector('.loading-overlay')) {
            tableContainer.style.position = 'relative';
            tableContainer.insertAdjacentHTML('beforeend', loadingHtml);
        }
    }
}

function hideLoading() {
    const loadingOverlay = document.querySelector('#list .loading-overlay');
    if (loadingOverlay) {
        loadingOverlay.remove();
    }
}

function showErrorMessage(message) {
    const tableContainer = document.querySelector('#list .table-responsive');
    if (tableContainer) {
        // Remove existing messages
        const existingMessage = tableContainer.querySelector('.error-message');
        if (existingMessage) existingMessage.remove();
        
        const errorHtml = `
            <div class="error-message alert alert-danger d-flex align-items-center mt-3" role="alert">
                <i class="fa fa-exclamation-triangle me-2"></i>
                ${message}
                <button type="button" class="btn-close ms-auto" onclick="this.parentElement.remove()"></button>
            </div>
        `;
        tableContainer.insertAdjacentHTML('afterend', errorHtml);
        
        // Auto-hide after 5 seconds
        setTimeout(() => {
            const errorMsg = document.querySelector('.error-message');
            if (errorMsg) errorMsg.remove();
        }, 5000);
    }
}

function showSuccessMessage(message) {
    const tableContainer = document.querySelector('#list .table-responsive');
    if (tableContainer) {
        // Remove existing messages
        const existingMessage = tableContainer.querySelector('.success-message');
        if (existingMessage) existingMessage.remove();
        
        const successHtml = `
            <div class="success-message alert alert-success d-flex align-items-center mt-3" role="alert">
                <i class="fa fa-check-circle me-2"></i>
                ${message}
                <button type="button" class="btn-close ms-auto" onclick="this.parentElement.remove()"></button>
            </div>
        `;
        tableContainer.insertAdjacentHTML('afterend', successHtml);
        
        // Auto-hide after 3 seconds
        setTimeout(() => {
            const successMsg = document.querySelector('.success-message');
            if (successMsg) successMsg.remove();
        }, 3000);
    }
}
                    newFilterButtons.forEach(btn => {
                        btn.classList.remove('active');
                        btn.classList.remove('btn-warning', 'btn-info', 'btn-success', 'btn-danger');
                        
                        // Reset to correct outline color based on filter type
                        const filterType = btn.getAttribute('data-filter');
                        btn.classList.remove('btn-outline-warning', 'btn-outline-info', 'btn-outline-success', 'btn-outline-danger');
                        if (filterType === 'khoitao') {
                            btn.classList.add('btn-outline-warning');
                        } else if (filterType === 'dangthuchien') {
                            btn.classList.add('btn-outline-info');
                        } else if (filterType === 'hoanthanh') {
                            btn.classList.add('btn-outline-success');
                        } else if (filterType === 'huyhen') {
                            btn.classList.add('btn-outline-danger');
                        }
                    });
                    
                    // Add active class to clicked button
                    this.classList.add('active');
                    // Change to solid color for active button
                    this.classList.remove('btn-outline-warning', 'btn-outline-info', 'btn-outline-success', 'btn-outline-danger');
                    if (filter === 'khoitao') {
                        this.classList.add('btn-warning');
                    } else if (filter === 'dangthuchien') {
                        this.classList.add('btn-info');
                    } else if (filter === 'hoanthanh') {
                        this.classList.add('btn-success');
                    } else if (filter === 'huyhen') {
                        this.classList.add('btn-danger');
                    }
                    
                    // Filter appointments
                    let visibleCount = 0;
                    const currentRows = document.querySelectorAll('#list tbody tr[data-status]');
                    currentRows.forEach(row => {
                        const status = row.getAttribute('data-status');
                        let shouldShow = false;
                        
                        switch(filter) {
                            case 'khoitao':
                                shouldShow = status === 'khoitao';
                                break;
                            case 'dangthuchien':
                                shouldShow = status === 'dangthuchien';
                                break;
                            case 'hoanthanh':
                                shouldShow = status === 'hoanthanh';
                                break;
                            case 'huyhen':
                                shouldShow = status === 'huyhen';
                                break;
                        }
                        
                        if (shouldShow) {
                            row.style.display = '';
                            visibleCount++;
                        } else {
                            row.style.display = 'none';
                        }
                    });
                    
                    console.log(`Filter '${filter}' applied, showing ${visibleCount} appointments`);
                    
                    // Update search if active
                    const searchInput = document.getElementById('searchAppointment');
                    if (searchInput && searchInput.value.trim() !== '') {
                        performSearch();
                    }
                    
                    // Show message if no appointments match filter
                    showFilterMessage(visibleCount, filter);
                });
            });
            
            // Set default active filter (Chờ Xác Nhận)
            const defaultButton = document.querySelector('#list [data-filter="khoitao"]');
            if (defaultButton && !document.querySelector('#list [data-filter].active')) {
                console.log('Setting default filter...');
                defaultButton.click();
            }
            
            // Search functionality
            function performSearch() {
                const searchInput = document.getElementById('searchAppointment');
                if (!searchInput) return;
                
                const searchTerm = searchInput.value.toLowerCase();
                const activeFilter = document.querySelector('#list .btn-group [data-filter].active')?.getAttribute('data-filter') || 'khoitao';
                
                console.log('Search term:', searchTerm, 'Active filter:', activeFilter);
                
                const currentRows = document.querySelectorAll('#list tbody tr[data-status]');
                let visibleCount = 0;
                
                currentRows.forEach(row => {
                    const propertyTitle = row.querySelector('td:first-child')?.textContent?.toLowerCase() || '';
                    const agentName = row.querySelector('td:nth-child(3)')?.textContent?.toLowerCase() || '';
                    const status = row.getAttribute('data-status');
                    
                    // Check if matches search term
                    const matchesSearch = searchTerm === '' || propertyTitle.includes(searchTerm) || agentName.includes(searchTerm);
                    
                    // Check if matches current filter
                    let matchesFilter = false;
                    switch(activeFilter) {
                        case 'khoitao':
                            matchesFilter = status === 'khoitao';
                            break;
                        case 'dangthuchien':
                            matchesFilter = status === 'dangthuchien';
                            break;
                        case 'hoanthanh':
                            matchesFilter = status === 'hoanthanh';
                            break;
                        case 'huyhen':
                            matchesFilter = status === 'huyhen';
                            break;
                    }
                    
                    if (matchesSearch && matchesFilter) {
                        row.style.display = '';
                        visibleCount++;
                    } else {
                        row.style.display = 'none';
                    }
                });
                
                showFilterMessage(visibleCount, activeFilter, searchTerm);
            }
            
            // Setup search input
            const searchInput = document.getElementById('searchAppointment');
            if (searchInput) {
                // Clear existing listeners
                const newSearchInput = searchInput.cloneNode(true);
                searchInput.parentNode.replaceChild(newSearchInput, searchInput);
                
                newSearchInput.addEventListener('input', performSearch);
            }
            
            function showFilterMessage(count, filter, searchTerm = '') {
                const tableContainer = document.querySelector('#list .table-responsive');
                if (!tableContainer) return;
                
                // Remove existing message
                const existingMessage = tableContainer.querySelector('.filter-message');
                if (existingMessage) {
                    existingMessage.remove();
                }
                
                if (count === 0) {
                    const filterNames = {
                        'khoitao': 'Chờ Xác Nhận',
                        'dangthuchien': 'Đang Thực Hiện', 
                        'hoanthanh': 'Hoàn Thành',
                        'huyhen': 'Đã Hủy'
                    };
                    
                    const message = document.createElement('div');
                    message.className = 'filter-message alert alert-info text-center mt-3';
                    message.innerHTML = `<i class="fa fa-info-circle me-2"></i>Không có lịch hẹn nào ${searchTerm ? `với từ khóa "${searchTerm}" ` : ''}trong trạng thái "${filterNames[filter]}"`;
                    tableContainer.appendChild(message);
                }
            }
            
        }, 100);
    }
    
    // Initialize filters immediately
    initializeAppointmentFilters();
    
    // Re-initialize when List tab is shown
    const listTab = document.getElementById('list-tab');
    if (listTab) {
        listTab.addEventListener('shown.bs.tab', function (e) {
            console.log('List tab shown, re-initializing filters...');
            filtersInitialized = false; // Reset flag
            setTimeout(initializeAppointmentFilters, 200);
        });
    }
    
    // Also initialize when tab content becomes visible
    const listTabPane = document.getElementById('list');
    if (listTabPane) {
        const observer = new MutationObserver(function(mutations) {
            mutations.forEach(function(mutation) {
                if (mutation.type === 'attributes' && mutation.attributeName === 'class') {
                    if (listTabPane.classList.contains('active') && !filtersInitialized) {
                        console.log('List tab pane is now active, initializing filters...');
                        setTimeout(initializeAppointmentFilters, 200);
                    }
                }
            });
        });
        observer.observe(listTabPane, { attributes: true });
    }
});

// Function to update appointment status
function updateAppointmentStatus(appointmentId, status) {
    if (confirm('Bạn có chắc chắn muốn thay đổi trạng thái cuộc hẹn này?')) {
        // Here you would send AJAX request to update status
        console.log('Updating appointment', appointmentId, 'to status', status);
        // Reload page or update UI accordingly
        location.reload();
    }
}
