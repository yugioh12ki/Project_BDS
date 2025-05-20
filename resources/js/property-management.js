/**
 * Property Management JavaScript
 * Handles property list interactions and status updates
 */

// Highlight selected property row
function highlightPropertyRow(propertyId) {
    // Remove highlighting from all rows
    document.querySelectorAll('.property-row').forEach(row => {
        row.classList.remove('highlighted-row');
    });

    // Highlight the selected row
    const selectedRow = document.getElementById(`property-row-${propertyId}`);
    if (selectedRow) {
        selectedRow.classList.add('highlighted-row');
        selectedRow.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }
}

// Handle property status updates (approval/rejection)
function updatePropertyStatus(propertyId, status, reason = null) {
    console.log(`Updating property ${propertyId} status to ${status}`);
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    const formData = new FormData();
    formData.append('property_id', propertyId);
    formData.append('status', status); // Will be handled by SystemController
    if (reason) {
        formData.append('reason', reason);
    }
    formData.append('_token', csrfToken);

    // Display loading state
    const row = document.getElementById(`property-row-${propertyId}`);
    if (row) {
        row.classList.add('updating');
    }

    // Send AJAX request
    fetch('/admin/property/update-status', {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Remove the row with a fade-out animation
            if (row) {
                row.style.opacity = '0';
                row.style.transform = 'translateX(100px)';
                setTimeout(() => {
                    row.remove();

                    // Update property count
                    updatePropertyCount();
                }, 500);
            }

            // Update the pending count in the navigation menu
            updatePendingCountInMenu();

            // Show success toast notification
            showNotification('success', data.message || 'Cập nhật trạng thái thành công!');
        } else {
            showNotification('error', data.message || 'Đã xảy ra lỗi khi cập nhật trạng thái.');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('error', 'Đã xảy ra lỗi khi cập nhật trạng thái.');
    });
}

// Handle batch property status updates
function updateBatchPropertyStatus(propertyIds, status, reason = null) {
    if (!Array.isArray(propertyIds) || propertyIds.length === 0) {
        return;
    }

    console.log(`Batch updating ${propertyIds.length} properties status to ${status}`);
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    const formData = new FormData();
    formData.append('property_ids', JSON.stringify(propertyIds));
    formData.append('status', status); // Will be handled by SystemController
    if (reason) {
        formData.append('reason', reason);
    }
    formData.append('_token', csrfToken);

    // Display loading state for all affected rows
    propertyIds.forEach(id => {
        const row = document.getElementById(`property-row-${id}`);
        if (row) {
            row.classList.add('updating');
        }
    });

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
            // Remove all affected rows with fade-out animation
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

            // Update property count after a delay to let the animation complete
            setTimeout(() => {
                updatePropertyCount();
            }, 600);

            // Update the pending count in the navigation menu
            updatePendingCountInMenu();

            // Show success toast notification
            showNotification('success', data.message || `Đã cập nhật trạng thái cho ${propertyIds.length} bất động sản`);
        } else {
            showNotification('error', data.message || 'Đã xảy ra lỗi khi cập nhật trạng thái');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('error', 'Đã xảy ra lỗi khi cập nhật trạng thái');
    });
}

// Update pending count in the navigation menu
function updatePendingCountInMenu() {
    const pendingCountBadges = document.querySelectorAll('.badge');
    pendingCountBadges.forEach(badge => {
        const count = parseInt(badge.textContent);
        if (!isNaN(count) && count > 0) {
            badge.textContent = count - 1;
            if (count - 1 <= 0) {
                badge.style.display = 'none';
            }
        }
    });
}

