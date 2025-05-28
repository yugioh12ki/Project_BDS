/**
 * Owner Autocomplete Component
 * AJAX autocomplete search for owners by name with dropdown suggestions
 */

class OwnerAutocomplete {
    constructor(options = {}) {
        this.options = {
            inputSelector: '#owner-search',
            dropdownSelector: '#owner-dropdown',
            searchUrl: '/agent/search/owners',
            minLength: 2,
            debounceDelay: 300,
            placeholder: 'Tìm kiếm chủ sở hữu...',
            ...options
        };
        
        this.input = null;
        this.dropdown = null;
        this.debounceTimeout = null;
        this.selectedOwner = null;
        this.isLoading = false;
        
        this.init();
    }
    
    init() {
        this.createElements();
        this.bindEvents();
    }
    
    createElements() {
        // Tìm hoặc tạo input element
        this.input = $(this.options.inputSelector);
        if (this.input.length === 0) {
            console.error(`Owner Autocomplete: Input element ${this.options.inputSelector} not found`);
            return;
        }
        
        // Thiết lập input attributes
        this.input.attr({
            'autocomplete': 'off',
            'placeholder': this.options.placeholder
        });
        
        // Tạo dropdown nếu chưa có
        this.dropdown = $(this.options.dropdownSelector);
        if (this.dropdown.length === 0) {
            this.dropdown = $(`<div id="${this.options.dropdownSelector.replace('#', '')}" class="owner-autocomplete-dropdown dropdown-menu"></div>`);
            this.input.after(this.dropdown);
        }
        
        // Thiết lập positioning
        this.dropdown.css({
            'position': 'absolute',
            'z-index': 1050,
            'max-height': '300px',
            'overflow-y': 'auto',
            'width': '100%',
            'box-shadow': '0 0.5rem 1rem rgba(0, 0, 0, 0.15)',
            'border': '1px solid #dee2e6'
        });
    }
    
    bindEvents() {
        // Input events
        this.input.on('input', (e) => this.handleInput(e));
        this.input.on('focus', (e) => this.handleFocus(e));
        this.input.on('blur', (e) => this.handleBlur(e));
        this.input.on('keydown', (e) => this.handleKeydown(e));
        
        // Dropdown events
        this.dropdown.on('click', '.owner-option', (e) => this.handleSelect(e));
        
        // Document events
        $(document).on('click', (e) => this.handleOutsideClick(e));
        
        // Resize events
        $(window).on('resize', () => this.updateDropdownPosition());
    }
    
    handleInput(e) {
        const value = e.target.value.trim();
        
        // Clear previous timeout
        if (this.debounceTimeout) {
            clearTimeout(this.debounceTimeout);
        }
        
        // Reset selected owner if input changes
        if (this.selectedOwner && this.selectedOwner.name !== value) {
            this.selectedOwner = null;
            this.triggerChange(null);
        }
        
        if (value.length < this.options.minLength) {
            this.hideDropdown();
            return;
        }
        
        // Debounce search
        this.debounceTimeout = setTimeout(() => {
            this.search(value);
        }, this.options.debounceDelay);
    }
    
    handleFocus(e) {
        const value = e.target.value.trim();
        if (value.length >= this.options.minLength) {
            this.search(value);
        }
    }
    
    handleBlur(e) {
        // Delay hiding to allow for clicks on dropdown
        setTimeout(() => {
            if (!this.dropdown.is(':hover')) {
                this.hideDropdown();
            }
        }, 150);
    }
    
    handleKeydown(e) {
        const items = this.dropdown.find('.owner-option');
        const activeItem = items.filter('.active');
        
        switch(e.key) {
            case 'ArrowDown':
                e.preventDefault();
                if (activeItem.length === 0) {
                    items.first().addClass('active');
                } else {
                    const next = activeItem.removeClass('active').next('.owner-option');
                    if (next.length > 0) {
                        next.addClass('active');
                    } else {
                        items.first().addClass('active');
                    }
                }
                this.scrollToActiveItem();
                break;
                
            case 'ArrowUp':
                e.preventDefault();
                if (activeItem.length === 0) {
                    items.last().addClass('active');
                } else {
                    const prev = activeItem.removeClass('active').prev('.owner-option');
                    if (prev.length > 0) {
                        prev.addClass('active');
                    } else {
                        items.last().addClass('active');
                    }
                }
                this.scrollToActiveItem();
                break;
                
            case 'Enter':
                e.preventDefault();
                if (activeItem.length > 0) {
                    this.selectOwner(this.getOwnerFromElement(activeItem));
                }
                break;
                
            case 'Escape':
                this.hideDropdown();
                break;
        }
    }
    
    handleSelect(e) {
        e.preventDefault();
        const ownerData = this.getOwnerFromElement($(e.currentTarget));
        this.selectOwner(ownerData);
    }
    
    handleOutsideClick(e) {
        if (!$(e.target).closest(this.input).length && 
            !$(e.target).closest(this.dropdown).length) {
            this.hideDropdown();
        }
    }
    
    search(term) {
        if (this.isLoading) return;
        
        this.isLoading = true;
        this.showLoading();
        
        $.ajax({
            url: this.options.searchUrl,
            method: 'GET',
            data: { term: term },
            success: (response) => {
                this.displayResults(response.owners || []);
            },
            error: (xhr, status, error) => {
                this.showError('Có lỗi xảy ra khi tìm kiếm. Vui lòng thử lại.');
                console.error('Owner search error:', error);
            },
            complete: () => {
                this.isLoading = false;
            }
        });
    }
    
    displayResults(owners) {
        if (owners.length === 0) {
            this.showNoResults();
            return;
        }
        
        const html = owners.map(owner => this.createOwnerItem(owner)).join('');
        this.dropdown.html(html);
        this.showDropdown();
        this.highlightSearchTerm();
    }
    
