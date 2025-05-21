@extends('_layout._layowner.app')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Đổi mật khẩu</h5>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif
                    
                    @if($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    
                    <form action="{{ route('owner.change-password.update') }}" method="POST">
                        @csrf
                        
                        <div class="mb-3">
                            <label for="current_password" class="form-label">Mật khẩu hiện tại</label>
                            <input type="password" class="form-control @error('current_password') is-invalid @enderror" 
                                   id="current_password" name="current_password">
                            @error('current_password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="password" class="form-label">Mật khẩu mới</label>
                            <input type="password" class="form-control @error('password') is-invalid @enderror" 
                                   id="password" name="password">
                            <small class="form-text text-muted">Mật khẩu phải có ít nhất 8 ký tự</small>
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="password_confirmation" class="form-label">Xác nhận mật khẩu mới</label>
                            <input type="password" class="form-control" 
                                   id="password_confirmation" name="password_confirmation">
                        </div>
                        
                        <div class="d-flex">
                            <button type="submit" class="btn btn-primary me-2">Cập nhật mật khẩu</button>
                            <a href="{{ route('owner.profile') }}" class="btn btn-outline-secondary">Quay lại</a>
                        </div>
                    </form>
                </div>
            </div>
            
            <div class="card mt-4">
                <div class="card-header">
                    <h5 class="mb-0">Lưu ý về bảo mật</h5>
                </div>
                <div class="card-body">
                    <ul class="mb-0">
                        <li>Sử dụng mật khẩu mạnh và khác biệt cho mỗi tài khoản.</li>
                        <li>Mật khẩu nên bao gồm chữ hoa, chữ thường, số và ký tự đặc biệt.</li>
                        <li>Không chia sẻ mật khẩu của bạn với người khác.</li>
                        <li>Thay đổi mật khẩu của bạn định kỳ, ít nhất mỗi 3 tháng một lần.</li>
                        <li>Không sử dụng thông tin cá nhân (ngày sinh, tên) trong mật khẩu.</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 