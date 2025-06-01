
document.addEventListener('DOMContentLoaded', function() {
    // Filter trạng thái
    document.querySelectorAll('.appointment-status-tabs [data-filter]').forEach(function(btn) {
        btn.addEventListener('click', function() {
            // Xóa class active ở tất cả, set active ở cái được click
            document.querySelectorAll('.appointment-status-tabs [data-filter]').forEach(b => b.classList.remove('active'));
            this.classList.add('active');

            let filter = this.getAttribute('data-filter');
            // Lọc từng dòng
            document.querySelectorAll('.appointment-row').forEach(function(row) {
                if(row.getAttribute('data-status') === filter) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });

            // Nếu không có dòng nào, hiện dòng trống
            let visibleRows = Array.from(document.querySelectorAll('.appointment-row')).filter(r => r.style.display !== 'none');
            let tbody = document.getElementById('appointmentsTbody');
            let emptyRow = document.getElementById('empty-appointment-row');
            if (visibleRows.length === 0) {
                if (!emptyRow) {
                    let tr = document.createElement('tr');
                    tr.id = 'empty-appointment-row';
                    tr.innerHTML = `<td colspan="9" class="text-center py-3 text-muted">Không có lịch hẹn nào ở trạng thái này</td>`;
                    tbody.appendChild(tr);
                }
            } else {
                if (emptyRow) emptyRow.remove();
            }
        });
    });

    // Khi load trang, filter mặc định trạng thái đầu tiên
    let firstBtn = document.querySelector('.appointment-status-tabs [data-filter].active');
    if (firstBtn) firstBtn.click();
});

// Search filter theo tiêu đề, tên KH, môi giới
document.addEventListener('DOMContentLoaded', function() {
    let searchInput = document.getElementById('searchAppointment');
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            let term = searchInput.value.trim().toLowerCase();
            let filter = document.querySelector('.appointment-status-tabs [data-filter].active')?.getAttribute('data-filter');
            let visibleCount = 0;
            document.querySelectorAll('.appointment-row').forEach(function(row) {
                // Ẩn theo status filter trước
                if(row.getAttribute('data-status') !== filter) {
                    row.style.display = 'none';
                    return;
                }
                // Kiểm tra search
                let text = row.textContent.toLowerCase();
                if (text.includes(term)) {
                    row.style.display = '';
                    visibleCount++;
                } else {
                    row.style.display = 'none';
                }
            });

            // Xử lý dòng trống
            let tbody = document.getElementById('appointmentsTbody');
            let emptyRow = document.getElementById('empty-appointment-row');
            if (visibleCount === 0) {
                if (!emptyRow) {
                    let tr = document.createElement('tr');
                    tr.id = 'empty-appointment-row';
                    tr.innerHTML = `<td colspan="9" class="text-center py-3 text-muted">Không có lịch hẹn nào phù hợp</td>`;
                    tbody.appendChild(tr);
                }
            } else {
                if (emptyRow) emptyRow.remove();
            }
        });
    }
});

function confirmAppointment(appointmentId, agentId) {
    if (!appointmentId || appointmentId == 0) {
        alert('ID lịch hẹn không hợp lệ!');
        return;
    }
    if (!confirm("Bạn có chắc chắn xác nhận lịch hẹn này?")) return;
    executeAppointmentAction(appointmentId, 'confirm', 'Xác nhận');
}

function cancelAppointment(appointmentId, agentId) {
    if (!appointmentId || appointmentId == 0) {
        alert('ID lịch hẹn không hợp lệ!');
        return;
    }
    if (!confirm("Bạn có chắc chắn muốn hủy lịch hẹn này?")) return;
    executeAppointmentAction(appointmentId, 'cancel', 'Hủy');
}

// Make functions globally available
window.confirmAppointment = confirmAppointment;
window.cancelAppointment = cancelAppointment;

// Helper functions for UI feedback
function showLoading() {
    // Create or show loading indicator
    let loader = document.getElementById('loading-indicator');
    if (!loader) {
        loader = document.createElement('div');
        loader.id = 'loading-indicator';
        loader.innerHTML = `
            <div class="d-flex justify-content-center align-items-center position-fixed" 
                 style="top: 0; left: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.3); z-index: 9999;">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Đang xử lý...</span>
                </div>
            </div>
        `;
        document.body.appendChild(loader);
    } else {
        loader.style.display = 'block';
    }
}

function hideLoading() {
    const loader = document.getElementById('loading-indicator');
    if (loader) {
        loader.style.display = 'none';
    }
}

function showSuccessMessage(message) {
    // Create success toast or alert
    const alert = document.createElement('div');
    alert.className = 'alert alert-success alert-dismissible fade show position-fixed';
    alert.style.cssText = 'top: 20px; right: 20px; z-index: 10000; min-width: 300px;';
    alert.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    document.body.appendChild(alert);
    
    // Auto remove after 5 seconds
    setTimeout(() => {
        if (alert.parentNode) {
            alert.parentNode.removeChild(alert);
        }
    }, 5000);
}

