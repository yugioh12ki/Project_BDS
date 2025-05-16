document.addEventListener('DOMContentLoaded', function () {
    const hamburgerBtn = document.getElementById('drawerHamburger');
    const drawerList = document.getElementById('drawerList');
    const overlay = document.getElementById('drawerOverlay');

    // Đảm bảo menu luôn ẩn khi load
    drawerList.classList.remove('active');
    overlay.style.display = 'none';

    // Bấm hamburger thì show/hide menu
    hamburgerBtn.addEventListener('click', function (e) {
        e.stopPropagation();
        const isActive = drawerList.classList.toggle('active');
        overlay.style.display = isActive ? 'block' : 'none';
        // Không thay đổi z-index ở đây để tránh lỗi overlay
    });

    // Click overlay thì tắt menu
    overlay.addEventListener('click', function () {
        drawerList.classList.remove('active');
        overlay.style.display = 'none';
    });

    // Bấm ESC tắt menu
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') {
            drawerList.classList.remove('active');
            overlay.style.display = 'none';
        }
    });

    // Submenu toggle
    document.querySelectorAll('.drawer-has-submenu > a').forEach(function (item) {
        item.addEventListener('click', function (e) {
            e.preventDefault();
            const parent = this.parentElement;
            parent.classList.toggle('open');
        });
    });

    document.querySelectorAll('.drawer-has-submenu').forEach(function(dropdown) {
        var toggle = dropdown.querySelector('.dropdown-toggle');
        var submenu = dropdown.querySelector('.drawer-submenu');
        toggle.addEventListener('click', function(e) {
            e.preventDefault();
            dropdown.classList.toggle('active');
        });
    });

    // Hiển thị form Thêm BĐS khi click sidebar
    var addBtn = document.getElementById('sidebarAddPropertySubBtn');
    if (addBtn) {
        addBtn.addEventListener('click', function(e) {
            e.preventDefault();
            // Mở offcanvas Thêm BĐS (Bootstrap 5)
            var drawer = document.getElementById('addPropertyDrawer');
            if (drawer) {
                var bsDrawer = bootstrap.Offcanvas.getOrCreateInstance(drawer);
                bsDrawer.show();
            }
        });
    }
});