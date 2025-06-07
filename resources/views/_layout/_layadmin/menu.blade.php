<!-- sidebar.blade.php -->
@php
  // Get current user's admin profile and position
  $currentUser = auth()->user();
  $userPosition = null;

  if ($currentUser && $currentUser->Role === 'Admin') {
    $adminProfile = $currentUser->profile_admin;
    $userPosition = $adminProfile ? $adminProfile->TenChucVu : null;
  }

  // Debug information - remove after testing
  if(config('app.debug')) {
    echo "<!-- DEBUG INFO:
    Current User ID: " . ($currentUser ? $currentUser->UserID : 'null') . "
    Current User Role: " . ($currentUser ? $currentUser->Role : 'null') . "
    Admin Profile exists: " . ($adminProfile ?? 'null') . "
    User Position: " . ($userPosition ?? 'null') . "
    -->";
  }
@endphp

<aside class="sidebar">
    <!-- User Profile Section -->
    <div class="user-profile-section">
        @if(Auth::check())
            <div class="user-avatar">
                <i class="fas fa-user-circle"></i>
            </div>
            <div class="user-info">
                <h4 class="user-name">{{ Auth::user()->Name }}</h4>
                <p class="user-role">{{ Auth::user()->Role ?? 'Admin' }}</p>
                @if($userPosition)
                    <span class="user-position">{{ $userPosition }}</span>
                @endif

            </div>
            <div class="user-actions">
                <form action="{{ route('logout') }}" method="POST" style="margin: 0;">
                    @csrf
                    <button type="submit" class="logout-btn" title="Đăng xuất">
                        <i class="fas fa-sign-out-alt"></i>
                    </button>
                </form>
                <a href="{{ route('home') }}" class="home-btn" title="Trang chủ">
                    <i class="fas fa-home"></i>
                </a>
            </div>
        @endif
    </div>

    <!-- Navigation Menu -->
    <nav class="menu">
      <ul class="tree-menu">
        {{-- User Management - Hide Staff list for Nhân Viên and Giám Đốc --}}
        <li>
          <a href="#" id="user-menu-toggle"><i class="fas fa-users"></i> <span>Quản lý người dùng</span> <i class="fas fa-caret-down"></i></a>
          <ul>
            <li><a href="{{ route('admin.users.byRole', 'Customer') }}">Danh sách khách hàng</a></li>
            <li><a href="{{ route('admin.users.byRole', 'Owner') }}">Danh sách chủ sở hữu</a></li>
            <li><a href="{{ route('admin.users.byRole', 'Agent') }}">Danh sách môi giới</a></li>
            {{-- Hide Staff list for Nhân Viên and Giám Đốc --}}
            @if($userPosition !== 'Nhân viên' && $userPosition !== 'Giám đốc')
              <li><a href="{{ route('admin.users.byRole', 'Admin') }}">Danh sách Nhân Viên</a></li>
            @endif
          </ul>
        </li>

        {{-- Property Management - Hide for Nhân Viên --}}
        @if($userPosition !== 'Nhân viên')
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
                $pendingRentalCount = \App\Models\Property::where('TypePro', 'Rent')->where('Status', 'pending')->count();
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
                $pendingSaleCount = \App\Models\Property::where('TypePro', 'Sale')->where('Status', 'pending')->count();
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
        @endif

        {{-- Transaction Management - Always show --}}
        <li><a href="{{ route('admin.transaction') }}"><i class="fas fa-file-contract"></i> <span>Hợp đồng giao dịch</span></a></li>

        {{-- Commission Management - Hide for Nhân Viên --}}
        @if($userPosition !== 'Nhân viên')
        <li><a href="{{ route('admin.commission') }}"><i class="fas fa-money-bill-wave"></i> <span>Hoa hồng môi giới</span></a></li>
        @endif

        {{-- Feedback Management - Always show --}}
        <li><a href="{{ route('admin.feedback') }}"><i class="fas fa-check-circle"></i> <span>Kiểm duyệt đánh giá</span></a></li>



        {{-- Appointment Management - Hide for Nhân Viên --}}
        @if($userPosition !== 'Nhân viên')
        <li><a href="{{ route('admin.appointment') }}"><i class="fas fa-calendar-alt"></i> <span>Quản lý cuộc hẹn</span></a></li>
        @endif

        {{-- Dashboard - Always show --}}
        <li><a href="{{ route('admin.dashboard') }}"><i class="fas fa-chart-bar"></i> <span>Thống kê báo cáo</span></a></li>

        {{-- Chatbox Management - Hide for Nhân Viên --}}
        @if($userPosition !== 'Nhân viên')
        <li><a href="{{ route('admin.chatbot.questions.index') }}"><i class="fas fa-comments"></i> <span>Quản lý Chatbox và FAQ</span></a></li>
        @endif
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


