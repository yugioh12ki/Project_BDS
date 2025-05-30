<header>
    <nav class="navbar">
      <div class="navbar__left">
        <img src="#" alt="Logo" class="navbar__logo">
      </div>

      <ul class="navbar__menu">
        <li><a href="/">Trang chủ</a></li>
      </ul>

      <div class="navbar__right">
        @if(Auth::check())
          <div class="dropdown">
            <button class="dropdown-toggle">Xin chào, {{ Auth::user()->Name }} ▼</button>
            <div class="dropdown-menu">
              <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="dropdown-item">Đăng xuất</button>
              </form>
            </div>
          </div>
        @endif
      </div>
    </nav>
</header>

