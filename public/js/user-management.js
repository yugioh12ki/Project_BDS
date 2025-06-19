// User Management JavaScript - Simplified with Reload Approach
// Enhanced functionality for user table, search, and profile management

let searchTimeout;
let currentPage = 1;

// Debounced search function - chỉ tìm kiếm Name, Email, Phone
function debounceSearch() {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        searchUsers();
    }, 500); // Wait 500ms after user stops typing
}

// Main search function
function searchUsers() {
    const searchTerm = document.getElementById('searchUsers')?.value || '';
    const roleFilter = document.getElementById('roleFilter')?.value || 'all';

    // Add loading state
    const tableContainer = document.querySelector('.tab-content');
    if (tableContainer) {
        tableContainer.classList.add('loading');
    }

    // Simulate search (in real implementation, this would be AJAX call)
    setTimeout(() => {
        filterUsersLocally(searchTerm, roleFilter);
        if (tableContainer) {
            tableContainer.classList.remove('loading');
        }
    }, 300);
}

// Local filtering function (for demo purposes)
function filterUsersLocally(searchTerm, roleFilter) {
    const activeTab = document.querySelector('#active-users');
    const inactiveTab = document.querySelector('#inactive-users');

    if (activeTab) {
        filterTabUsers(activeTab, searchTerm, roleFilter);
    }
    if (inactiveTab) {
        filterTabUsers(inactiveTab, searchTerm, roleFilter);
    }

    updateUserCounts();
}

// Filter users in a specific tab - enhanced with proper empty state handling
function filterTabUsers(tabElement, searchTerm, roleFilter) {
    const rows = tabElement.querySelectorAll('tbody tr.user-row');
    let visibleCount = 0;

    rows.forEach(row => {
        const userName = row.querySelector('td:nth-child(2)')?.textContent?.toLowerCase() || '';
        const userEmail = row.querySelector('td:nth-child(3)')?.textContent?.toLowerCase() || '';
        const userPhone = row.querySelector('td:nth-child(4)')?.textContent?.toLowerCase() || '';
        const userRole = row.querySelector('.badge')?.textContent?.toLowerCase() || '';

        const searchLower = searchTerm.toLowerCase();

        // Search only in Name, Email, Phone as requested
        const matchesSearch = !searchTerm ||
            userName.includes(searchLower) ||
            userEmail.includes(searchLower) ||
            userPhone.includes(searchLower);

        const matchesRole = roleFilter === 'all' || userRole.includes(roleFilter.toLowerCase());

        if (matchesSearch && matchesRole) {
            row.style.display = '';
            row.classList.add('fade-in');
            visibleCount++;
        } else {
            row.style.display = 'none';
            row.classList.remove('fade-in');
        }
    });

    // Handle empty state for search results
    const tableBody = tabElement.querySelector('tbody');
    const existingEmptyState = tableBody.querySelector('.empty-state');
    const totalUserRows = rows.length;

    if (visibleCount === 0 && totalUserRows > 0) {
        // Has users but none visible due to search/filter - show search empty state
        if (existingEmptyState) {
            existingEmptyState.remove();
        }
        const emptyRow = document.createElement('tr');
        emptyRow.className = 'empty-state search-empty';
        emptyRow.innerHTML = `
            <td colspan="6" class="text-center py-4">
                <div class="text-muted">
                    <i class="fas fa-search fa-3x mb-3"></i>
                    <h5>Không tìm thấy kết quả</h5>
                    <p>Thử thay đổi từ khóa tìm kiếm hoặc bộ lọc.</p>
                </div>
            </td>
        `;
        tableBody.appendChild(emptyRow);
    } else if (visibleCount > 0) {
        // Remove search empty state if users are visible
        const searchEmpty = tableBody.querySelector('.search-empty');
        if (searchEmpty) {
            searchEmpty.remove();
        }
    }
    // If totalUserRows === 0, keep the original empty state from Blade template
}

