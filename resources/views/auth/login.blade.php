@extends('_layout.app')

@section('login')
<main class="main-content">
    <div class="login-container split-box">
      <div class="image-section">
        {{-- <img src="your-image.jpg" alt="Login illustration"> --}}
      </div>
      <div class="form-section">
        <form class="login-form" method="POST" action="{{ route('login.authenticate') }}">
            @csrf
            <h1 class="login-title">Đăng nhập</h1>


          <label for="username">Email</label>
          <input type="email" id="email" name="email" placeholder="Nhập email" required>

          <label for="password">Password</label>
          <input type="password" id="password" name="password" placeholder="Nhập Password" required>

            @if($errors->any())
                <ul>
                    @foreach ($errors->all() as $error)
                        <li style="color: red">{{ $error }}</li>
                    @endforeach
                </ul>
            @endif
            @if (session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

          <div class="button-group">
            <button type="submit">Đăng nhập</button>
            <button type="button" class="register" onclick="window.location.href='{{ route('register') }}'">Đăng ký</button>
          </div>

          <a href="#" class="forgot">Forgot password?</a>
        </form>
      </div>
    </div>
  </main>

@endsection
