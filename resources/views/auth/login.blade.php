@extends('_layout.app')

@section('login')
<main class="main-content">
    <div class="login-container">
        <div class="image-section">
            <div class="welcome-content">
                <h2>Chào mừng trở lại!</h2>
                <p>Đăng nhập để tiếp tục hành trình bất động sản của bạn</p>
                <div class="feature-list">
                    <div class="feature">
                        <i class="fas fa-search"></i>
                        <span>Tìm kiếm nhanh chóng</span>
                    </div>
                    <div class="feature">
                        <i class="fas fa-bookmark"></i>
                        <span>Lưu tin yêu thích</span>
                    </div>
                    <div class="feature">
                        <i class="fas fa-chart-line"></i>
                        <span>Theo dõi thị trường</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="form-section">
            <form class="login-form" method="POST" action="{{ route('login.authenticate') }}">
                @csrf
                <h1 class="login-title">Đăng Nhập</h1>
                <p class="subtitle">Vui lòng nhập thông tin đăng nhập của bạn</p>

                @if (session('success'))
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle"></i>
                        {{ session('success') }}
                    </div>
                @endif

                @if (session('error'))
                    <div class="alert alert-error">
                        <i class="fas fa-exclamation-circle"></i>
                        {{ session('error') }}
                    </div>
                @endif

                @if($errors->any())
                    <div class="alert alert-error">
                        <i class="fas fa-exclamation-triangle"></i>
                        <strong>Vui lòng kiểm tra lại thông tin:</strong>
                        <ul class="error-list">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="form-group">
                    <label for="email">Email <span style="color: red;">*</span></label>
                    <input type="email" id="email" name="email"
                           placeholder="Nhập địa chỉ email của bạn"
                           value="{{ old('email') }}" required>
                </div>

                <div class="form-group">
                    <label for="password">Mật khẩu <span style="color: red;">*</span></label>
                    <input type="password" id="password" name="password"
                           placeholder="Nhập mật khẩu của bạn" required>
                </div>

                <div class="button-group">
                    <button type="submit">Đăng Nhập</button>
                    <button type="button" class="register" onclick="window.location.href='{{ route('register') }}'">
                        Đăng Ký
                    </button>
                </div>

                <a href="{{ route('password.reset') }}" class="forgot">Quên mật khẩu?</a>
            </form>
        </div>
    </div>
</main>

@endsection