function showErrorMessage(message) {
    // Create error toast or alert
    const alert = document.createElement('div');
    alert.className = 'alert alert-danger alert-dismissible fade show position-fixed';
    alert.style.cssText = 'top: 20px; right: 20px; z-index: 10000; min-width: 300px;';
    alert.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    document.body.appendChild(alert);
    
    // Auto remove after 5 seconds
    setTimeout(() => {
        if (alert.parentNode) {
            alert.parentNode.removeChild(alert);
        }
    }, 5000);
}

function executeAppointmentAction(appointmentId, action, actionText) {
    console.log(`Executing ${action} for appointment ${appointmentId}`);
    showLoading();
    const url = `/owner/appointments/${appointmentId}/${action}`;
    console.log(`Making request to: ${url}`);
    
    fetch(url, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json',
        }
    })
    .then(response => {
        console.log('Response received:', response);
        if (response.redirected) {
            // Laravel redirect with flash messages
            console.log('Redirected to:', response.url);
            window.location.href = response.url;
            return;
        }
        return response.json();
    })
    .then(data => {
        console.log('Response data:', data);
        hideLoading();
        if (data) {
            if (data.success) {
                // Show success message
                showSuccessMessage(data.message || `Đã ${actionText.toLowerCase()} lịch hẹn thành công`);
                
                // Update UI immediately without page reload
                updateAppointmentRow(appointmentId, data.new_status);
                
                // Update status counts in tabs
                updateStatusCounts();
                
            } else {
                showErrorMessage(data.message || "Có lỗi xảy ra khi cập nhật");
            }
        }
    })
    .catch(error => {
        hideLoading();
        console.error('Error:', error);
        showErrorMessage("Có lỗi kết nối server");
    });
}

function updateAppointmentRow(appointmentId, newStatus) {
    // Find the appointment row
    const appointmentRows = document.querySelectorAll('.appointment-row');
    
    appointmentRows.forEach(row => {
        // Check if this is the row we need to update (you may need to adjust selector)
        const confirmBtn = row.querySelector(`[onclick*="confirmAppointment(${appointmentId}"]`);
        const cancelBtn = row.querySelector(`[onclick*="cancelAppointment(${appointmentId}"]`);
        
        if (confirmBtn || cancelBtn) {
            // Update status badge
            const statusCell = row.querySelector('td:nth-child(4)'); // Assuming status is in 4th column
            if (statusCell) {
                const statusBadge = statusCell.querySelector('.badge');
                if (statusBadge) {
                    // Update badge based on new status
                    statusBadge.className = 'badge'; // Reset classes
                    if (newStatus === 'Đang Thực Hiện') {
                        statusBadge.className = 'badge bg-info';
                        statusBadge.textContent = 'Đang Thực Hiện';
                    } else if (newStatus === 'Hủy Hẹn') {
                        statusBadge.className = 'badge bg-danger';
                        statusBadge.textContent = 'Đã Hủy';
                    }
                }
            }
            
            // Hide action buttons
            const actionCell = row.querySelector('td:last-child .btn-group');
            if (actionCell) {
                const confirmButton = actionCell.querySelector('.btn-outline-success');
                const cancelButton = actionCell.querySelector('.btn-outline-danger');
                
                if (confirmButton) confirmButton.style.display = 'none';
                if (cancelButton) cancelButton.style.display = 'none';
            }
            
            // Update row data-status attribute for filtering
            if (newStatus === 'Đang Thực Hiện') {
                row.setAttribute('data-status', 'dangthuchien');
            } else if (newStatus === 'Hủy Hẹn') {
                row.setAttribute('data-status', 'huyhen');
            }
            row.setAttribute('data-appointment-status', newStatus);
        }
    });
}

function updateStatusCounts() {
    // Count visible rows for each status
    const counts = {
        khoitao: 0,
        dangthuchien: 0,
        hoanthanh: 0,
        huyhen: 0
    };
    
    document.querySelectorAll('.appointment-row').forEach(row => {
        const status = row.getAttribute('data-status');
        if (status && counts.hasOwnProperty(status)) {
            counts[status]++;
        }
    });
    
    // Update tab badges
    const updateTabBadge = (tabId, count) => {
        const tab = document.getElementById(tabId);
        if (tab) {
            const badge = tab.querySelector('.badge');
            if (badge) {
                badge.textContent = count;
            }
        }
    };
    
    updateTabBadge('btnKhoiTao', counts.khoitao);
    updateTabBadge('btnDangThucHien', counts.dangthuchien);
    updateTabBadge('btnHoanThanh', counts.hoanthanh);
    updateTabBadge('btnHuyHen', counts.huyhen);
}


