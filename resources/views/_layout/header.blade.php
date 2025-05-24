<header>
    <nav class="navbar">
        <div class="navbar__left">
            <img src="#" alt="" class="navbar__logo">
            <ul class="navbar__menu">
                <li><a href="{{ route('home') }}">Trang chủ</a></li>
                <li><a href="#">Giới thiệu</a></li>
                <li><a href="#">Mua</a></li>
                <li><a href="#">Cho Thuê</a></li>
                @auth
                    <li><a href="{{ route('customer.appointments.index') }}">Lịch hẹn</a></li>
                @else
                    <li><a href="{{ route('login') }}">Lịch hẹn</a></li>
                @endauth
                <li><a href="#">Ý kiến khách hàng</a></li>
                <li><a href="#">Liên hệ</a></li>
                <li><a href="#">Danh bạ</a></li>
                <li><a href="#">Lịch sử giao dịch</a></li>
            </ul>
        </div>
        <div class="navbar__right">
            <div class="navbar__call">
                <i class="bi bi-telephone"></i>
                <span>19001881</span>
            </div>
            @if(Auth::check() && Auth::user()->Role == 'Customer')
                <div class="user-dropdown">
                    <button class="user-dropdown-btn" type="button" id="userDropdownBtn">
                        Xin chào, {{ Auth::user()->Name }} <span style="font-size: 12px;">&#9662;</span>
                    </button>
                    <div class="user-dropdown-content" id="userDropdownContent">
                        <a href="{{ route('customer.profile') }}" class="dropdown-item">Thông tin cá nhân</a>
                        <a href="{{ route('customer.change-password') }}" class="dropdown-item">Đổi mật khẩu</a>
                        <form action="{{ route('logout') }}" method="POST" style="margin:0;">
                            @csrf
                            <button type="submit" class="logout-btn">Đăng xuất</button>
                        </form>
                    </div>
                </div>
            @else
                <button class="btn-login" onclick="window.location.href='{{ route('login') }}'">Đăng nhập</button>
                <button class="btn-register" onclick="window.location.href='{{ route('register') }}'">Đăng ký</button>
            @endif
        </div>
    </nav>
</header>
