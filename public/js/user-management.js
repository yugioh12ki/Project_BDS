// User Management JavaScript
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

// Filter users in a specific tab
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

    // Show/hide empty state
    const emptyState = tabElement.querySelector('.empty-state');
    const tableBody = tabElement.querySelector('tbody');

    if (visibleCount === 0 && !emptyState) {
        const emptyRow = document.createElement('tr');
        emptyRow.className = 'empty-state';
        emptyRow.innerHTML = `
            <td colspan="6" class="text-center py-4">
                <div class="text-muted">
                    <i class="fas fa-search fa-3x mb-3"></i>
                    <h5>Không tìm thấy kết quả</h5>
                    <p>Thử thay đổi từ khóa tìm kiếm hoặc bộ lọc.</p>
                </div>
            </td>
        `;
        if (tableBody) tableBody.appendChild(emptyRow);
    } else if (visibleCount > 0 && emptyState) {
        emptyState.remove();
    }
}

// Update user counts in badges
function updateUserCounts() {
    const activeTab = document.querySelector('#active-tab');
    const inactiveTab = document.querySelector('#inactive-tab');

    if (activeTab) {
        const activeVisible = document.querySelectorAll('#active-users tbody tr.user-row[style=""], #active-users tbody tr.user-row:not([style])').length;
        const activeBadge = activeTab.querySelector('.badge');
        if (activeBadge) {
            activeBadge.textContent = activeVisible;
        }
    }

    if (inactiveTab) {
        const inactiveVisible = document.querySelectorAll('#inactive-users tbody tr.user-row[style=""], #inactive-users tbody tr.user-row:not([style])').length;
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

// Update tab counts
function updateTabCounts() {
    const activeCount = document.querySelectorAll('#active-users tbody tr[data-user-id]').length;
    const inactiveCount = document.querySelectorAll('#inactive-users tbody tr[data-user-id]').length;

    const activeBadge = document.querySelector('#active-users-tab .badge');
    const inactiveBadge = document.querySelector('#inactive-users-tab .badge');

    if (activeBadge) activeBadge.textContent = activeCount;
    if (inactiveBadge) inactiveBadge.textContent = inactiveCount;
}

// Enhanced toggle user status for tab system
function toggleUserStatus(userId, currentStatus) {
    if (!confirm(`Bạn có chắc muốn ${currentStatus === 'active' ? 'vô hiệu hóa' : 'kích hoạt'} tài khoản này?`)) {
        return;
    }

    const newStatus = currentStatus === 'active' ? 'inactive' : 'active';

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
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Move user row to appropriate tab
            moveUserBetweenTabs(userId, currentStatus, newStatus);
            showNotification(`Tài khoản đã được ${newStatus === 'active' ? 'kích hoạt' : 'vô hiệu hóa'}!`, 'success');
            updateTabCounts();
        } else {
            showNotification('Có lỗi xảy ra: ' + data.message, 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Có lỗi xảy ra khi cập nhật trạng thái!', 'error');
    });
}

// Move user row between active/inactive tabs
function moveUserBetweenTabs(userId, oldStatus, newStatus) {
    const userRow = document.querySelector(`tr[data-user-id="${userId}"]`);
    if (!userRow) return;

    // Remove from current location
    userRow.remove();

    // Update row styling and data
    userRow.setAttribute('data-status', newStatus);

    if (newStatus === 'active') {
        // Move to active tab - update styling
        userRow.className = 'user-row border-start border-success border-3';
        userRow.style.backgroundColor = '';

        // Update avatar opacity
        const avatar = userRow.querySelector('img, .bg-secondary');
        if (avatar) {
            avatar.classList.remove('opacity-50', 'bg-secondary');
            if (avatar.tagName === 'DIV') {
                avatar.classList.add('bg-primary');
            }
        }

        // Update text colors
        userRow.querySelectorAll('.text-muted').forEach(el => {
            el.classList.remove('text-muted');
            el.classList.add('text-dark', 'fw-medium');
        });

        // Update action button
        const toggleBtn = userRow.querySelector('button[onclick*="toggleUserStatus"]');
        if (toggleBtn) {
            toggleBtn.className = 'btn btn-outline-danger btn-sm';
            toggleBtn.title = 'Vô hiệu hóa';
            toggleBtn.innerHTML = '<i class="fas fa-user-times"></i>';
            toggleBtn.setAttribute('onclick', `toggleUserStatus('${userId}', 'active')`);
        }

        // Add to active tab
        const activeTableBody = document.querySelector('#active-users tbody');
        if (activeTableBody) {
            activeTableBody.appendChild(userRow);
        }
    } else {
        // Move to inactive tab - update styling
        userRow.className = 'user-row border-start border-warning border-3 bg-light';

        // Update avatar opacity
        const avatar = userRow.querySelector('img, .bg-primary');
        if (avatar) {
            if (avatar.tagName === 'IMG') {
                avatar.classList.add('opacity-50');
            } else {
                avatar.classList.remove('bg-primary');
                avatar.classList.add('bg-secondary');
            }
        }

        // Update text colors
        userRow.querySelectorAll('.text-dark, .fw-medium').forEach(el => {
            el.classList.add('text-muted');
        });

        // Update action button
        const toggleBtn = userRow.querySelector('button[onclick*="toggleUserStatus"]');
        if (toggleBtn) {
            toggleBtn.className = 'btn btn-outline-success btn-sm';
            toggleBtn.title = 'Kích hoạt lại';
            toggleBtn.innerHTML = '<i class="fas fa-user-check"></i>';
            toggleBtn.setAttribute('onclick', `toggleUserStatus('${userId}', 'inactive')`);
        }

        // Add to inactive tab
        const inactiveTableBody = document.querySelector('#inactive-users tbody');
        if (inactiveTableBody) {
            inactiveTableBody.appendChild(userRow);
        }
    }

    // Add animation
    userRow.classList.add('fade-in');
}

// Enhanced notification system
function showNotification(message, type = 'info') {
    // Remove existing notifications
    const existingNotifications = document.querySelectorAll('.notification-toast');
    existingNotifications.forEach(n => n.remove());

    // Create notification
    const notification = document.createElement('div');
    notification.className = `notification-toast alert alert-${type === 'error' ? 'danger' : type} alert-dismissible fade show position-fixed`;
    notification.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';

    notification.innerHTML = `
        <i class="fas fa-${type === 'success' ? 'check-circle' : type === 'error' ? 'exclamation-triangle' : 'info-circle'} me-2"></i>
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;

    document.body.appendChild(notification);

    // Auto remove after 5 seconds
    setTimeout(() => {
        if (notification.parentNode) {
            notification.remove();
        }
    }, 5000);
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

// Profile management functions
function createProfile(userId, profileType) {
    const createUrl = `/admin/users/${userId}/create-${profileType.toLowerCase()}-profile`;

    fetch(createUrl, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification(`Profile ${profileType} đã được tạo thành công!`, 'success');
            // Reload modal or update UI

































































































        } else {
            showNotification('Có lỗi xảy ra khi tạo profile!', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Có lỗi xảy ra!', 'error');
    });
}

// Clear search
function clearSearch() {
    const searchInput = document.getElementById('searchUsers');
    const roleFilter = document.getElementById('roleFilter');

    if (searchInput) searchInput.value = '';
    if (roleFilter) roleFilter.value = 'all';

    searchUsers();
}

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    // Load provinces on page load
    loadProvinces();

    // Setup search input
    const searchInput = document.getElementById('searchUsers');
    if (searchInput) {
        searchInput.addEventListener('input', debounceSearch);
    }

    // Setup role filter
    const roleFilter = document.getElementById('roleFilter');
    if (roleFilter) {
        roleFilter.addEventListener('change', searchUsers);
    }

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

    // Setup modal handlers
    document.addEventListener('shown.bs.modal', function(e) {
        if (e.target.classList.contains('user-modal')) {
            // Load address data for modals
            loadProvinces();
        }
    });

    // Setup form validation
    document.addEventListener('submit', function(e) {
        if (e.target.classList.contains('user-form')) {
            // Basic form validation
            const requiredFields = e.target.querySelectorAll('[required]');
            let isValid = true;

            requiredFields.forEach(field => {
                if (!field.value.trim()) {
                    isValid = false;
                    field.classList.add('is-invalid');
                } else {
                    field.classList.remove('is-invalid');
                }
            });

            if (!isValid) {
                e.preventDefault();
                showNotification('Vui lòng điền đầy đủ thông tin bắt buộc!', 'warning');
            }
        }
    });

    // Initialize tabs
    initializeTabs();
});

// Export functions for global access
window.debounceSearch = debounceSearch;
window.searchUsers = searchUsers;
window.createUserByRole = createUserByRole;
window.toggleUserStatus = toggleUserStatus;
window.loadProvinces = loadProvinces;
window.loadDistricts = loadDistricts;
window.loadWards = loadWards;
window.createProfile = createProfile;
window.clearSearch = clearSearch;
