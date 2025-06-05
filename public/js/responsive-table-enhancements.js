/**
 * RESPONSIVE TABLE ENHANCEMENTS - JavaScript for table interactions
 * Tạo bởi: Assistant AI
 * Mục đích: Cải thiện tương tác và sorting cho bảng responsive
 */

document.addEventListener('DOMContentLoaded', function() {
    
    // ====================================================================
    // TABLE SORTING FUNCTIONALITY
    // ====================================================================
    
    function initTableSorting() {
        const sortableHeaders = document.querySelectorAll('.table-enhanced .sortable');
        
        sortableHeaders.forEach(header => {
            header.addEventListener('click', function() {
                const column = this.dataset.column;
                const table = this.closest('table');
                const tbody = table.querySelector('tbody');
                const rows = Array.from(tbody.querySelectorAll('tr'));
                const sortIcon = this.querySelector('.sort-icon');
                
                // Reset other sort icons
                table.querySelectorAll('.sort-icon').forEach(icon => {
                    if (icon !== sortIcon) {
                        icon.className = 'fas fa-sort ms-auto sort-icon';
                    }
                });
                
                // Determine sort direction
                let isAscending = sortIcon.classList.contains('fa-sort-up');
                let sortDirection = isAscending ? 'desc' : 'asc';
                
                // Update sort icon
                sortIcon.className = `fas fa-sort-${sortDirection === 'asc' ? 'up' : 'down'} ms-auto sort-icon`;
                
                // Sort rows
                rows.sort((a, b) => {
                    const aValue = getCellValue(a, column);
                    const bValue = getCellValue(b, column);
                    
                    if (sortDirection === 'asc') {
                        return aValue > bValue ? 1 : aValue < bValue ? -1 : 0;
                    } else {
                        return aValue < bValue ? 1 : aValue > bValue ? -1 : 0;
                    }
                });
                
                // Reappend sorted rows
                rows.forEach(row => tbody.appendChild(row));
                
                // Add visual feedback
                this.style.background = 'rgba(0, 123, 255, 0.1)';
                setTimeout(() => {
                    this.style.background = '';
                }, 300);
            });
        });
    }
    
    function getCellValue(row, column) {
        const cells = row.querySelectorAll('td');
        const headers = row.closest('table').querySelectorAll('th');
        let columnIndex = -1;
        
        // Find column index
        headers.forEach((header, index) => {
            if (header.dataset.column === column) {
                columnIndex = index;
            }
        });
        
        if (columnIndex === -1 || !cells[columnIndex]) return '';
        
        const cellText = cells[columnIndex].textContent.trim();
        
        // Try to parse as number
        const numberValue = parseFloat(cellText.replace(/[^\d.-]/g, ''));
        if (!isNaN(numberValue)) {
            return numberValue;
        }
        
        // Try to parse as date
        const dateValue = Date.parse(cellText);
        if (!isNaN(dateValue)) {
            return dateValue;
        }
        
        return cellText.toLowerCase();
    }
    
    // ====================================================================
    // MOBILE TABLE CARD CONVERSION
    // ====================================================================
    
    function initMobileTableCards() {
        function convertToCards() {
            const tables = document.querySelectorAll('.table-enhanced');
            
            tables.forEach(table => {
                const wrapper = table.closest('.table-responsive-enhanced');
                if (!wrapper) return;
                
                if (window.innerWidth <= 576) {
                    if (!wrapper.classList.contains('mobile-cards')) {
                        convertTableToCards(table, wrapper);
                    }
                } else {
                    if (wrapper.classList.contains('mobile-cards')) {
                        convertCardsToTable(table, wrapper);
                    }
                }
            });
        }
        
        function convertTableToCards(table, wrapper) {
            const headers = Array.from(table.querySelectorAll('thead th')).map(th => 
                th.textContent.trim()
            );
            const rows = table.querySelectorAll('tbody tr');
            
            let cardsHTML = '<div class="mobile-cards-container">';
            
            rows.forEach(row => {
                const cells = row.querySelectorAll('td');
                cardsHTML += '<div class="mobile-card">';
                
                cells.forEach((cell, index) => {
                    if (headers[index] && cell.textContent.trim()) {
                        cardsHTML += `
                            <div class="mobile-card-row">
                                <div class="mobile-card-label">${headers[index]}</div>
                                <div class="mobile-card-value">${cell.innerHTML}</div>
                            </div>
                        `;
                    }
                });
                
                cardsHTML += '</div>';
            });
            
            cardsHTML += '</div>';
            
            table.style.display = 'none';
            wrapper.insertAdjacentHTML('beforeend', cardsHTML);
            wrapper.classList.add('mobile-cards');
        }
        
        function convertCardsToTable(table, wrapper) {
            const cardsContainer = wrapper.querySelector('.mobile-cards-container');
            if (cardsContainer) {
                cardsContainer.remove();
            }
            table.style.display = '';
            wrapper.classList.remove('mobile-cards');
        }
        
        // Initial conversion
        convertToCards();
        
        // Listen for resize events
        let resizeTimeout;
        window.addEventListener('resize', function() {
            clearTimeout(resizeTimeout);
            resizeTimeout = setTimeout(convertToCards, 250);
        });
    }
    
    // ====================================================================
    // SCROLL INDICATORS
    // ====================================================================
    
    function initScrollIndicators() {
        const tableWrappers = document.querySelectorAll('.table-responsive-enhanced');
        
        tableWrappers.forEach(wrapper => {
            const table = wrapper.querySelector('table');
            if (!table) return;
            
            // Add scroll indicators
            const scrollIndicator = document.createElement('div');
            scrollIndicator.className = 'scroll-indicator';
            scrollIndicator.innerHTML = `
                <div class="scroll-hint">
                    <i class="fas fa-arrows-alt-h"></i>
                    <span>Vuốt để xem thêm</span>
                </div>
            `;
            wrapper.appendChild(scrollIndicator);
            
            // Show/hide scroll indicator
            function updateScrollIndicator() {
                const canScroll = wrapper.scrollWidth > wrapper.clientWidth;
                const isScrolled = wrapper.scrollLeft > 0;
                
                if (canScroll && !isScrolled) {
                    scrollIndicator.classList.add('visible');
                } else {
                    scrollIndicator.classList.remove('visible');
                }
            }
            
            // Check on load and scroll
            updateScrollIndicator();
            wrapper.addEventListener('scroll', updateScrollIndicator);
            window.addEventListener('resize', updateScrollIndicator);
            
            // Hide indicator after first scroll
            wrapper.addEventListener('scroll', function() {
                scrollIndicator.classList.add('scrolled');
            }, { once: true });
        });
    }
    
    // ====================================================================
    // ENHANCED ACTION BUTTONS
    // ====================================================================
    
    function initActionButtons() {
        const actionGroups = document.querySelectorAll('.action-buttons-mobile');
        
        actionGroups.forEach(group => {
            // Add dropdown functionality for mobile
            if (window.innerWidth <= 768) {
                const buttons = group.querySelectorAll('.btn');
                if (buttons.length > 2) {
                    // Convert to dropdown on mobile
                    group.classList.add('btn-group-vertical');
                }
            }
        });
    }
    
    // ====================================================================
    // LOADING STATES
    // ====================================================================
    
    function showTableLoading(tableWrapper) {
        const loadingHTML = `
            <div class="table-loading">
                <div class="loading-spinner">
                    <i class="fas fa-spinner fa-spin"></i>
                </div>
                <div class="loading-text">Đang tải dữ liệu...</div>
            </div>
        `;
        tableWrapper.insertAdjacentHTML('beforeend', loadingHTML);
    }
    
    function hideTableLoading(tableWrapper) {
        const loading = tableWrapper.querySelector('.table-loading');
        if (loading) {
            loading.remove();
        }
    }
    
    // ====================================================================
    // ACCESSIBILITY ENHANCEMENTS
    // ====================================================================
    
    function initAccessibility() {
        // Add ARIA labels to sortable headers
        const sortableHeaders = document.querySelectorAll('.sortable');
        sortableHeaders.forEach(header => {
            header.setAttribute('role', 'button');
            header.setAttribute('tabindex', '0');
            header.setAttribute('aria-label', `Sắp xếp theo ${header.textContent.trim()}`);
            
            // Keyboard support
            header.addEventListener('keydown', function(e) {
                if (e.key === 'Enter' || e.key === ' ') {
                    e.preventDefault();
                    this.click();
                }
            });
        });
        
        // Add table navigation hints
        const tables = document.querySelectorAll('.table-enhanced');
        tables.forEach(table => {
            table.setAttribute('role', 'table');
            table.setAttribute('aria-label', 'Bảng dữ liệu có thể cuộn và sắp xếp');
        });
    }
    
    // ====================================================================
    // PERFORMANCE OPTIMIZATION
    // ====================================================================
    
    function debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }
    
    // ====================================================================
    // INITIALIZATION
    // ====================================================================
    
    // Initialize all enhancements
    initTableSorting();
    initMobileTableCards();
    initScrollIndicators();
    initActionButtons();
    initAccessibility();
    
    // Add debugging info in development
    if (window.location.hostname === 'localhost' || window.location.hostname === '127.0.0.1') {
        console.log('🚀 Responsive Table Enhancements loaded successfully!');
        console.log('📱 Mobile breakpoints: 576px, 768px, 1024px');
        console.log('🔧 Features: Sorting, Mobile cards, Scroll indicators, Accessibility');
    }
    
    // Global functions for external use
    window.ResponsiveTableEnhancements = {
        showLoading: showTableLoading,
        hideLoading: hideTableLoading,
        refresh: function() {
            initTableSorting();
            initMobileTableCards();
            initScrollIndicators();
            initActionButtons();
        }
    };
});