// Update user counts in badges - enhanced version
function updateUserCounts() {
    const activeTab = document.querySelector('#active-users-tab');
    const inactiveTab = document.querySelector('#inactive-users-tab');

    if (activeTab) {
        // Count only visible user rows (not search empty states)
        const activeVisible = document.querySelectorAll('#active-users tbody tr.user-row[style=""], #active-users tbody tr.user-row:not([style*="none"])').length;
        const activeBadge = activeTab.querySelector('.badge');
        if (activeBadge) {
            activeBadge.textContent = activeVisible;
        }
    }

    if (inactiveTab) {
        // Count only visible user rows (not search empty states)
        const inactiveVisible = document.querySelectorAll('#inactive-users tbody tr.user-row[style=""], #inactive-users tbody tr.user-row:not([style*="none"])').length;
        const inactiveBadge = inactiveTab.querySelector('.badge');
        if (inactiveBadge) {
            inactiveBadge.textContent = inactiveVisible;
        }
    }
}

// Create user by role
function createUserByRole(role) {
    // In real implementation, this would open a modal or redirect to creation form
    const modal = new bootstrap.Modal(document.getElementById('createUserModal'));
    if (modal) {
        // Set role in modal
        const roleSelect = document.getElementById('createUserRole');
        if (roleSelect) {
            roleSelect.value = role;
        }
        modal.show();
    } else {
        alert(`Tạo user mới với role: ${role}`);
    }
}

// Tab functionality for active/inactive users
function initializeTabs() {
    const activeTab = document.getElementById('active-users-tab');
    const inactiveTab = document.getElementById('inactive-users-tab');

    if (activeTab && inactiveTab) {
        // Update tab counts when switching
        activeTab.addEventListener('shown.bs.tab', updateTabCounts);
        inactiveTab.addEventListener('shown.bs.tab', updateTabCounts);
    }
}

// Update tab counts - enhanced version
function updateTabCounts() {
    // Count actual user rows (excluding empty states)
    const activeCount = document.querySelectorAll('#active-users tbody tr[data-user-id]').length;
    const inactiveCount = document.querySelectorAll('#inactive-users tbody tr[data-user-id]').length;

    // Update tab badges
    const activeBadge = document.querySelector('#active-users-tab .badge');
    const inactiveBadge = document.querySelector('#inactive-users-tab .badge');

    if (activeBadge) activeBadge.textContent = activeCount;
    if (inactiveBadge) inactiveBadge.textContent = inactiveCount;

    // Debug logging
    console.log(`Tab counts updated: Active=${activeCount}, Inactive=${inactiveCount}`);
}

// Enhanced toggle user status for tab system with beautiful confirmation modal
function toggleUserStatus(userId, currentStatus) {
    const userRow = document.querySelector(`tr[data-user-id="${userId}"]`);
    const userName = userRow ? userRow.querySelector('.fw-bold.text-dark, .fw-bold.text-muted')?.textContent?.trim() : 'người dùng này';

    const actionText = currentStatus === 'active' ? 'vô hiệu hóa' : 'kích hoạt';
    const newStatus = currentStatus === 'active' ? 'inactive' : 'active';

    // Show enhanced confirmation modal
    showStatusConfirmationModal(userId, userName, currentStatus, newStatus, actionText);
}

