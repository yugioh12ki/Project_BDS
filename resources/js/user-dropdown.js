document.addEventListener('DOMContentLoaded', function () {
    var btn = document.getElementById('userDropdownBtn');
    var content = document.getElementById('userDropdownContent');

    if (btn && content) {
        btn.addEventListener('click', function (e) {
            e.stopPropagation();
            content.classList.toggle('show');
        });

        // Ẩn dropdown nếu click ra ngoài
        document.addEventListener('click', function (event) {
            if (!btn.contains(event.target) && !content.contains(event.target)) {
                content.classList.remove('show');
            }
        });
    }
});
