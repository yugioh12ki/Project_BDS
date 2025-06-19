
document.addEventListener('DOMContentLoaded', function() {
    console.log('=== APPOINTMENT OWNER JS DEBUG LOG ===');
    console.log('Page loaded at:', new Date().toISOString());
    console.log('Bootstrap available:', typeof bootstrap !== 'undefined');
    console.log('jQuery available:', typeof $ !== 'undefined');

    // Bootstrap error detection
    if (typeof bootstrap === 'undefined') {
        console.error('❌ Bootstrap không khả dụng! Có thể gây lỗi modal.');
    } else {
        console.log('✅ Bootstrap đã sẵn sàng');
    }

    // Filter trạng thái
    document.querySelectorAll('.appointment-status-tabs [data-filter]').forEach(function(btn) {
        btn.addEventListener('click', function() {
            console.log('Filter clicked:', this.getAttribute('data-filter'));

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
    if (firstBtn) {
        console.log('Auto-clicking first filter button');
        firstBtn.click();
    }

    // Log modal elements
    const appointmentModal = document.getElementById('appointmentDetailModal');
    console.log('Appointment detail modal element:', appointmentModal);

    if (appointmentModal) {
        console.log('✅ Modal element found');

        // Test modal configuration
        try {
            // Use createSafeModal helper if available, otherwise use direct bootstrap.Modal
            const testModal = (typeof createSafeModal !== 'undefined') ?
                createSafeModal(appointmentModal, {
                    backdrop: true,
                    keyboard: true,
                    focus: true
                }) :
                new bootstrap.Modal(appointmentModal, {
                    backdrop: true,
                    keyboard: true,
                    focus: true
                });
            console.log('✅ Modal instance created successfully');

            // Add event listeners for debugging
            appointmentModal.addEventListener('show.bs.modal', function(event) {
                console.log('🎯 Modal is showing:', event);
            });

            appointmentModal.addEventListener('shown.bs.modal', function(event) {
                console.log('✅ Modal fully shown:', event);
            });

            appointmentModal.addEventListener('hide.bs.modal', function(event) {
                console.log('🔄 Modal is hiding:', event);
            });

            appointmentModal.addEventListener('hidden.bs.modal', function(event) {
                console.log('❌ Modal fully hidden:', event);
            });

        } catch (error) {
            console.error('❌ Lỗi khi tạo modal instance:', error);
        }
    } else {
        console.error('❌ Không tìm thấy appointment modal element');
    }

    console.log('Appointment owner script initialized successfully');
});

// Search filter theo tiêu đề, tên KH, môi giới với enhanced error handling
document.addEventListener('DOMContentLoaded', function() {
    console.log('🔍 Setting up search functionality...');

    let searchInput = document.getElementById('searchAppointment');
    if (searchInput) {
        console.log('✅ Search input found');
        searchInput.addEventListener('input', function() {
            try {
                let term = searchInput.value.trim().toLowerCase();
                let filter = document.querySelector('.appointment-status-tabs [data-filter].active')?.getAttribute('data-filter');
                let visibleCount = 0;

                console.log(`🔍 Searching for: "${term}" with filter: ${filter}`);

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

                console.log(`✅ Search completed. Found ${visibleCount} results.`);

            } catch (error) {
                console.error('❌ Error in search function:', error);
                showErrorMessage('Có lỗi xảy ra khi tìm kiếm. Vui lòng thử lại.');
            }
        });
    } else {
        console.warn('⚠️ Search input not found');
    }

    console.log('🔍 Search functionality setup complete');
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
    console.log(`🎯 Executing ${action} for appointment ${appointmentId}`);

    // Enhanced error handling with user feedback
    try {
        showLoading();
        const url = `/owner/appointments/${appointmentId}/${action}`;
        console.log(`🌐 Making request to: ${url}`);

        fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json',
            }
        })
        .then(response => {
            console.log('📡 Response received:', response.status, response.statusText);
            if (response.redirected) {
                // Laravel redirect with flash messages
                console.log('🔄 Redirected to:', response.url);
                window.location.href = response.url;
                return;
            }
            return response.json();
        })
        .then(data => {
            console.log('📊 Response data:', data);
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
            console.error('❌ Fetch Error:', error);
            showErrorMessage(`Có lỗi kết nối server: ${error.message}`);
        });

    } catch (error) {
        hideLoading();
        console.error('❌ Critical Error in executeAppointmentAction:', error);
        showErrorMessage(`Lỗi hệ thống: ${error.message}`);
    }
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

// ========== BOOTSTRAP ERROR HANDLING & DEBUGGING ==========

// Bootstrap Modal Error Handling
if (typeof bootstrap !== 'undefined') {
    console.log('🛡️ Setting up Bootstrap error handling...');

    // Create a safe modal helper function instead of overriding bootstrap.Modal
    window.createSafeModal = function(element, options) {
        try {
            console.log('🏗️ Creating Modal instance for:', element?.id || 'unknown element');

            // Ensure backdrop is properly configured
            const safeOptions = {
                backdrop: true,
                keyboard: true,
                focus: true,
                ...options
            };

            console.log('⚙️ Modal options:', safeOptions);

            return new bootstrap.Modal(element, safeOptions);
        } catch (error) {
            console.error('❌ BOOTSTRAP MODAL ERROR INTERCEPTED:', {
                error: error.message,
                element: element?.id || 'unknown',
                options: options,
                stack: error.stack
            });

            // Show user-friendly error
            showErrorMessage(`Lỗi giao diện modal: ${error.message}. Vui lòng refresh trang.`);

            // Try to create a basic modal as fallback
            try {
                return new bootstrap.Modal(element, { backdrop: true, keyboard: true, focus: true });
            } catch (fallbackError) {
                console.error('❌ Even fallback modal failed:', fallbackError);
                throw error;
            }
        }
    };

    console.log('✅ Bootstrap Modal error handling installed');
} else {
    console.error('❌ Bootstrap not available for error handling');
}

// Global Error Handler for Bootstrap-related errors
window.addEventListener('error', function(event) {
    if (event.error && (
        event.message.includes('backdrop') ||
        event.message.includes('Cannot read properties of undefined') ||
        event.message.includes('modal') ||
        event.filename?.includes('bootstrap')
    )) {
        console.error('🚨 BOOTSTRAP ERROR DETECTED:', {
            message: event.message,
            filename: event.filename,
            lineno: event.lineno,
            colno: event.colno,
            stack: event.error?.stack,
            timestamp: new Date().toISOString()
        });

        // Prevent default error handling
        event.preventDefault();

        // Show user notification
        const errorAlert = document.createElement('div');
        errorAlert.className = 'alert alert-warning alert-dismissible fade show position-fixed';
        errorAlert.style.cssText = 'top: 20px; right: 20px; z-index: 10000; max-width: 400px; font-size: 14px;';
        errorAlert.innerHTML = `
            <div class="d-flex align-items-start">
                <i class="fa fa-exclamation-triangle me-2 mt-1 flex-shrink-0"></i>
                <div>
                    <strong>Thông báo:</strong> Đã phát hiện lỗi giao diện nhỏ.
                    <br><small class="text-muted">Hệ thống vẫn hoạt động bình thường. Nếu gặp sự cố, vui lòng refresh trang.</small>
                    <br><small><strong>Chi tiết lỗi:</strong> ${event.message}</small>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        `;

        document.body.appendChild(errorAlert);

        // Auto remove after 15 seconds
        setTimeout(() => {
            if (errorAlert.parentNode) {
                errorAlert.remove();
            }
        }, 15000);

        return true; // Prevent default browser error handling
    }
});

// Additional debugging for modal events
document.addEventListener('DOMContentLoaded', function() {
    setTimeout(() => {
        console.log('🔍 Final system check...');
        console.log('Bootstrap Modal:', typeof bootstrap?.Modal);
        console.log('Available modals:', document.querySelectorAll('.modal').length);
        console.log('Appointment modal:', document.getElementById('appointmentDetailModal') ? '✅ Found' : '❌ Missing');
        console.log('=== OWNER APPOINTMENTS DEBUGGING COMPLETE ===');
    }, 1000);
});


