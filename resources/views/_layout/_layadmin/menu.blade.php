<!-- sidebar.blade.php -->
<aside class="sidebar">
    <nav class="menu">
      <ul>
        <li class="dropdown">
          <a href="#" class="dropdown-toggle">Quản lý Tài Khoản</a>
          <ul class="submenu">
            <li><a href="#">Xem Danh Sách</a></li>
            <li><a href="#">Thêm tài khoản</a></li>
            <li><a href="#">Xóa Tài Khoản</a></li>
            <li><a href="#">Chỉnh sửa</a></li>
          </ul>
        </li>
        <li><a href="#">Quản lý danh mục BĐS, khu vực</a></li>
        <li><a href="#">Kiểm duyệt đánh giá khách hàng</a></li>
        <li><a href="#">Quản lý cuộc hẹn</a></li>
        <li><a href="#">Quản lý hợp đồng (mua bán / cho thuê)</a></li>
        <li><a href="#">Quản lý hoa hồng môi giới</a></li>
        <li><a href="#">Thống kê báo cáo</a></li>
        <li><a href="#">Quản lý Chatbox hỗ trợ KH</a></li>
      </ul>
    </nav>
  </aside>
  <script>
    document.addEventListener("DOMContentLoaded", function () {
      const toggles = document.querySelectorAll('.dropdown-toggle');
      const sidebar = document.querySelector('.sidebar');

      toggles.forEach(toggle => {
        toggle.addEventListener('click', function (e) {
          e.preventDefault();
          const parent = this.closest('.dropdown');

          // Đóng các mục khác nếu cần (accordion-style)
          document.querySelectorAll('.dropdown.open').forEach(item => {
            if (item !== parent) item.classList.remove('open');
          });

          parent.classList.toggle('open');

          // Kiểm tra nếu có dropdown nào đang mở -> sidebar mở rộng
          const anyOpen = document.querySelector('.dropdown.open');
          if (anyOpen) {
            sidebar.classList.add('expanded');
          } else {
            sidebar.classList.remove('expanded');
          }
        });
      });
    });
  </script>

