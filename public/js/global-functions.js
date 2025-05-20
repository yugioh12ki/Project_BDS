/**
 * Global functions for property approval and rejection
 * These functions are needed by inline onclick handlers
 */

// Global function to approve a property
function approveProperty(propertyId) {
    if (confirm('Bạn có chắc chắn muốn duyệt bất động sản này không?')) {
        PropertyManagement.updatePropertyStatus(propertyId, 'approved');
    }
}

// Global function to reject a property
function rejectProperty(propertyId) {
    const reason = prompt('Vui lòng nhập lý do từ chối:');
    if (reason) {
        PropertyManagement.updatePropertyStatus(propertyId, 'rejected', reason);
    }
}
