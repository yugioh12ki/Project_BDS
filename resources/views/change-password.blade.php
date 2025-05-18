@extends('_layout.master')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Đổi mật khẩu</div>

                <div class="card-body">
                    <form method="POST" action="{{ route('customer.password.change') }}">
                        @csrf

                        <div class="form-group row mb-3">
                            <label for="current_password" class="col-md-4 col-form-label text-md-right">Mật khẩu hiện tại</label>
                            <div class="col-md-6">
                                <input id="current_password" type="password" 
                                    class="form-control @error('current_password') is-invalid @enderror" 
                                    name="current_password" required>
                                @error('current_password')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row mb-3">
                            <label for="password" class="col-md-4 col-form-label text-md-right">Mật khẩu mới</label>
                            <div class="col-md-6">
                                <input id="password" type="password" 
                                    class="form-control @error('password') is-invalid @enderror" 
                                    name="password" required>
                                @error('password')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row mb-3">
                            <label for="password_confirmation" class="col-md-4 col-form-label text-md-right">
                                Xác nhận mật khẩu mới
                            </label>
                            <div class="col-md-6">
                                <input id="password_confirmation" type="password" class="form-control"
                                    name="password_confirmation" required>
                            </div>
                        </div>

                        <div class="form-group row mb-0">
                            <div class="col-md-6 offset-md-4">
                                <button type="submit" class="btn btn-primary">
                                    Đổi mật khẩu
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 