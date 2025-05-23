/**
 * Utilities for agent list sorting and badge handling
 */

document.addEventListener('DOMContentLoaded', function() {
    // Fix badge count NaN issues
    fixBadgeCounts();

    // Add hover effects to agent items
    addAgentItemEffects();

    // Add tooltips to badge counts
    addBadgeTooltips();
});

/**
 * Fixes NaN issues in agent badge counts by improving parsing
 */
function fixBadgeCounts() {
    // Find all badge elements
    const badges = document.querySelectorAll('.badge');

    badges.forEach(badge => {
        try {
            // Use regex to extract number from badge text
            const badgeText = badge.textContent.trim();
            const matches = badgeText.match(/(\d+)/);

            if (matches && matches[1]) {
                const parsedValue = parseInt(matches[1], 10);
                // Make sure it's a valid number
                if (!isNaN(parsedValue)) {
                    // Store the parsed value as a data attribute for future use
                    badge.dataset.count = parsedValue;

                    // Update color based on count
                    updateBadgeColor(badge, parsedValue);
                } else {
                    console.warn('Badge text could not be converted to a number:', badgeText);
                    badge.dataset.count = 0;
                }
            }
        } catch (error) {
            console.error('Error processing badge text:', error);
        }
    });
}

/**
 * Updates badge color based on property count
 */
function updateBadgeColor(badge, count) {
    badge.classList.remove('bg-danger', 'bg-warning', 'bg-primary', 'bg-secondary');

    if (count >= 10) {
        badge.classList.add('bg-danger');
    } else if (count >= 7) {
        badge.classList.add('bg-warning');
    } else if (count > 0) {
        badge.classList.add('bg-primary');
    } else {
        badge.classList.add('bg-secondary');
    }
}

/**
 * Adds hover effects to agent items
 */
function addAgentItemEffects() {
    const agentItems = document.querySelectorAll('.agent-item');

    agentItems.forEach(item => {
        item.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-2px)';
            this.style.boxShadow = '0 4px 8px rgba(0,0,0,0.1)';
        });

        item.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0)';
            this.style.boxShadow = 'none';
        });
    });
}

/**
 * Adds tooltips to badge counts
 */
function addBadgeTooltips() {
    const badges = document.querySelectorAll('.badge');

    badges.forEach(badge => {
        const count = badge.dataset.count || 0;
        badge.title = `${count} bất động sản được quản lý`;
    });
}
