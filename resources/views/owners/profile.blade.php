@extends('_layout._layowner.app')

@section('content')
<div class="container py-4">
    <div class="row">
        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-body text-center">
                    <div class="avatar-wrapper mb-3">
                        <img src="{{ asset('images/avatars/' . ($user->Avatar ?? 'default-avatar.jpg')) }}" 
                             alt="{{ $user->FullName }}" 
                             class="rounded-circle img-fluid" style="width: 150px; height: 150px; object-fit: cover;">
                    </div>
                    <h5 class="card-title">{{ $user->FullName }}</h5>
                    <p class="text-muted mb-1">Chủ sở hữu</p>
                    <p class="text-muted mb-4">{{ $user->Address }}</p>
                    
                    <div class="d-flex justify-content-center mb-2">
                        <a href="{{ route('owner.change-password') }}" class="btn btn-outline-primary ms-1">
                            <i class="bi bi-key me-1"></i> Đổi mật khẩu
                        </a>
                    </div>
                </div>
            </div>
            
            <div class="card mb-4">
                <div class="card-body">
                    <div class="row">
                        <div class="col-sm-4">
                            <p class="mb-0 text-muted">Tài khoản</p>
                        </div>
                        <div class="col-sm-8">
                            <p class="text-dark mb-0">{{ $user->Username }}</p>
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-sm-4">
                            <p class="mb-0 text-muted">Email</p>
                        </div>
                        <div class="col-sm-8">
                            <p class="text-dark mb-0">{{ $user->Email }}</p>
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-sm-4">
                            <p class="mb-0 text-muted">Điện thoại</p>
                        </div>
                        <div class="col-sm-8">
                            <p class="text-dark mb-0">{{ $user->PhoneNumber }}</p>
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-sm-4">
                            <p class="mb-0 text-muted">Ngày tham gia</p>
                        </div>
                        <div class="col-sm-8">
                            <p class="text-dark mb-0">{{ \Carbon\Carbon::parse($user->created_at)->format('d/m/Y') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Chỉnh sửa thông tin cá nhân</h5>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif
                    
                    <form action="{{ route('owner.profile.update') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        
                        <div class="mb-3">
                            <label for="FullName" class="form-label">Họ tên đầy đủ</label>
                            <input type="text" class="form-control @error('FullName') is-invalid @enderror" 
                                   id="FullName" name="FullName" value="{{ old('FullName', $user->FullName) }}">
                            @error('FullName')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="Email" class="form-label">Email</label>
                            <input type="email" class="form-control @error('Email') is-invalid @enderror" 
                                   id="Email" name="Email" value="{{ old('Email', $user->Email) }}">
                            @error('Email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="PhoneNumber" class="form-label">Số điện thoại</label>
                            <input type="text" class="form-control @error('PhoneNumber') is-invalid @enderror" 
                                   id="PhoneNumber" name="PhoneNumber" value="{{ old('PhoneNumber', $user->PhoneNumber) }}">
                            @error('PhoneNumber')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="Address" class="form-label">Địa chỉ</label>
                            <textarea class="form-control @error('Address') is-invalid @enderror" 
                                      id="Address" name="Address" rows="3">{{ old('Address', $user->Address) }}</textarea>
                            @error('Address')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="Avatar" class="form-label">Ảnh đại diện</label>
                            <input type="file" class="form-control @error('Avatar') is-invalid @enderror" 
                                   id="Avatar" name="Avatar">
                            <small class="form-text text-muted">Chỉ chấp nhận file hình ảnh JPG, JPEG, PNG (tối đa 2MB)</small>
                            @error('Avatar')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <button type="submit" class="btn btn-primary">Cập nhật thông tin</button>
                    </form>
                </div>
            </div>
            
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Thống kê hoạt động</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <div class="p-3 bg-light rounded text-center">
                                <h2 class="mb-0">{{ $user->properties()->count() }}</h2>
                                <p class="mb-0 text-muted">Bất động sản</p>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="p-3 bg-light rounded text-center">
                                <h2 class="mb-0">{{ $user->properties()->where('Status', 'active')->count() }}</h2>
                                <p class="mb-0 text-muted">BĐS đang rao bán</p>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="p-3 bg-light rounded text-center">
                                <h2 class="mb-0">{{ App\Models\Transaction::where('OwnerID', $user->UserID)->count() }}</h2>
                                <p class="mb-0 text-muted">Giao dịch</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 