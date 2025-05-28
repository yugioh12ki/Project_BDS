/**
 * Appointment filters and sorting functionality
 */
$(document).ready(function() {
    const $appointmentCards = $('.appointment-card');
    const $appointmentFilter = $('#appointmentFilter');
    const $propertyOwnerSearch = $('#propertyOwnerSearch');
    const $resetSearch = $('#resetSearch');    // Filter appointments based on dropdown selection with improved formatting
    $appointmentFilter.on('change', function() {
        const filterValue = $(this).val();
        
        // Reset search
        $propertyOwnerSearch.val('');
        $('.empty-filtered').hide();
        $('.property-group-header, .owner-group-header').remove(); // Remove any existing group headers
        
        switch(filterValue) {
            case 'newest':
                // Sort by date (newest first)
                sortAppointmentsByDate(true);
                break;
            case 'oldest':
                // Sort by date (oldest first)
                sortAppointmentsByDate(false);
                break;
            case 'property':
                // Group by property (visual grouping by property)
                groupAppointmentsByProperty();
                break;
            case 'owner':
                // Group by owner (visual grouping by owner)
                groupAppointmentsByOwner();
                break;
            default:
                // Show all appointments without sorting
                $('.appointment-card').fadeIn(300);
                break;
        }
    });
    
    // Function to sort appointments by date
    function sortAppointmentsByDate(newestFirst) {
        const $container = $('.appointment-card').parent();
        const $appointmentCards = $('.appointment-card').toArray();
        
        // Hide all cards first for smooth transition
        $('.appointment-card').hide();
        
        $appointmentCards.sort(function(a, b) {
            const dateA = new Date($(a).data('date'));
            const dateB = new Date($(b).data('date'));
            
            return newestFirst ? dateB - dateA : dateA - dateB;
        });
        
        // Re-append the sorted cards with fade effect
        $appointmentCards.forEach(function(card, index) {
            setTimeout(() => {
                $container.append(card);
                $(card).fadeIn(200);
            }, index * 50); // Staggered animation
        });
    }
    
    // Function to group appointments visually by property
    function groupAppointmentsByProperty() {
        // Create an object to group appointments by property
        const groupedByProperty = {};
        
        // First pass - collect all property IDs and their appointments
        $('.appointment-card').each(function() {
            const propertyId = $(this).data('property-id');
            if (!propertyId) return;
            
            if (!groupedByProperty[propertyId]) {
                groupedByProperty[propertyId] = [];
            }
            groupedByProperty[propertyId].push($(this));
        });
        
        // Second pass - reorder appointments by property groups
        const $container = $('.appointment-card').first().parent();
        $('.appointment-card').hide();
        
        // For each property, show its appointments
        Object.keys(groupedByProperty).forEach((propertyId, groupIndex) => {
            const cards = groupedByProperty[propertyId];
            const propertyName = cards[0].find('.property-name').text().trim();
            
            // Add a header for this property group if it has appointments
            if (cards.length > 0) {
                const $header = $(`
                    <div class="property-group-header mb-2 mt-4" data-property-id="${propertyId}">
                        <h5 class="mb-1">
                            <i class="bi bi-buildings me-2"></i>
                            ${propertyName}
                        </h5>
                        <div class="small text-muted">${cards.length} lịch hẹn</div>
                    </div>
                `);
                
                setTimeout(() => {
                    $container.append($header);
                    $header.hide().fadeIn(200);
                    
                    // Append all appointments for this property
                    cards.forEach((card, cardIndex) => {
                        setTimeout(() => {
                            $container.append(card);
                            card.fadeIn(200);
                        }, cardIndex * 100);
                    });
                }, groupIndex * 300);
            }
        });
    }
    
    // Function to group appointments visually by owner
    function groupAppointmentsByOwner() {
        // Create an object to group appointments by owner
        const groupedByOwner = {};
        
        // First pass - collect all owner IDs and their appointments
        $('.appointment-card').each(function() {
            const ownerId = $(this).data('owner-id');
            if (!ownerId) return;
            
            if (!groupedByOwner[ownerId]) {
                groupedByOwner[ownerId] = [];
            }
            groupedByOwner[ownerId].push($(this));
        });
        
        // Second pass - reorder appointments by owner groups
        const $container = $('.appointment-card').first().parent();
        $('.appointment-card').hide();
        $('.property-group-header').remove(); // Remove any previous grouping headers
        
        // For each owner, show their appointments
        Object.keys(groupedByOwner).forEach((ownerId, groupIndex) => {
            const cards = groupedByOwner[ownerId];
            
            // Try to get owner name from first appointment
            let ownerName = "Chủ sở hữu";
            if (cards.length > 0) {
                // Find property with this owner ID
                const property = window.propertyList.find(p => p.ownerId === ownerId);
                if (property && property.ownerName) {
                    ownerName = property.ownerName;
                }
            }
            
            // Add a header for this owner group if it has appointments
            if (cards.length > 0) {
                const $header = $(`
                    <div class="owner-group-header mb-2 mt-4" data-owner-id="${ownerId}">
                        <h5 class="mb-1">
                            <i class="bi bi-person me-2"></i>
                            ${ownerName}
                        </h5>
                        <div class="small text-muted">${cards.length} lịch hẹn</div>
                    </div>
                `);
                
                setTimeout(() => {
                    $container.append($header);
                    $header.hide().fadeIn(200);
                    
                    // Append all appointments for this owner
                    cards.forEach((card, cardIndex) => {
                        setTimeout(() => {
                            $container.append(card);
                            card.fadeIn(200);
                        }, cardIndex * 100);
                    });
                }, groupIndex * 300);
            }
        });
    }

    // Function to filter appointments by property with improved empty state handling
    function filterAppointmentsByProperty(propertyId) {
        let foundAny = false;
        
        $('.appointment-card').each(function() {
            const cardPropertyId = $(this).data('property-id');
            if (propertyId && cardPropertyId == propertyId) {
                $(this).fadeIn(300);
                foundAny = true;
            } else {
                $(this).hide();
            }
        });
        
        // Show empty state message if no appointments found
        const $tabPane = $('.appointment-card').first().closest('.tab-pane');
        const $emptyMessage = $tabPane.find('.empty-filtered');
        
        if (!foundAny) {
            if ($emptyMessage.length === 0) {
                $tabPane.append(`
                    <div class="empty-filtered p-4 text-center text-muted">
                        <i class="bi bi-calendar-x mb-2" style="font-size: 2rem;"></i>
                        <p>Không có lịch hẹn nào cho bất động sản này</p>
                        <button class="btn btn-sm btn-outline-primary mt-2 reset-filter">
                            <i class="bi bi-arrow-counterclockwise"></i> Hiển thị tất cả
                        </button>
                    </div>
                `);
                
                // Add event handler for the reset filter button
                $('.reset-filter').on('click', function() {
                    $resetSearch.click();
                });
            } else {
                $emptyMessage.show();
            }
        } else {
            $('.empty-filtered').hide();
        }
    }

    // Make the filterAppointmentsByProperty function available globally
    window.filterAppointmentsByProperty = filterAppointmentsByProperty;
});