// Show beautiful confirmation modal for status change
function showStatusConfirmationModal(userId, userName, currentStatus, newStatus, actionText) {
    const modal = document.getElementById('confirmStatusModal');
    const modalHeader = document.getElementById('confirmModalHeader');
    const modalIcon = document.getElementById('confirmModalIcon');
    const modalTitle = document.getElementById('confirmModalTitle');
    const modalMessage = document.getElementById('confirmModalMessage');
    const modalWarning = document.getElementById('confirmModalWarning');
    const modalWarningText = document.getElementById('confirmModalWarningText');
    const confirmButton = document.getElementById('confirmStatusAction');

    // Configure modal based on action type
    if (currentStatus === 'active') {
        // Deactivating user
        modalHeader.className = 'modal-header bg-warning text-white';
        modalIcon.innerHTML = '<i class="fas fa-user-times fa-4x text-warning"></i>';
        modalTitle.textContent = `Vô hiệu hóa tài khoản`;
        modalMessage.innerHTML = `Bạn có chắc chắn muốn <strong>vô hiệu hóa</strong> tài khoản của <strong>"${userName}"</strong>?`;
        modalWarningText.textContent = '⚠️ Tài khoản sẽ bị vô hiệu hóa và không thể đăng nhập vào hệ thống.';
        modalWarning.className = 'alert alert-warning';
        confirmButton.className = 'btn btn-warning';
        confirmButton.innerHTML = '<i class="fas fa-user-times me-2"></i>Vô hiệu hóa';
    } else {
        // Activating user
        modalHeader.className = 'modal-header bg-success text-white';
        modalIcon.innerHTML = '<i class="fas fa-user-check fa-4x text-success"></i>';
        modalTitle.textContent = `Kích hoạt tài khoản`;
        modalMessage.innerHTML = `Bạn có chắc chắn muốn <strong>kích hoạt</strong> tài khoản của <strong>"${userName}"</strong>?`;
        modalWarningText.textContent = '✅ Tài khoản sẽ được kích hoạt và có thể đăng nhập vào hệ thống.';
        modalWarning.className = 'alert alert-success';
        confirmButton.className = 'btn btn-success';
        confirmButton.innerHTML = '<i class="fas fa-user-check me-2"></i>Kích hoạt';
    }

    // Set up confirmation action
    confirmButton.onclick = function() {
        // Hide modal first
        const bootstrapModal = bootstrap.Modal.getInstance(modal);
        bootstrapModal.hide();

        // Execute the actual status change
        executeStatusChange(userId, userName, currentStatus, newStatus);
    };

    // Show modal
    const bootstrapModal = new bootstrap.Modal(modal);
    bootstrapModal.show();
}

