document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM loaded, initializing dropdown...');

    // Manual dropdown toggle
    const dropdownToggle = document.getElementById('dropdownUserMenu');
    const dropdownMenu = dropdownToggle ? dropdownToggle.nextElementSibling : null;

    if (dropdownToggle && dropdownMenu) {
        console.log('Dropdown elements found, setting up manual toggle');

        // Toggle dropdown on click
        dropdownToggle.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();

            const isOpen = dropdownMenu.classList.contains('show');

            // Close all other dropdowns first
            document.querySelectorAll('.dropdown-menu.show').forEach(menu => {
                menu.classList.remove('show');
            });

            // Toggle current dropdown
            if (!isOpen) {
                dropdownMenu.classList.add('show');
                dropdownToggle.setAttribute('aria-expanded', 'true');
            } else {
                dropdownMenu.classList.remove('show');
                dropdownToggle.setAttribute('aria-expanded', 'false');
            }
        });

        // Close dropdown when clicking outside
        document.addEventListener('click', function(e) {
            if (!dropdownToggle.contains(e.target) && !dropdownMenu.contains(e.target)) {
                dropdownMenu.classList.remove('show');
                dropdownToggle.setAttribute('aria-expanded', 'false');
            }
        });

        // Close dropdown on Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                dropdownMenu.classList.remove('show');
                dropdownToggle.setAttribute('aria-expanded', 'false');
            }
        });

        console.log('Manual dropdown initialized successfully');
    } else {
        console.error('Dropdown elements not found');
    }

    // Handle notifications display
    const notificationIcon = document.querySelector('.notification-icon');
    if (notificationIcon) {
        notificationIcon.addEventListener('click', function(e) {
            e.preventDefault();
            // Display notification panel - can be implemented later
            console.log('Notification icon clicked');
        });
    }

    // Highlight current page in navigation
    const currentPath = window.location.pathname;
    const navLinks = document.querySelectorAll('.header-nav-link');

    navLinks.forEach(link => {
        if (link.getAttribute('href') === currentPath || currentPath.includes(link.getAttribute('href'))) {
            link.style.color = '#3d8bfd';
            link.style.fontWeight = 'bold';
        }
    });

    // Mobile navigation toggle (if implemented)
    const mobileToggle = document.querySelector('.mobile-menu-toggle');
    if (mobileToggle) {
        mobileToggle.addEventListener('click', function() {
            const navMenu = document.querySelector('.header-nav');
            if (navMenu) {
                navMenu.classList.toggle('show-mobile');
            }
        });
    }
});
