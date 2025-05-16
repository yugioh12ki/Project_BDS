// Kích hoạt dropdown Bootstrap 5 cho user menu nếu cần (phòng trường hợp render động hoặc JS khác gây lỗi)
document.addEventListener('DOMContentLoaded', function () {
    var dropdownToggle = document.getElementById('dropdownUserMenu');
    if (dropdownToggle && typeof bootstrap !== 'undefined') {
        // Nếu dropdown chưa được kích hoạt bởi Bootstrap, kích hoạt thủ công
        if (!dropdownToggle.classList.contains('show')) {
            new bootstrap.Dropdown(dropdownToggle);
        }
    }
});
