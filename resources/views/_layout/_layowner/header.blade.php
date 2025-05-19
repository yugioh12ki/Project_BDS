<header class="owner-header">
    <div class="header-left">
        <a href="{{ route('owner.dashboard') }}">
            <img src="{{ asset('images/logo_owner.png') }}" alt="Logo Chủ sở hữu" class="header-logo" style="height:40px;object-fit:contain;">
        </a>
    </div>

    <div class="header-nav" style="display:flex; align-items:center; flex-grow:1; justify-content:center;">
        <ul style="display:flex; list-style:none; margin:0; padding:0; gap:20px;">
            <li style="margin:0;"><a href="{{ route('owner.dashboard') }}" class="header-nav-link" style="text-decoration:none; color:#4A4A4A; font-weight:500; padding:8px 12px; display:flex; align-items:center;"><i class="bi bi-house-fill" style="margin-right:6px;"></i> Trang Chủ</a></li>
            <li style="margin:0;"><a href="{{ route('owner.property.index') }}" class="header-nav-link" style="text-decoration:none; color:#4A4A4A; font-weight:500; padding:8px 12px; display:flex; align-items:center;"><i class="bi bi-building" style="margin-right:6px;"></i> Danh Sách BĐS</a></li>
            <li style="margin:0;"><a href="{{ route('owner.appointments.index') }}" class="header-nav-link" style="text-decoration:none; color:#4A4A4A; font-weight:500; padding:8px 12px; display:flex; align-items:center;"><i class="bi bi-calendar-event" style="margin-right:6px;"></i> Lịch Hẹn</a></li>
            <li style="margin:0;"><a href="{{ route('owner.transactions.index') }}" class="header-nav-link" style="text-decoration:none; color:#4A4A4A; font-weight:500; padding:8px 12px; display:flex; align-items:center;"><i class="bi bi-cash-stack" style="margin-right:6px;"></i> Giao Dịch</a></li>
        </ul>
    </div>

    <div class="header-right dropdown" style="display:flex; align-items:center; gap:15px;">
        <div class="phone-number-wrapper" style="display:flex; align-items:center;">
            <a href="tel:0123456789" class="phone-link" style="text-decoration:none; color:#4A4A4A; display:flex; align-items:center; font-weight:500; font-size:0.9rem; background-color: #e8b885; padding: 5px 15px; border-radius: 25px; border: 1px solid rgba(255,255,255,0.3); box-shadow: 0 1px 2px rgba(0,0,0,0.1);">
                <i class="bi bi-telephone-fill" style="font-size:1rem; margin-right:5px; color: white;"></i>
                <span style="color: white;">0123456789</span>
            </a>
        </div>
        <div class="notification-icon-wrapper" style="position:relative;">
            <a href="#" class="notification-icon" aria-label="Thông báo" style="color:#4A4A4A; font-size:1.2rem; display:flex; padding:5px;">
                <i class="bi bi-bell"></i>
                <span class="notification-count" style="display: none; position:absolute; top:-5px; right:-5px; background:#E74C3C; color:white; border-radius:50%; padding:1px 5px; font-size:0.7rem; font-weight:bold;">0</span>
            </a>
        </div>
        @if(Auth::check() && Auth::user()->Role === 'Owner')
            <button class="user-greeting dropdown-toggle" type="button" id="dropdownUserMenu" data-bs-toggle="dropdown" aria-expanded="false">
                Xin chào, {{ Auth::user()->Name }}
            </button>
            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownUserMenu">
                <li>
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button class="dropdown-item" type="submit">Đăng xuất</button>
                    </form>
                </li>
            </ul>
        @endif
    </div>
</header>