// Execute the actual status change with page reload for reliability
function executeStatusChange(userId, userName, currentStatus, newStatus) {
    const userRow = document.querySelector(`tr[data-user-id="${userId}"]`);
    const toggleBtn = userRow?.querySelector(`button[onclick*="toggleUserStatus('${userId}',"]`);

    // Show loading state with full page overlay
    showPageLoading(`Đang ${newStatus === 'active' ? 'kích hoạt' : 'vô hiệu hóa'} tài khoản "${userName}"...`);

    // Disable the button
    if (toggleBtn) {
        toggleBtn.disabled = true;
        toggleBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
    }

    fetch(`/admin/users/${userId}/toggle-status`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
        },
        body: JSON.stringify({
            status: newStatus
        })
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP ${response.status}: ${response.statusText}`);
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            // Show success message briefly before reload
            const successMessage = `✅ Tài khoản "${userName}" đã được ${newStatus === 'active' ? 'kích hoạt' : 'vô hiệu hóa'} thành công!`;
            showNotification(successMessage, 'success');

            // Log activity
            console.log(`User ${userId} status changed from ${currentStatus} to ${newStatus}`);

            // Reload page after short delay to show success message
            setTimeout(() => {
                window.location.reload();
            }, 1500);
        } else {
            // Hide loading and show error
            hidePageLoading();
            restoreButton(toggleBtn);
            showNotification(`❌ Có lỗi xảy ra: ${data.message}`, 'error');
        }
    })
    .catch(error => {
        console.error('Toggle user status error:', error);

        // Hide loading and restore button
        hidePageLoading();
        restoreButton(toggleBtn);

        const errorMessage = error.message.includes('HTTP') ?
                           `❌ Lỗi kết nối: ${error.message}` :
                           '❌ Có lỗi xảy ra khi cập nhật trạng thái tài khoản!';
        showNotification(errorMessage, 'error');
    });
}

// Helper function to show page loading overlay
function showPageLoading(message = 'Đang xử lý...') {
    // Remove existing loading overlay if any
    const existingOverlay = document.getElementById('page-loading-overlay');
    if (existingOverlay) {
        existingOverlay.remove();
    }

    // Create loading overlay
    const overlay = document.createElement('div');
    overlay.id = 'page-loading-overlay';
    overlay.style.cssText = `
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.7);
        backdrop-filter: blur(3px);
        z-index: 9999;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-direction: column;
        animation: fadeIn 0.3s ease;
    `;

    overlay.innerHTML = `
        <div style="
            background: white;
            padding: 30px 40px;
            border-radius: 15px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            text-align: center;
            max-width: 400px;
            margin: 20px;
        ">
            <div style="
                width: 50px;
                height: 50px;
                border: 4px solid #e3f2fd;
                border-top: 4px solid #2196f3;
                border-radius: 50%;
                animation: spin 1s linear infinite;
                margin: 0 auto 20px;
            "></div>
            <h5 style="margin: 0 0 10px; color: #333; font-weight: 600;">${message}</h5>
            <p style="margin: 0; color: #666; font-size: 14px;">Vui lòng đợi trong giây lát...</p>
        </div>
    `;

    document.body.appendChild(overlay);

    // Add CSS animations if not already present
    if (!document.getElementById('loading-animations')) {
        const style = document.createElement('style');
        style.id = 'loading-animations';
        style.textContent = `
            @keyframes fadeIn {
                from { opacity: 0; }
                to { opacity: 1; }
            }

            @keyframes fadeOut {
                from { opacity: 1; }
                to { opacity: 0; }
            }

            @keyframes spin {
                0% { transform: rotate(0deg); }
                100% { transform: rotate(360deg); }
            }
        `;
        document.head.appendChild(style);
    }
}

// Helper function to hide page loading overlay
function hidePageLoading() {
    const overlay = document.getElementById('page-loading-overlay');
    if (overlay) {
        overlay.style.animation = 'fadeOut 0.3s ease forwards';
        setTimeout(() => overlay.remove(), 300);
    }
}

// Helper function to restore button state
function restoreButton(toggleBtn) {
    if (toggleBtn) {
        const originalHTML = toggleBtn.getAttribute('data-original-html');
        toggleBtn.disabled = false;
        if (originalHTML) {
            toggleBtn.innerHTML = originalHTML;
        } else {
            // Fallback restoration based on current status
            const isActiveBtn = toggleBtn.onclick.toString().includes("'active'");
            if (isActiveBtn) {
                toggleBtn.innerHTML = '<i class="fas fa-user-times"></i>';
                toggleBtn.className = 'btn btn-outline-danger btn-sm';
            } else {
                toggleBtn.innerHTML = '<i class="fas fa-user-check"></i>';
                toggleBtn.className = 'btn btn-outline-success btn-sm';
            }
        }
        toggleBtn.classList.remove('loading');
        toggleBtn.removeAttribute('data-original-html');
    }
}

// Enhanced notification system with better styling and UX
function showNotification(message, type = 'info') {
    // Remove existing notifications
    const existingNotifications = document.querySelectorAll('.notification-toast');
    existingNotifications.forEach(n => {
        n.style.animation = 'slideOut 0.3s ease forwards';
        setTimeout(() => n.remove(), 300);
    });

    // Create notification container if not exists
    let container = document.getElementById('notification-container');
    if (!container) {
        container = document.createElement('div');
        container.id = 'notification-container';
        container.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 9999;
            max-width: 400px;
            pointer-events: none;
        `;
        document.body.appendChild(container);
    }

    // Define notification styles and icons
    const notificationStyles = {
        success: {
            bg: 'linear-gradient(135deg, #27ae60 0%, #2ecc71 100%)',
            icon: 'fas fa-check-circle',
            color: '#ffffff'
        },
        error: {
            bg: 'linear-gradient(135deg, #e74c3c 0%, #c0392b 100%)',
            icon: 'fas fa-exclamation-triangle',
            color: '#ffffff'
        },
        warning: {
            bg: 'linear-gradient(135deg, #f39c12 0%, #e67e22 100%)',
            icon: 'fas fa-exclamation-circle',
            color: '#ffffff'
        },
        info: {
            bg: 'linear-gradient(135deg, #3498db 0%, #2980b9 100%)',
            icon: 'fas fa-info-circle',
            color: '#ffffff'
        }
    };

    const style = notificationStyles[type] || notificationStyles.info;

    // Create notification
    const notification = document.createElement('div');
    notification.className = 'notification-toast';
    notification.style.cssText = `
        background: ${style.bg};
        color: ${style.color};
        padding: 16px 20px;
        border-radius: 12px;
        box-shadow: 0 8px 32px rgba(0,0,0,0.2);
        margin-bottom: 12px;
        display: flex;
        align-items: center;
        gap: 12px;
        font-weight: 600;
        font-size: 14px;
        line-height: 1.4;
        border: 2px solid rgba(255,255,255,0.2);
        backdrop-filter: blur(10px);
        pointer-events: auto;
        cursor: pointer;
        animation: slideIn 0.4s cubic-bezier(0.68, -0.55, 0.265, 1.55);
        transition: all 0.3s ease;
        max-width: 100%;
        word-wrap: break-word;
    `;

    notification.innerHTML = `
        <i class="${style.icon}" style="font-size: 18px; flex-shrink: 0;"></i>
        <span style="flex: 1;">${message}</span>
        <button type="button" style="
            background: rgba(255,255,255,0.2);
            border: none;
            color: ${style.color};
            width: 24px;
            height: 24px;
            border-radius: 50%;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            transition: all 0.2s ease;
            flex-shrink: 0;
        " onclick="this.parentElement.remove()" onmouseover="this.style.background='rgba(255,255,255,0.3)'" onmouseout="this.style.background='rgba(255,255,255,0.2)'">
            <i class="fas fa-times"></i>
        </button>
    `;

    // Add hover effects
    notification.addEventListener('mouseenter', () => {
        notification.style.transform = 'translateX(-5px) scale(1.02)';
        notification.style.boxShadow = '0 12px 48px rgba(0,0,0,0.3)';
    });

    notification.addEventListener('mouseleave', () => {
        notification.style.transform = 'translateX(0) scale(1)';
        notification.style.boxShadow = '0 8px 32px rgba(0,0,0,0.2)';
    });

    // Click to dismiss
    notification.addEventListener('click', (e) => {
        if (e.target.tagName !== 'BUTTON' && e.target.tagName !== 'I') {
            notification.style.animation = 'slideOut 0.3s ease forwards';
            setTimeout(() => notification.remove(), 300);
        }
    });

    container.appendChild(notification);

    // Auto remove after duration based on message length
    const duration = Math.max(4000, message.length * 100); // Min 4s, longer for longer messages
    setTimeout(() => {
        if (notification.parentNode) {
            notification.style.animation = 'slideOut 0.3s ease forwards';
            setTimeout(() => notification.remove(), 300);
        }
    }, duration);

    // Add CSS animations if not already present
    if (!document.getElementById('notification-animations')) {
        const style = document.createElement('style');
        style.id = 'notification-animations';
        style.textContent = `
            @keyframes slideIn {
                from {
                    transform: translateX(100%) scale(0.8);
                    opacity: 0;
                }
                to {
                    transform: translateX(0) scale(1);
                    opacity: 1;
                }
            }

            @keyframes slideOut {
                from {
                    transform: translateX(0) scale(1);
                    opacity: 1;
                }
                to {
                    transform: translateX(100%) scale(0.8);
                    opacity: 0;
                }
            }

            .notification-toast:hover {
                transform: translateX(-5px) scale(1.02) !important;
            }
        `;
        document.head.appendChild(style);
    }
}

