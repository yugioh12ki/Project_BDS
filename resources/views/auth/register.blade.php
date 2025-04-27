@extends('_layout.app')
@section('register')
<main class="main-content">
    <div class="register-container split-box">
      <div class="image-section">
        <img src="your-image.jpg" alt="register illustration">
      </div>
      <div class="form-section">
        <form class="register-form">
            <h1 class="register-title">Đăng Ký</h1>
          <label for="username">Tên Đăng Nhập</label>
          <input type="text" placeholder="Value" required>

          <label for="password">Mật Khẩu</label>
          <input type="password" placeholder="Value" required>

          <label for="password">Nhập Mật Khẩu lại</label>
          <input type="re-password"  placeholder="Value" required>

          <label for="email">Email</label>
          <input type="email"  placeholder="Value" required>

          <label for="phone">Điện thoại</label>
          <input type="tel"  placeholder="Value" required pattern="[0-9]{10}">

          <label for="cccd">CCCD/CMND</label>
          <input type="text"  placeholder="Value" required pattern="[0-9]{12}">

          <label for="address">Địa Chỉ</label>
          <input type="text" placeholder="Value" required>

          <label for="City">Thành Phố</label>
          <select id="province" required>
            <option value="">Chọn Tỉnh/Thành phố</option>
          </select>

          <label for="district">Quận</label>
          <select id="district" required>
            <option value=""></option>
          </select>

          <label for="ward">Phường/Xã</label>
          <select id="ward" required>
            <option value="">Chọn Phường/Xã</option>
          </select>

          <div class="button-group">
            <button type="button" class="Login" onclick="window.location.href='{{ route('login') }}'">Về Trang Đăng nhập</button>
            <button type="submit" >Đăng ký</button>
          </div>
        </form>
      </div>
    </div>
  </main>

@endsection
