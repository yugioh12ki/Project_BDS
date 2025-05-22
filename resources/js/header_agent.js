document.addEventListener('DOMContentLoaded', function() {
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