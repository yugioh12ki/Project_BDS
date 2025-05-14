<header style="background-color: #333; color: #fff; padding: 10px;">
    <h2 style="margin: 0;">Bảng điều khiển Chủ sở hữu</h2>
    <nav>
        <a href="{{ route('owner.dashboard') }}" style="color: #fff; margin-right: 10px;">Trang chủ</a>
        <a href="{{ route('logout') }}"
           onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
           style="color: #fff;">Đăng xuất</a>
        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
            @csrf
        </form>
    </nav>
</header>
