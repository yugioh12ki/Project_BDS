<header class="owner-header">
    <div class="header-left">
        <a href="{{ route('agent.dashboard') }}">
            <img src="{{ asset('images/logo_owner.png') }}" alt="Logo Agent" class="header-logo" style="height:40px;object-fit:contain;">
        </a>
    </div>

    <div class="header-nav">
        <ul>
            <li><a href="{{ route('agent.dashboard') }}" class="header-nav-link"><i class="bi bi-house-fill"></i> Trang Chủ</a></li>
            <li><a href="{{ route('agent.listings') }}" class="header-nav-link"><i class="bi bi-list-task"></i>Danh Sách Phân Công Môi Giới</a></li>
            <li><a href="{{ route('agent.appointments') }}" class="header-nav-link"><i class="bi bi-calendar2-event-fill"></i>Tạo Lịch Hẹn Xem Nhà</a></li>
            <li><a href="{{ route('agent.appointments') }}" class="header-nav-link"><i class="bi bi-cash-stack"></i>Lịch Sử Giao Dịch</a></li>
        </ul>
    </div>

    <div class="header-right dropdown">
        <div class="phone-number-wrapper">
            <a href="tel:0123456789" class="phone-link">
                <i class="bi bi-telephone-fill"></i>
                <span>0123456789</span>
            </a>
        </div>
        <div class="notification-icon-wrapper">
            <a href="#" class="notification-icon" aria-label="Thông báo">
                <i class="bi bi-bell"></i>
                <span class="notification-count" style="display: none;">0</span>
            </a>
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