// Address management functions
let provinces = [];
let districts = [];
let wards = [];

// Load provinces
async function loadProvinces() {
    try {
        const response = await fetch('https://provinces.open-api.vn/api/p/');
        provinces = await response.json();

        const provinceSelects = document.querySelectorAll('[name="province"], [name="Province"]');
        provinceSelects.forEach(select => {
            select.innerHTML = '<option value="">Chọn Tỉnh/Thành phố</option>';
            provinces.forEach(province => {
                select.innerHTML += `<option value="${province.code}">${province.name}</option>`;
            });
        });
    } catch (error) {
        console.error('Error loading provinces:', error);
    }
}

// Load districts based on province
async function loadDistricts(provinceCode, targetSelectId = null) {
    if (!provinceCode) {
        const districtSelects = targetSelectId ?
            [document.getElementById(targetSelectId)] :
            document.querySelectorAll('[name="district"], [name="District"]');

        districtSelects.forEach(select => {
            if (select) {
                select.innerHTML = '<option value="">Chọn Quận/Huyện</option>';
            }
        });
        return;
    }

    try {
        const response = await fetch(`https://provinces.open-api.vn/api/p/${provinceCode}?depth=2`);
        const provinceData = await response.json();
        districts = provinceData.districts || [];

        const districtSelects = targetSelectId ?
            [document.getElementById(targetSelectId)] :
            document.querySelectorAll('[name="district"], [name="District"]');

        districtSelects.forEach(select => {
            if (select) {
                select.innerHTML = '<option value="">Chọn Quận/Huyện</option>';
                districts.forEach(district => {
                    select.innerHTML += `<option value="${district.code}">${district.name}</option>`;
                });
            }
        });
    } catch (error) {
        console.error('Error loading districts:', error);
    }
}

