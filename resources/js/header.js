// Header functionality for dropdown menu
document.addEventListener('DOMContentLoaded', function() {
    const userDropdownBtn = document.getElementById('userDropdownBtn');
    const userDropdown = document.querySelector('.user-dropdown');

    if (userDropdownBtn && userDropdown) {
        userDropdownBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            userDropdown.classList.toggle('active');
        });

        // Close dropdown when clicking outside
        document.addEventListener('click', function(e) {
            if (!userDropdown.contains(e.target)) {
                userDropdown.classList.remove('active');
            }
        });

        // Prevent dropdown from closing when clicking inside
        const userDropdownContent = document.querySelector('.user-dropdown-content');
        if (userDropdownContent) {
            userDropdownContent.addEventListener('click', function(e) {
                e.stopPropagation();
            });
        }
    }
});
