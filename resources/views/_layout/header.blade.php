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
                    @if(Auth::user()->Role == 'Customer')
                        <li><a href="{{ route('customer.appointments.index') }}">Lịch hẹn</a></li>
                    @endif
                @else
                    <li><a href="{{ route('login') }}">Lịch hẹn</a></li>
                @endauth
                <li><a href="#">Danh bạ</a></li>
                @if(!Auth::check() || Auth::user()->Role == 'Customer')
                    <li><a href="#">Lịch sử giao dịch</a></li>
                @endif
            </ul>
        </div>
        <div class="navbar__right">
            <div class="navbar__call">
                <i class="bi bi-telephone"></i>
                <span>19001881</span>
            </div>
            @auth
                <!-- Notification Icon - chỉ hiển thị cho Customer -->
                @if(Auth::user()->Role == 'Customer')
                <div class="notification-dropdown">
                    <button class="notification-btn" id="notificationBtn">
                        <i class="bi bi-bell"></i>
                        <span class="notification-badge" id="notificationCount">0</span>
                    </button>
                    <div class="notification-content" id="notificationContent">
                        <div class="notification-header">
                            <h6>Thông báo</h6>
                        </div>
                        <div class="notification-list" id="notificationList">
                            <!-- Notifications will be loaded here -->
                        </div>
                    </div>
                </div>
                @endif

                <!-- User Dropdown - hiển thị cho tất cả role đã đăng nhập -->
                <div class="user-dropdown">
                    <button class="user-dropdown-btn" type="button" id="userDropdownBtn">
                        Xin chào, {{ Auth::user()->Name }}
                        @if(Auth::user()->Role != 'Customer')
                            ({{ Auth::user()->Role }})
                        @endif
                        <span style="font-size: 12px;">&#9662;</span>
                    </button>
                    <div class="user-dropdown-content" id="userDropdownContent">
                        @if(Auth::user()->Role == 'Customer')
                            <a href="{{ route('customer.profile') }}" class="dropdown-item">Thông tin cá nhân</a>
                            <a href="{{ route('customer.change-password') }}" class="dropdown-item">Đổi mật khẩu</a>
                        @elseif(Auth::user()->Role == 'Admin')
                            <a href="{{ route('admin.dashboard') }}" class="dropdown-item">Bảng điều khiển</a>
                        @elseif(Auth::user()->Role == 'Owner')
                            <a href="#" class="dropdown-item">Thông tin cá nhân</a>
                        @elseif(Auth::user()->Role == 'Agent')
                            <a href="#" class="dropdown-item">Thông tin cá nhân</a>
                        @endif
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