    createOwnerItem(owner) {
        const name = owner.name || owner.Name || '';
        const email = owner.email || owner.Email || '';
        const phone = owner.phone || owner.Phone || '';
        const id = owner.id || owner.UserID || '';
        
        return `
            <div class="owner-option dropdown-item" 
                 data-owner-id="${id}"
                 data-owner-name="${name}"
                 data-owner-email="${email}"
                 data-owner-phone="${phone}">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="owner-info">
                        <div class="owner-name fw-semibold">${name}</div>
                        <div class="owner-details text-muted small">
                            ${email ? `<i class="bi bi-envelope me-1"></i>${email}` : ''}
                            ${phone ? `<i class="bi bi-telephone ms-2 me-1"></i>${phone}` : ''}
                        </div>
                    </div>
                    <i class="bi bi-person-check text-primary"></i>
                </div>
            </div>
        `;
    }
    
    showLoading() {
        this.dropdown.html(`
            <div class="dropdown-item text-center py-3">
                <div class="spinner-border spinner-border-sm text-primary me-2" role="status">
                    <span class="visually-hidden">Đang tìm kiếm...</span>
                </div>
                Đang tìm kiếm chủ sở hữu...
            </div>
        `);
        this.showDropdown();
    }
    
    showNoResults() {
        this.dropdown.html(`
            <div class="dropdown-item text-center py-3 text-muted">
                <i class="bi bi-person-x me-2"></i>
                Không tìm thấy chủ sở hữu phù hợp
            </div>
        `);
        this.showDropdown();
    }
    
    showError(message) {
        this.dropdown.html(`
            <div class="dropdown-item text-center py-3 text-danger">
                <i class="bi bi-exclamation-triangle me-2"></i>
                ${message}
            </div>
        `);
        this.showDropdown();
    }
    
    showDropdown() {
        this.updateDropdownPosition();
        this.dropdown.addClass('show').show();
    }
    
    hideDropdown() {
        this.dropdown.removeClass('show').hide();
        this.dropdown.find('.owner-option').removeClass('active');
    }
    
    updateDropdownPosition() {
        const inputOffset = this.input.offset();
        const inputHeight = this.input.outerHeight();
        const inputWidth = this.input.outerWidth();
        
        this.dropdown.css({
            'top': inputOffset.top + inputHeight,
            'left': inputOffset.left,
            'width': inputWidth
        });
    }
    
    scrollToActiveItem() {
        const activeItem = this.dropdown.find('.owner-option.active');
        if (activeItem.length > 0) {
            const dropdown = this.dropdown[0];
            const item = activeItem[0];
            const dropdownHeight = dropdown.clientHeight;
            const itemTop = item.offsetTop;
            const itemHeight = item.offsetHeight;
            
            if (itemTop < dropdown.scrollTop) {
                dropdown.scrollTop = itemTop;
            } else if (itemTop + itemHeight > dropdown.scrollTop + dropdownHeight) {
                dropdown.scrollTop = itemTop + itemHeight - dropdownHeight;
            }
        }
    }
    
    highlightSearchTerm() {
        const searchTerm = this.input.val().trim().toLowerCase();
        if (!searchTerm) return;
        
        this.dropdown.find('.owner-name').each(function() {
            const $this = $(this);
            const text = $this.text();
            const regex = new RegExp(`(${searchTerm})`, 'gi');
            const highlightedText = text.replace(regex, '<mark>$1</mark>');
            $this.html(highlightedText);
        });
    }
    
    getOwnerFromElement($element) {
        return {
            id: $element.data('owner-id'),
            name: $element.data('owner-name'),
            email: $element.data('owner-email'),
            phone: $element.data('owner-phone')
        };
    }
    
    selectOwner(owner) {
        this.selectedOwner = owner;
        this.input.val(owner.name);
        this.hideDropdown();
        this.triggerChange(owner);
    }
    
    triggerChange(owner) {
        // Trigger custom event
        this.input.trigger('owner:selected', [owner]);
        
        // Call callback if provided
        if (typeof this.options.onSelect === 'function') {
            this.options.onSelect(owner);
        }
    }
    
    // Public methods
    getSelectedOwner() {
        return this.selectedOwner;
    }
    
    clearSelection() {
        this.selectedOwner = null;
        this.input.val('');
        this.hideDropdown();
        this.triggerChange(null);
    }
    
    setOwner(owner) {
        this.selectedOwner = owner;
        this.input.val(owner.name);
        this.triggerChange(owner);
    }
    
    disable() {
        this.input.prop('disabled', true);
        this.hideDropdown();
    }
    
    enable() {
        this.input.prop('disabled', false);
    }
    
    destroy() {
        // Remove events
        this.input.off();
        this.dropdown.off();
        $(document).off('click', this.handleOutsideClick);
        $(window).off('resize', this.updateDropdownPosition);
        
        // Clear timeouts
        if (this.debounceTimeout) {
            clearTimeout(this.debounceTimeout);
        }
        
        // Remove dropdown if it was created by this component
        if (this.dropdown.attr('id') === this.options.dropdownSelector.replace('#', '')) {
            this.dropdown.remove();
        }
    }
}

// Export for use in other files
window.OwnerAutocomplete = OwnerAutocomplete;

// jQuery plugin wrapper
$.fn.ownerAutocomplete = function(options) {
    return this.each(function() {
        const $this = $(this);
        if (!$this.data('ownerAutocomplete')) {
            const autocomplete = new OwnerAutocomplete({
                inputSelector: '#' + $this.attr('id'),
                dropdownSelector: '#' + $this.attr('id') + '-dropdown',
                ...options
            });
            $this.data('ownerAutocomplete', autocomplete);
        }
    });
};
