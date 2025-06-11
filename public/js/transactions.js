// Global transaction action functions (defined outside DOMContentLoaded for immediate availability)
function viewTransaction(transactionId) {
    console.log('Viewing transaction:', transactionId);
    // TODO: Implement modal or redirect to details page
    alert(`Xem chi tiết giao dịch ${transactionId}`);
}

function editTransaction(transactionId) {
    console.log('Editing transaction:', transactionId);
    // TODO: Implement edit modal or redirect to edit page
    alert(`Chỉnh sửa giao dịch ${transactionId}`);
}

function viewDocuments(transactionId) {
    console.log('Managing documents for transaction:', transactionId);
    // TODO: Implement document management modal
    alert(`Quản lý tài liệu cho giao dịch ${transactionId}`);
}

// Global export function (placeholder until DOMContentLoaded)
function exportToExcel() {
    if (exportToExcelFunction) {
        exportToExcelFunction();
    } else {
        console.warn('Export function not ready yet');
        alert('Vui lòng đợi trang tải xong trước khi xuất Excel');
    }
}

// Export to Excel function (will be called from DOMContentLoaded)
let exportToExcelFunction;

// Expose functions globally for onclick handlers
window.viewTransaction = viewTransaction;
window.editTransaction = editTransaction;
window.viewDocuments = viewDocuments;
window.exportToExcel = exportToExcel;

