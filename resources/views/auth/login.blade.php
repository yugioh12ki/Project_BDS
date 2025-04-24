@extends('_layout.app')

@section('login')
<main class="main-content">
    <div class="login-container split-box">
      <div class="image-section">
        <img src="your-image.jpg" alt="Login illustration">
      </div>
      <div class="form-section">
        <form class="login-form">
            <h1 class="login-title">Đăng nhập</h1>
          <label for="username">Username</label>
          <input type="text" id="username" placeholder="Value">

          <label for="password">Password</label>
          <input type="password" id="password" placeholder="Value">

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
