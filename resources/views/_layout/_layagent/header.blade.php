<header class="owner-header">
    <div class="header-left">
        <a href="{{ route('agent.dashboard') }}">
            <img src="{{ asset('images/logo_owner.png') }}" alt="Logo Agent" class="header-logo" style="height:40px;object-fit:contain;">
        </a>
    </div>

    <div class="header-nav">
        <ul>
            <li><a href="{{ route('agent.dashboard') }}" class="header-nav-link"><i class="bi bi-house-fill"></i> Trang Chủ</a></li>
            <li><a href="{{ route('agent.brokers') }}" class="header-nav-link"><i class="bi bi-list-task"></i>Bảng Phân Công</a></li>
            <li><a href="{{ route('agent.appointments') }}" class="header-nav-link"><i class="bi bi-calendar2-event-fill"></i>Quản Lý Lịch Hẹn</a></li>
            <li><a href="{{ route('agent.transactions') }}" class="header-nav-link"><i class="bi bi-cash-stack"></i>Lịch Sử Giao Dịch</a></li>
        </ul>
    </div>

    <div class="header-right dropdown">
        <div class="phone-number-wrapper">
            <a href="tel:0123456789" class="phone-link">
                <i class="bi bi-telephone-fill"></i>
                <span>19001881</span>
            </a>
        </div>
        
        <div class="notification-icon-wrapper" style="position:relative;">
            <a href="#" class="notification-icon" aria-label="Thông báo" style="color:#4A4A4A; font-size:1.2rem; display:flex; padding:5px;" id="notificationBell">
                <i class="bi bi-bell"></i>
                <span class="notification-count" style="display: none; position:absolute; top:-5px; right:-5px; background:#E74C3C; color:white; border-radius:50%; padding:1px 5px; font-size:0.7rem; font-weight:bold;" id="notificationCount">0</span>
            </a>
            
            <!-- Notification Popup -->
            <div class="notification-dropdown" id="notificationDropdown" style="display: none; position: absolute; top: 100%; right: 0; width: 350px; max-height: 400px; background: white; border: 1px solid #ddd; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.15); z-index: 1000; overflow: hidden;">
                <div class="notification-header" style="padding: 15px; border-bottom: 1px solid #eee; background: #f8f9fa;">
                    <h6 style="margin: 0; font-weight: 600; color: #333;">Thông báo</h6>
                </div>
                <div class="notification-body" id="notificationList" style="max-height: 300px; overflow-y: auto;">
                    <div class="text-center py-4" id="notificationLoading">
                        <i class="bi bi-hourglass-split" style="font-size: 1.5rem; color: #6c757d;"></i>
                        <p style="margin: 10px 0 0 0; color: #6c757d;">Đang tải...</p>
                    </div>
                </div>
                <div class="notification-footer" style="padding: 10px 15px; border-top: 1px solid #eee; background: #f8f9fa; text-align: center;">
                    <a href="{{ route('agent.appointments') }}" style="color: #007bff; text-decoration: none; font-size: 0.9rem;">Xem tất cả</a>
                </div>
            </div>
        </div>

        @if(Auth::check() && Auth::user()->Role === 'Agent')
            <button class="user-greeting dropdown-toggle" type="button" id="dropdownUserMenu" data-bs-toggle="dropdown" aria-expanded="false">
                Xin chào, {{ Auth::user()->Name }}
            </button>
            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownUserMenu">
                <li><a class="dropdown-item" href="{{ route('agent.profile') }}"><i class="bi bi-person-circle me-2"></i>Thông Tin Cá Nhân</a></li>
                <li><a class="dropdown-item" href="#"><i class="bi bi-key"></i>Đổi Mật Khẩu</a></li>
                <li><hr class="dropdown-divider"></li>
                <li>
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button class="dropdown-item" type="submit"><i class="bi bi-box-arrow-right me-2"></i>Đăng xuất</button>
                    </form>
                </li>
            </ul>
        @endif
    </div>
</header> 