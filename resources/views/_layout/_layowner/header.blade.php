<header class="owner-header">
    <div class="header-left">
        <a href="{{ route('owner.dashboard') }}">
            <img src="{{ asset('images/logo_owner.png') }}" alt="Logo Chủ sở hữu" class="header-logo" style="height:40px;object-fit:contain;">
        </a>
    </div>
    <div class="header-right dropdown">
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