// Document ready functionality
document.addEventListener('DOMContentLoaded', function() {
    // Get filter elements
    const searchInput = document.getElementById('searchTransaction');
    const statusFilter = document.getElementById('statusFilter');
    const typeFilter = document.getElementById('typeFilter');
    const dateFromFilter = document.getElementById('dateFrom');
    const dateToFilter = document.getElementById('dateTo');
    const clearFiltersBtn = document.getElementById('clearFilters');
    const exportExcelBtn = document.getElementById('exportExcel');
    const tableRows = document.querySelectorAll('.transaction-table tbody tr:not(.empty-row)');
    const loadingOverlay = document.getElementById('loadingOverlay');
    const noResults = document.querySelector('.empty-row');
    const transactionsTable = document.getElementById('transactionTable');

    // Pagination elements
    const paginationContainer = document.getElementById('paginationContainer');
    const paginationInfo = document.getElementById('paginationInfo');
    const prevPageBtn = document.getElementById('prevPage');
    const nextPageBtn = document.getElementById('nextPage');
    const paginationNumbers = document.getElementById('paginationNumbers');

    // Pagination settings
    let currentPage = 1;
    const itemsPerPage = 10;
    let filteredRows = Array.from(tableRows);
    let sortColumn = null;
    let sortDirection = 'asc';

    // Initialize
    updateTable();
    initSortableHeaders();

    // Filter function
    function filterTransactions() {
        showLoading();

        setTimeout(() => {
            const searchTerm = searchInput.value.toLowerCase();
            const statusValue = statusFilter.value;
            const typeValue = typeFilter.value;
            const dateFrom = dateFromFilter.value;
            const dateTo = dateToFilter.value;

            filteredRows = [];

            tableRows.forEach(row => {
                const transactionId = row.querySelector('.transaction-id').textContent.toLowerCase();
                const propertyCode = row.querySelector('.property-code').textContent.toLowerCase();
                const statusBadge = row.querySelector('.status-badge');
                const typeBadge = row.querySelector('.type-badge');
                const transactionDate = row.querySelector('.transaction-date').textContent;

                // Search filter
                const matchesSearch = transactionId.includes(searchTerm) ||
                                    propertyCode.includes(searchTerm);

                // Status filter
                let matchesStatus = true;
                if (statusValue && statusBadge) {
                    const statusClass = statusBadge.classList;
                    matchesStatus = statusClass.contains('status-' + statusValue.toLowerCase());
                }

                // Type filter
                let matchesType = true;
                if (typeValue && typeBadge) {
                    const typeClass = typeBadge.classList;
                    matchesType = typeClass.contains('type-' + typeValue.toLowerCase());
                }

                // Date filter
                let matchesDate = true;
                if (dateFrom || dateTo) {
                    const transactionDateObj = parseDate(transactionDate);
                    if (dateFrom && transactionDateObj < new Date(dateFrom)) {
                        matchesDate = false;
                    }
                    if (dateTo && transactionDateObj > new Date(dateTo)) {
                        matchesDate = false;
                    }
                }

                if (matchesSearch && matchesStatus && matchesType && matchesDate) {
                    filteredRows.push(row);
                }
            });

            currentPage = 1;
            updateTable();
            hideLoading();
        }, 300);
    }

    // Parse date from Vietnamese format (dd/mm/yyyy)
    function parseDate(dateString) {
        const parts = dateString.split('/');
        if (parts.length === 3) {
            return new Date(parts[2], parts[1] - 1, parts[0]); // year, month-1, day
        }
        return new Date();
    }

    // Show loading state
    function showLoading() {
        if (loadingOverlay) {
            loadingOverlay.style.display = 'flex';
        }
    }

    // Hide loading state
    function hideLoading() {
        if (loadingOverlay) {
            loadingOverlay.style.display = 'none';
        }
    }

    // Update table display with pagination
    function updateTable() {
        const startIndex = (currentPage - 1) * itemsPerPage;
        const endIndex = startIndex + itemsPerPage;
        const totalItems = filteredRows.length;

        // Hide all rows first
        tableRows.forEach(row => {
            row.style.display = 'none';
        });

        // Show rows for current page
        const currentPageRows = filteredRows.slice(startIndex, endIndex);
        currentPageRows.forEach(row => {
            row.style.display = '';
        });

        // Update pagination info
        if (totalItems > 0) {
            const displayStart = startIndex + 1;
            const displayEnd = Math.min(endIndex, totalItems);
            paginationInfo.textContent = `Hiển thị ${displayStart}-${displayEnd} của ${totalItems} giao dịch`;

            if (noResults) noResults.style.display = 'none';
            if (transactionsTable) transactionsTable.style.display = '';
            if (paginationContainer) paginationContainer.style.display = 'flex';
        } else {
            if (noResults) noResults.style.display = 'block';
            if (transactionsTable) transactionsTable.style.display = 'none';
            if (paginationContainer) paginationContainer.style.display = 'none';
        }

        updatePaginationButtons();
    }

    // Update pagination buttons
    function updatePaginationButtons() {
        const totalPages = Math.ceil(filteredRows.length / itemsPerPage);

        // Update prev/next buttons
        prevPageBtn.disabled = currentPage === 1;
        nextPageBtn.disabled = currentPage === totalPages || totalPages === 0;

        // Update page numbers
        paginationNumbers.innerHTML = '';

        const startPage = Math.max(1, currentPage - 2);
        const endPage = Math.min(totalPages, startPage + 4);

        for (let i = startPage; i <= endPage; i++) {
            const pageBtn = document.createElement('button');
            pageBtn.className = 'pagination-number';
            pageBtn.textContent = i;

            if (i === currentPage) {
                pageBtn.classList.add('active');
            }

            pageBtn.addEventListener('click', () => {
                currentPage = i;
                updateTable();
            });

            paginationNumbers.appendChild(pageBtn);
        }
    }

    // Initialize sortable headers
    function initSortableHeaders() {
        const sortableHeaders = document.querySelectorAll('.sortable');

        sortableHeaders.forEach(header => {
            header.addEventListener('click', () => {
                const sortType = header.dataset.sort;

                if (sortColumn === sortType) {
                    sortDirection = sortDirection === 'asc' ? 'desc' : 'asc';
                } else {
                    sortColumn = sortType;
                    sortDirection = 'asc';
                }

                sortTransactions(sortType, sortDirection);
                updateSortIcons(header, sortDirection);
            });
        });
    }

    // Sort transactions
    function sortTransactions(column, direction) {
        filteredRows.sort((a, b) => {
            let aValue, bValue;

            switch (column) {
                case 'id':
                    aValue = a.querySelector('.transaction-id').textContent;
                    bValue = b.querySelector('.transaction-id').textContent;
                    break;
                case 'property':
                    aValue = a.querySelector('.property-code').textContent;
                    bValue = b.querySelector('.property-code').textContent;
                    break;
                case 'type':
                    aValue = a.querySelector('.type-badge').textContent;
                    bValue = b.querySelector('.type-badge').textContent;
                    break;
                case 'value':
                    aValue = parseFloat(a.querySelector('.transaction-value').textContent.replace(/[^\d]/g, ''));
                    bValue = parseFloat(b.querySelector('.transaction-value').textContent.replace(/[^\d]/g, ''));
                    break;
                case 'date':
                    aValue = parseDate(a.querySelector('.transaction-date').textContent);
                    bValue = parseDate(b.querySelector('.transaction-date').textContent);
                    break;
                case 'status':
                    aValue = a.querySelector('.status-badge').textContent;
                    bValue = b.querySelector('.status-badge').textContent;
                    break;
                default:
                    return 0;
            }

            if (direction === 'asc') {
                return aValue > bValue ? 1 : -1;
            } else {
                return aValue < bValue ? 1 : -1;
            }
        });

        updateTable();
    }

    // Update sort icons
    function updateSortIcons(activeHeader, direction) {
        // Reset all sort icons
        document.querySelectorAll('.sortable').forEach(header => {
            header.classList.remove('sort-asc', 'sort-desc');
        });

        // Set active sort icon
        activeHeader.classList.add(direction === 'asc' ? 'sort-asc' : 'sort-desc');
    }

    // Clear filters function
    function clearFilters() {
        searchInput.value = '';
        statusFilter.value = '';
        typeFilter.value = '';
        dateFromFilter.value = '';
        dateToFilter.value = '';

        filteredRows = Array.from(tableRows);
        currentPage = 1;
        sortColumn = null;
        sortDirection = 'asc';

        // Reset sort icons
        document.querySelectorAll('.sortable').forEach(header => {
            header.classList.remove('sort-asc', 'sort-desc');
        });

        updateTable();
    }

    // Export to Excel function
    function exportToExcel() {
        showLoading();

        setTimeout(() => {
            // Prepare data for export
            const exportData = [];
            const headers = ['ID Giao dịch', 'Mã BĐS', 'Loại', 'Giá trị', 'Ngày giao dịch', 'Trạng thái'];
            exportData.push(headers);

            filteredRows.forEach(row => {
                const rowData = [
                    row.querySelector('.transaction-id').textContent,
                    row.querySelector('.property-code').textContent,
                    row.querySelector('.type-badge').textContent,
                    row.querySelector('.transaction-value').textContent,
                    row.querySelector('.transaction-date').textContent,
                    row.querySelector('.status-badge').textContent
                ];
                exportData.push(rowData);
            });

            // Create CSV content
            const csvContent = exportData.map(row =>
                row.map(cell => `"${cell}"`).join(',')
            ).join('\n');

            // Create and download file
            const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
            const link = document.createElement('a');
            const url = URL.createObjectURL(blob);
            link.setAttribute('href', url);
            link.setAttribute('download', `giao-dich-${new Date().toISOString().split('T')[0]}.csv`);
            link.style.visibility = 'hidden';
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);

            hideLoading();
        }, 500);
    }

    // Expose exportToExcel globally
    window.exportToExcel = exportToExcel;

    // Event listeners
    searchInput.addEventListener('input', filterTransactions);
    statusFilter.addEventListener('change', filterTransactions);
    typeFilter.addEventListener('change', filterTransactions);
    dateFromFilter.addEventListener('change', filterTransactions);
    dateToFilter.addEventListener('change', filterTransactions);

    if (clearFiltersBtn) {
        clearFiltersBtn.addEventListener('click', clearFilters);
    }

    if (exportExcelBtn) {
        exportExcelBtn.addEventListener('click', exportToExcel);
    }

    // Pagination event listeners
    if (prevPageBtn) {
        prevPageBtn.addEventListener('click', () => {
            if (currentPage > 1) {
                currentPage--;
                updateTable();
            }
        });
    }

    if (nextPageBtn) {
        nextPageBtn.addEventListener('click', () => {
            const totalPages = Math.ceil(filteredRows.length / itemsPerPage);
            if (currentPage < totalPages) {
                currentPage++;
                updateTable();
            }
        });
    }

    // Action button handlers
    document.addEventListener('click', function(e) {
        if (e.target.closest('.btn-view')) {
            e.preventDefault();
            const transactionId = e.target.closest('tr').querySelector('.transaction-id').textContent;
            viewTransaction(transactionId);
        }

        if (e.target.closest('.btn-edit')) {
            e.preventDefault();
            const transactionId = e.target.closest('tr').querySelector('.transaction-id').textContent;
            editTransaction(transactionId);
        }

        if (e.target.closest('.btn-docs')) {
            e.preventDefault();
            const transactionId = e.target.closest('tr').querySelector('.transaction-id').textContent;
            viewDocuments(transactionId);
        }
    });

    // Make export function available globally
    exportToExcelFunction = exportToExcel;
    window.exportToExcel = exportToExcel;
});
