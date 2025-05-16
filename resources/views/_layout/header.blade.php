<header>
    <nav class="navbar">
        <div class="navbar__left">
            <img src="#" alt="" class="navbar__logo">
            <ul class="navbar__menu">
                <li><a href="{{ route('home') }}">Trang chủ</a></li>
                <li></li>
                <li><a href="#">Cho Thuê</a></li>
                <li></li>
                <li><a href="#">Cho Bán</a></li>
                <li></li>
                <li><a href="#">Danh Bạ</a></li>
                <li><a href="#"></a></li>
            </ul>
        </div>
        <div class="navbar__right">
            @if(Auth::check() && Auth::user()->Role == 'Customer')
                <div class="user-dropdown">
                    <button class="user-dropdown-btn" type="button" id="userDropdownBtn">
                        Xin chào, {{ Auth::user()->Name }} <span style="font-size: 12px;">&#9662;</span>
                    </button>
                    <div class="user-dropdown-content" id="userDropdownContent">
                        <form action="{{ route('logout') }}" method="POST" style="margin:0;">
                            @csrf
                            <button type="submit" class="logout-btn">Đăng xuất</button>
                        </form>
                    </div>
                </div>
            @else
                <button class="icon-label">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v2a2 2 0 002 2h12a2 2 0 002-2v-2M7 10l5 5 5-5M12 4v12" />
                    </svg>
                    <span>Tải xuống</span>
                </button>
                <button class="btn-login" onclick="window.location.href='{{ route('login') }}'">Đăng nhập</button>
                <button class="btn-register" onclick="window.location.href='{{ route('register') }}'">Đăng ký</button>
                <button class="icon-label">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    <span>Đăng tin</span>
                </button>
            @endif
        </div>
    </nav>
</header>
