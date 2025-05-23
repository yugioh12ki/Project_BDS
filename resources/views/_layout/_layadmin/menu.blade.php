<!-- sidebar.blade.php -->
<aside class="sidebar">
    <nav class="menu">
      <ul class="tree-menu">
        <li>
          <a href="#" id="user-menu-toggle"><i class="fas fa-users"></i> <span>Quản lý người dùng</span> <i class="fas fa-caret-down"></i></a>
          <ul>
            <li><a href="{{ route('admin.users.byRole', 'Customer') }}">Danh sách khách hàng</a></li>
            <li><a href="{{ route('admin.users.byRole', 'Owner') }}">Danh sách chủ sở hữu</a></li>
            <li><a href="{{ route('admin.users.byRole', 'Agent') }}">Danh sách môi giới</a></li>
          </ul>
        </li>
        <li>
          <a href="#" id="property-menu-toggle">
            <i class="fas fa-list"></i>
            <span>Quản lý BĐS</span>
            @if(isset($pendingPropertyCount) && $pendingPropertyCount > 0)
              <span class="badge bg-danger rounded-pill ms-2">{{ $pendingPropertyCount }}</span>
            @endif
            <i class="fas fa-caret-down"></i>
          </a>
          <ul>
            <li>
              @php
                use App\Models\Property;
                $pendingRentalCount = Property::where('TypePro', 'Rent')->where('Status', 'pending')->count();
              @endphp
              <a href="{{ route('admin.property.type.status', ['type' => 'rent']) }}?status=pending">
                Cho thuê
                @if($pendingRentalCount > 0)
                  <span class="badge bg-danger rounded-pill ms-2">{{ $pendingRentalCount }}</span>
                @endif
              </a>
            </li>
            <li>
              @php
                $pendingSaleCount = Property::where('TypePro', 'Sale')->where('Status', 'pending')->count();
              @endphp
              <a href="{{ route('admin.property.type.status', ['type' => 'sale']) }}?status=pending">
                Cho bán
                @if($pendingSaleCount > 0)
                  <span class="badge bg-danger rounded-pill ms-2">{{ $pendingSaleCount }}</span>
                @endif
              </a>
            </li>
            <li>
                <a href="{{ route('admin.property.create') }}">Tiếp nhận hồ sơ</a>
            </li>
          </ul>
        </li>
        <li><a href="{{ route('admin.transaction') }}"><i class="fas fa-file-contract"></i> <span>Hợp đồng giao dịch BĐS</span></a></li>
        <li><a href="{{ route('admin.commission') }}"><i class="fas fa-money-bill-wave"></i> <span>Hoa hồng môi giới</span></a></li>
        <li><a href="{{ route('admin.feedback') }}"><i class="fas fa-check-circle"></i> <span>Kiểm duyệt đánh giá KH</span></a></li>
        <li><a href="{{ route('admin.appointment') }}"><i class="fas fa-calendar-alt"></i> <span>Quản lý cuộc hẹn</span></a></li>
        <li><a href="{{ route('admin.dashboard') }}"><i class="fas fa-chart-bar"></i> <span>Thống kê báo cáo</span></a></li>
        <li><a href="{{ route('admin.chatbot.questions.index') }}"><i class="fas fa-comments"></i> <span>Quản lý Chatbox hỗ trợ KH</span></a></li>
      </ul>
    </nav>
</aside>
<script>
document.addEventListener('DOMContentLoaded', function() {
  // Toggle main menu (level 1)
  document.querySelectorAll('.tree-menu > li > a').forEach(function(el) {
    el.addEventListener('click', function(e) {
      if (el.nextElementSibling && el.nextElementSibling.tagName === 'UL') {
        e.preventDefault();
        var parent = el.parentElement;
        parent.classList.toggle('open');
      }
    });
  });

  // Không cần xử lý submenu (đã loại bỏ)

  // Add badge notification to menu if needed
  if (typeof pendingPropertyCount !== 'undefined' && pendingPropertyCount > 0) {
    // Highlight the property menu
    const propertyMenu = document.getElementById('property-menu-toggle');
    if (propertyMenu) {
      propertyMenu.classList.add('has-notification');
    }
  }
});
</script>