// Show notification toast
function showNotification(type, message) {
    // Create notification element
    const notification = document.createElement('div');
    notification.className = `alert alert-${type === 'success' ? 'success' : 'danger'} alert-dismissible fade show notification-toast`;
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

// Update the property count display
function updatePropertyCount() {
    // Count visible property rows
    const visibleRows = document.querySelectorAll('.property-row:not([style*="display: none"])').length;

    // Update the count display
    const countElement = document.getElementById('propertyCount');
    if (countElement) {
        countElement.textContent = visibleRows;
    }

    // Toggle no results message
    const noResultsMessage = document.getElementById('noPropertiesMessage');
    if (noResultsMessage) {
        noResultsMessage.style.display = visibleRows === 0 ? 'block' : 'none';
    }
}

// Toggle between list and map views (for mobile)
function toggleViewMode(mode) {
    const listColumn = document.querySelector('.property-list-column');
    const mapColumn = document.querySelector('.map-column');

    if (mode === 'list') {
        listColumn.classList.remove('d-none');
        mapColumn.classList.add('d-none', 'd-lg-block');
        document.getElementById('listViewBtn').classList.add('active');
        document.getElementById('mapViewBtn').classList.remove('active');
    } else if (mode === 'map') {
        listColumn.classList.add('d-none', 'd-lg-block');
        mapColumn.classList.remove('d-none');
        document.getElementById('mapViewBtn').classList.add('active');
        document.getElementById('listViewBtn').classList.remove('active');
    }
}

// Fit all markers on the map
function fitAllMarkersToMap(map, markers) {
    if (!map || !markers || markers.length === 0) return;

    const bounds = new google.maps.LatLngBounds();

    markers.forEach(marker => {
        bounds.extend(marker.getPosition());
    });

    map.fitBounds(bounds);

    // Don't zoom in too far on only one marker
    if (markers.length === 1) {
        map.setZoom(15);
    }
}

/**
 * Initialize keyboard shortcuts for property management
 */
function initializeKeyboardShortcuts() {
    // Handle keyboard shortcuts
    document.addEventListener('keydown', function(event) {
        // Only process shortcuts when on the property page
        if (!document.querySelector('.property-page-wrapper')) {
            return;
        }

        // Get the highlighted row
        const highlightedRow = document.querySelector('.property-row.highlighted-row');
        if (!highlightedRow) return;

        const propertyId = highlightedRow.id.replace('property-row-', '');

        // A key - Approve property
        if (event.key === 'a' && !event.ctrlKey && !event.metaKey && !isInputFocused()) {
            if (confirm('Bạn có chắc chắn muốn duyệt bất động sản này không?')) {
                updatePropertyStatus(propertyId, 'approved');
            }
        }



        // V key - View property details
        if (event.key === 'v' && !event.ctrlKey && !event.metaKey && !isInputFocused()) {
            // Find and click the view button for this property
            const viewButton = highlightedRow.querySelector('.btn-info');
            if (viewButton) {
                viewButton.click();
            }
        }

        // Arrow down - Navigate to next property
        if (event.key === 'ArrowDown' && !isInputFocused()) {
            const nextRow = highlightedRow.nextElementSibling;
            if (nextRow && nextRow.classList.contains('property-row')) {
                // Click the row to highlight it
                const propertyId = nextRow.id.replace('property-row-', '');
                const lat = nextRow.getAttribute('data-lat');
                const lng = nextRow.getAttribute('data-lng');

                // Call the selectPropertyOnMap function
                if (typeof window.selectPropertyOnMap === 'function') {
                    window.selectPropertyOnMap(propertyId, lat, lng);
                } else {
                    highlightPropertyRow(propertyId);
                }
            }
        }

        // Arrow up - Navigate to previous property
        if (event.key === 'ArrowUp' && !isInputFocused()) {
            const prevRow = highlightedRow.previousElementSibling;
            if (prevRow && prevRow.classList.contains('property-row')) {
                // Click the row to highlight it
                const propertyId = prevRow.id.replace('property-row-', '');
                const lat = prevRow.getAttribute('data-lat');
                const lng = prevRow.getAttribute('data-lng');

                // Call the selectPropertyOnMap function
                if (typeof window.selectPropertyOnMap === 'function') {
                    window.selectPropertyOnMap(propertyId, lat, lng);
                } else {
                    highlightPropertyRow(propertyId);
                }
            }
        }

        // Space - Toggle checkbox for batch operations
        if (event.key === ' ' && !isInputFocused()) {
            const checkbox = highlightedRow.querySelector('.property-checkbox');
            if (checkbox) {
                checkbox.checked = !checkbox.checked;

                // Highlight row if checked
                if (checkbox.checked) {
                    highlightedRow.classList.add('selected-for-batch');
                } else {
                    highlightedRow.classList.remove('selected-for-batch');
                }

                // Trigger change event to update counters
                const changeEvent = new Event('change');
                checkbox.dispatchEvent(changeEvent);
            }

            // Prevent page scrolling
            event.preventDefault();
        }
    });

    // Display shortcut hints in the UI
    addShortcutHints();
}

/**
 * Check if an input element is currently focused
 * This prevents keyboard shortcuts from firing when typing in input fields
 */
function isInputFocused() {
    const activeElement = document.activeElement;
    const isInput = activeElement.tagName === 'INPUT' ||
                   activeElement.tagName === 'TEXTAREA' ||
                   activeElement.tagName === 'SELECT' ||
                   activeElement.contentEditable === 'true';
    return isInput;
}

/**
 * Add shortcut hint elements to the UI
 */
function addShortcutHints() {
    // Add hints to buttons
    const approveButtons = document.querySelectorAll('.approve-btn');
    approveButtons.forEach(btn => {
        if (!btn.querySelector('.shortcut-hint')) {
            const hint = document.createElement('span');
            hint.className = 'shortcut-hint';
            hint.textContent = 'A';
            btn.appendChild(hint);
        }
    });

    const rejectButtons = document.querySelectorAll('.reject-btn');
    rejectButtons.forEach(btn => {
        if (!btn.querySelector('.shortcut-hint')) {
            const hint = document.createElement('span');
            hint.className = 'shortcut-hint';
            hint.textContent = 'R';
            btn.appendChild(hint);
        }
    });

    const viewButtons = document.querySelectorAll('.btn-info');
    viewButtons.forEach(btn => {
        if (!btn.querySelector('.shortcut-hint')) {
            const hint = document.createElement('span');
            hint.className = 'shortcut-hint';
            hint.textContent = 'V';
            btn.appendChild(hint);
        }
    });
}

// Export functions for use in other files
export {
    highlightPropertyRow,
    updatePropertyStatus,
    updateBatchPropertyStatus,
    updatePendingCountInMenu,
    updatePropertyCount,
    showNotification,
    toggleViewMode,
    fitAllMarkersToMap,
    initializeKeyboardShortcuts,
    exportPropertiesToCsv
};

/**
 * Export properties to CSV file
 * @param {string} filename - Name to save the CSV file as
 */
function exportPropertiesToCsv(filename = 'property-list.csv') {
    // Get all visible property rows
    const propertyRows = document.querySelectorAll('.property-row:not([style*="display: none"])');
    if (propertyRows.length === 0) {
        showNotification('error', 'Không có dữ liệu bất động sản để xuất');
        return;
    }

    // Determine which columns to include based on viewport
    const isMobile = window.innerWidth < 768;

    // Define CSV columns and headers
    const csvColumns = [
        { header: 'ID', selector: row => row.querySelector('td:nth-child(' + (row.querySelector('.checkbox-column') ? '2' : '1') + ')').textContent.trim() },
        { header: 'Tiêu đề', selector: row => row.querySelector('td:nth-child(' + (row.querySelector('.checkbox-column') ? '3' : '2') + ')').textContent.trim() },
    ];

    // Add columns that might not be visible on mobile
    if (!isMobile) {
        csvColumns.push(
            { header: 'Loại hình', selector: row => row.querySelector('td.d-none.d-md-table-cell:nth-of-type(1)')?.textContent.trim() || 'N/A' },
            { header: 'Địa chỉ', selector: row => row.querySelector('td.d-none.d-md-table-cell:nth-of-type(2)')?.textContent.trim() || 'N/A' },
            { header: 'Ngày đăng', selector: row => row.querySelector('td.d-none.d-lg-table-cell:nth-of-type(1)')?.textContent.trim() || 'N/A' },
            { header: 'Chủ sở hữu', selector: row => row.querySelector('td.d-none.d-lg-table-cell:nth-of-type(2)')?.textContent.trim() || 'N/A' }
        );
    }

    // Create CSV header row
    let csvContent = csvColumns.map(col => '"' + col.header + '"').join(',') + '\r\n';

    // Add data rows
    propertyRows.forEach(row => {
        const rowData = csvColumns.map(col => {
            // Get data using the selector function
            let value = col.selector(row);
            // Escape quotes and wrap in quotes
            value = value.replace(/"/g, '""');
            return '"' + value + '"';
        });

        csvContent += rowData.join(',') + '\r\n';
    });

    // Create a Blob containing the CSV data
    const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });

    // Create download link
    const link = document.createElement('a');
    const url = URL.createObjectURL(blob);
    link.setAttribute('href', url);
    link.setAttribute('download', filename);
    link.style.visibility = 'hidden';
    document.body.appendChild(link);

    // Trigger download
    link.click();

    // Clean up
    document.body.removeChild(link);
    URL.revokeObjectURL(url);

    // Show success message
    showNotification('success', `Đã xuất ${propertyRows.length} bất động sản thành tệp CSV`);
}