// Load wards based on district
async function loadWards(districtCode, targetSelectId = null) {
    if (!districtCode) {
        const wardSelects = targetSelectId ?
            [document.getElementById(targetSelectId)] :
            document.querySelectorAll('[name="ward"], [name="Ward"]');

        wardSelects.forEach(select => {
            if (select) {
                select.innerHTML = '<option value="">Chọn Phường/Xã</option>';
            }
        });
        return;
    }

    try {
        const response = await fetch(`https://provinces.open-api.vn/api/d/${districtCode}?depth=2`);
        const districtData = await response.json();
        wards = districtData.wards || [];

        const wardSelects = targetSelectId ?
            [document.getElementById(targetSelectId)] :
            document.querySelectorAll('[name="ward"], [name="Ward"]');

        wardSelects.forEach(select => {
            if (select) {
                select.innerHTML = '<option value="">Chọn Phường/Xã</option>';
                wards.forEach(ward => {
                    select.innerHTML += `<option value="${ward.code}">${ward.name}</option>`;
                });
            }
        });
    } catch (error) {
        console.error('Error loading wards:', error);
    }
}

// Clear search
function clearSearch() {
    const searchInput = document.getElementById('searchUsers');
    const roleFilter = document.getElementById('roleFilter');

    if (searchInput) searchInput.value = '';
    if (roleFilter) roleFilter.value = 'all';

    searchUsers();
}

// Initialize tabs and counts when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    // Initialize tab functionality
    initializeTabs();

    // Update initial tab counts
    updateTabCounts();

    // Load provinces for address dropdowns
    loadProvinces();

    // Setup search functionality
    const searchInput = document.getElementById('searchUsers');
    if (searchInput) {
        searchInput.addEventListener('input', debounceSearch);
    }

    const roleFilter = document.getElementById('roleFilter');
    if (roleFilter) {
        roleFilter.addEventListener('change', searchUsers);
    }

    // Setup keyboard shortcuts for modals
    document.addEventListener('keydown', function(e) {
        // ESC key to close modals
        if (e.key === 'Escape') {
            const openModal = document.querySelector('.modal.show');
            if (openModal) {
                const bootstrapModal = bootstrap.Modal.getInstance(openModal);
                if (bootstrapModal) {
                    bootstrapModal.hide();
                }
            }
        }

        // Enter key to confirm in status modal
        if (e.key === 'Enter') {
            const statusModal = document.getElementById('confirmStatusModal');
            if (statusModal && statusModal.classList.contains('show')) {
                const confirmButton = document.getElementById('confirmStatusAction');
                if (confirmButton && !confirmButton.disabled) {
                    confirmButton.click();
                }
            }
        }
    });

    // Setup province/district/ward cascading
    document.addEventListener('change', function(e) {
        if (e.target.matches('[name="province"], [name="Province"]')) {
            const districtId = e.target.id.replace('province', 'district').replace('Province', 'District');
            loadDistricts(e.target.value, districtId);
        }

        if (e.target.matches('[name="district"], [name="District"]')) {
            const wardId = e.target.id.replace('district', 'ward').replace('District', 'Ward');
            loadWards(e.target.value, wardId);
        }
    });

    console.log('User management system initialized successfully (reload approach)');
});
