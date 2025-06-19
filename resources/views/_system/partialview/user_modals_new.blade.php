@if(isset($user))
<!-- Modal Xem Chi Tiết -->
<div class="modal fade" id="viewModal{{ $user->UserID }}" tabindex="-1" aria-labelledby="viewModalLabel{{ $user->UserID }}" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="viewModalLabel{{ $user->UserID }}">
                    <i class="fas fa-user me-2"></i>
                    Chi tiết thông tin {{ $user->Name }}
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                {{-- Tab Navigation --}}
                <ul class="nav nav-tabs" id="profileTabs{{ $user->UserID }}" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="general-tab{{ $user->UserID }}" data-bs-toggle="tab"
                                data-bs-target="#general{{ $user->UserID }}" type="button" role="tab">
                            <i class="fas fa-user me-2"></i>Thông tin chung
                        </button>
                    </li>
                    @if($user->Role === 'Admin')
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="admin-tab{{ $user->UserID }}" data-bs-toggle="tab"
                                data-bs-target="#admin{{ $user->UserID }}" type="button" role="tab">
                            <i class="fas fa-user-shield me-2"></i>Profile Admin
                        </button>
                    </li>
                    @elseif($user->Role === 'Agent')
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="agent-tab{{ $user->UserID }}" data-bs-toggle="tab"
                                data-bs-target="#agent{{ $user->UserID }}" type="button" role="tab">
                            <i class="fas fa-user-tie me-2"></i>Profile Agent
                        </button>
                    </li>
                    @elseif($user->Role === 'Owner')
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="owner-tab{{ $user->UserID }}" data-bs-toggle="tab"
                                data-bs-target="#owner{{ $user->UserID }}" type="button" role="tab">
                            <i class="fas fa-home me-2"></i>Profile Owner
                        </button>
                    </li>
                    @elseif($user->Role === 'Customer')
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="customer-tab{{ $user->UserID }}" data-bs-toggle="tab"
                                data-bs-target="#customer{{ $user->UserID }}" type="button" role="tab">
                            <i class="fas fa-user-tag me-2"></i>Profile Customer
                        </button>
                    </li>
                    @endif
                </ul>

                {{-- Tab Content --}}
                <div class="tab-content mt-3" id="profileTabsContent{{ $user->UserID }}">
                    {{-- Thông tin chung --}}
                    <div class="tab-pane fade show active" id="general{{ $user->UserID }}" role="tabpanel">
                        <div class="row">
                            <div class="col-md-3 text-center">
                                @if($user->Avatar)
                                    <img src="{{ asset('storage/avatars/' . $user->Avatar) }}" alt="Avatar" class="rounded-circle mb-3" width="100" height="100">
                                @else
                                    <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3" style="width: 100px; height: 100px;">
                                        <span class="text-white fw-bold fs-1">{{ strtoupper(substr($user->Name, 0, 1)) }}</span>
                                    </div>
                                @endif
                                <span class="badge bg-{{ $user->Role === 'Admin' ? 'danger' : ($user->Role === 'Agent' ? 'primary' : ($user->Role === 'Owner' ? 'success' : 'secondary')) }} fs-6">
                                    {{ $user->Role }}
                                </span>
                            </div>
                            <div class="col-md-9">
                                <table class="table table-borderless">
                                    <tr>
                                        <td class="fw-bold"><i class="fas fa-id-card text-primary me-2"></i>User ID:</td>
                                        <td><code>{{ $user->UserID }}</code></td>
                                    </tr>
                                    <tr>
                                        <td class="fw-bold"><i class="fas fa-user text-primary me-2"></i>Họ tên:</td>
                                        <td>{{ $user->Name }}</td>
                                    </tr>
                                    <tr>
                                        <td class="fw-bold"><i class="fas fa-envelope text-primary me-2"></i>Email:</td>
                                        <td>{{ $user->Email }}</td>
                                    </tr>
                                    <tr>
                                        <td class="fw-bold"><i class="fas fa-phone text-primary me-2"></i>Điện thoại:</td>
                                        <td>{{ $user->Phone ?? 'Chưa có' }}</td>
                                    </tr>
                                    <tr>
                                        <td class="fw-bold"><i class="fas fa-birthday-cake text-primary me-2"></i>Ngày sinh:</td>
                                        <td>{{ $user->Birth ?? 'Chưa có' }}</td>
                                    </tr>
                                    <tr>
                                        <td class="fw-bold"><i class="fas fa-venus-mars text-primary me-2"></i>Giới tính:</td>
                                        <td>{{ $user->Sex ?? 'Chưa có' }}</td>
                                    </tr>
                                    <tr>
                                        <td class="fw-bold"><i class="fas fa-id-card-alt text-primary me-2"></i>CCCD:</td>
                                        <td>{{ $user->IdentityCard ?? 'Chưa có' }}</td>
                                    </tr>
                                    <tr>
                                        <td class="fw-bold"><i class="fas fa-map-marker-alt text-primary me-2"></i>Địa chỉ:</td>
                                        <td>
                                            @if($user->Address)
                                                {{ $user->Address }}
                                                @if($user->Ward), {{ $user->Ward }}@endif
                                                @if($user->District), {{ $user->District }}@endif
                                                @if($user->Province), {{ $user->Province }}@endif
                                            @else
                                                Chưa có
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="fw-bold"><i class="fas fa-toggle-on text-primary me-2"></i>Trạng thái:</td>
                                        <td>
                                            <span class="badge bg-{{ strtolower($user->StatusUser) === 'active' ? 'success' : 'warning' }}">
                                                {{ $user->StatusUser === 'active' ? 'Đang hoạt động' : 'Ngừng hoạt động' }}
                                            </span>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>

                    @if($user->Role === 'Admin')
                    {{-- Profile Admin --}}
                    <div class="tab-pane fade" id="admin{{ $user->UserID }}" role="tabpanel">
                        @php
                            $adminProfile = DB::table('profile_admin')->where('UserID', $user->UserID)->first();
                        @endphp
                        <div class="card border-danger">
                            <div class="card-header bg-danger text-white">
                                <i class="fas fa-user-shield me-2"></i>Thông tin Admin
                            </div>
                            <div class="card-body">
                                @if($adminProfile)
                                    <table class="table table-borderless">
                                        <tr>
                                            <td class="fw-bold"><i class="fas fa-crown text-danger me-2"></i>Chức vụ:</td>
                                            <td><span class="badge bg-danger">{{ $adminProfile->TenChucVu }}</span></td>
                                        </tr>
                                    </table>
                                @else
                                    <div class="text-center text-muted">
                                        <i class="fas fa-exclamation-triangle fa-3x mb-3"></i>
                                        <h5>Chưa có thông tin profile Admin</h5>
                                        <p>Vui lòng cập nhật thông tin profile cho user này.</p>
                                        <button class="btn btn-primary" onclick="createAdminProfile('{{ $user->UserID }}')">
                                            <i class="fas fa-plus me-2"></i>Tạo Profile Admin
                                        </button>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    @elseif($user->Role === 'Agent')
                    {{-- Profile Agent --}}
                    <div class="tab-pane fade" id="agent{{ $user->UserID }}" role="tabpanel">
                        @php
                            $agentProfile = DB::table('profile_agent')->where('UserID', $user->UserID)->first();
                        @endphp
                        <div class="card border-primary">
                            <div class="card-header bg-primary text-white">
                                <i class="fas fa-user-tie me-2"></i>Thông tin Agent
                            </div>
                            <div class="card-body">
                                @if($agentProfile)
                                    <table class="table table-borderless">
                                        <tr>
                                            <td class="fw-bold"><i class="fas fa-certificate text-primary me-2"></i>Chứng chỉ:</td>
                                            <td>{{ $agentProfile->Certificate ?? 'Chưa có' }}</td>
                                        </tr>
                                        <tr>
                                            <td class="fw-bold"><i class="fas fa-map-marked-alt text-primary me-2"></i>Khu vực hoạt động:</td>
                                            <td>
                                                {{ $agentProfile->ProvinceAgent }}
                                                @if($agentProfile->DistrictAgent), {{ $agentProfile->DistrictAgent }}@endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="fw-bold"><i class="fas fa-phone text-primary me-2"></i>Liên hệ công việc:</td>
                                            <td>{{ $agentProfile->ContactAgent ?? 'Chưa có' }}</td>
                                        </tr>
                                        <tr>
                                            <td class="fw-bold"><i class="fas fa-id-card text-primary me-2"></i>Số thẻ Agent:</td>
                                            <td>{{ $agentProfile->NumberCardAgent ?? 'Chưa có' }}</td>
                                        </tr>
                                    </table>
                                @else
                                    <div class="text-center text-muted">
                                        <i class="fas fa-exclamation-triangle fa-3x mb-3"></i>
                                        <h5>Chưa có thông tin profile Agent</h5>
                                        <p>Vui lòng cập nhật thông tin profile cho user này.</p>
                                        <button class="btn btn-primary" onclick="createAgentProfile('{{ $user->UserID }}')">
                                            <i class="fas fa-plus me-2"></i>Tạo Profile Agent
                                        </button>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    @elseif($user->Role === 'Owner')
                    {{-- Profile Owner --}}
                    <div class="tab-pane fade" id="owner{{ $user->UserID }}" role="tabpanel">
                        @php
                            $ownerProfile = DB::table('profile_owner')->where('UserID', $user->UserID)->first();
                        @endphp
                        <div class="card border-success">
                            <div class="card-header bg-success text-white">
                                <i class="fas fa-home me-2"></i>Thông tin Owner
                            </div>
                            <div class="card-body">
                                @if($ownerProfile)
                                    <table class="table table-borderless">
                                        <tr>
                                            <td class="fw-bold"><i class="fas fa-phone text-success me-2"></i>Liên hệ Owner:</td>
                                            <td>{{ $ownerProfile->ContactOwner ?? 'Chưa có' }}</td>
                                        </tr>
                                        <tr>
                                            <td class="fw-bold"><i class="fas fa-id-card text-success me-2"></i>Số thẻ Owner:</td>
                                            <td>{{ $ownerProfile->NumberCardOwner ?? 'Chưa có' }}</td>
                                        </tr>
                                        <tr>
                                            <td class="fw-bold"><i class="fas fa-file-alt text-success me-2"></i>Giấy tờ:</td>
                                            <td>{{ $ownerProfile->GiayTo ?? 'Chưa có' }}</td>
                                        </tr>
                                    </table>
                                @else
                                    <div class="text-center text-muted">
                                        <i class="fas fa-exclamation-triangle fa-3x mb-3"></i>
                                        <h5>Chưa có thông tin profile Owner</h5>
                                        <p>Vui lòng cập nhật thông tin profile cho user này.</p>
                                        <button class="btn btn-success" onclick="createOwnerProfile('{{ $user->UserID }}')">
                                            <i class="fas fa-plus me-2"></i>Tạo Profile Owner
                                        </button>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    @elseif($user->Role === 'Customer')
                    {{-- Profile Customer --}}
                    <div class="tab-pane fade" id="customer{{ $user->UserID }}" role="tabpanel">
                        @php
                            $customerProfile = DB::table('profile_customer')->where('UserID', $user->UserID)->first();
                        @endphp
                        <div class="card border-secondary">
                            <div class="card-header bg-secondary text-white">
                                <i class="fas fa-user-tag me-2"></i>Thông tin Customer
                            </div>
                            <div class="card-body">
                                @if($customerProfile)
                                    <table class="table table-borderless">
                                        <tr>
                                            <td class="fw-bold"><i class="fas fa-heart text-secondary me-2"></i>Danh sách yêu thích:</td>
                                            <td>{{ $customerProfile->Whitelist ?? 'Chưa có' }}</td>
                                        </tr>
                                        <tr>
                                            <td class="fw-bold"><i class="fas fa-home text-secondary me-2"></i>Loại BĐS ưa thích:</td>
                                            <td>{{ $customerProfile->PreferredPropertyType ?? 'Chưa có' }}</td>
                                        </tr>
                                    </table>
                                @else
                                    <div class="text-center text-muted">
                                        <i class="fas fa-exclamation-triangle fa-3x mb-3"></i>
                                        <h5>Chưa có thông tin profile Customer</h5>
                                        <p>Vui lòng cập nhật thông tin profile cho user này.</p>
                                        <button class="btn btn-secondary" onclick="createCustomerProfile('{{ $user->UserID }}')">
                                            <i class="fas fa-plus me-2"></i>Tạo Profile Customer
                                        </button>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-2"></i>Đóng
                </button>
                <button type="button" class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#editModal{{ $user->UserID }}" data-bs-dismiss="modal">
                    <i class="fas fa-edit me-2"></i>Chỉnh sửa
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Chỉnh Sửa -->
<div class="modal fade" id="editModal{{ $user->UserID }}" tabindex="-1" aria-labelledby="editModalLabel{{ $user->UserID }}" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header bg-warning text-white">
                <h5 class="modal-title" id="editModalLabel{{ $user->UserID }}">
                    <i class="fas fa-edit me-2"></i>
                    Chỉnh sửa thông tin {{ $user->Name }}
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                {{-- Tab Navigation cho Edit --}}
                <ul class="nav nav-tabs" id="editTabs{{ $user->UserID }}" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="edit-general-tab{{ $user->UserID }}" data-bs-toggle="tab"
                                data-bs-target="#edit-general{{ $user->UserID }}" type="button" role="tab">
                            <i class="fas fa-user me-2"></i>Thông tin chung
                        </button>
                    </li>
                    @if($user->Role === 'Admin')
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="edit-admin-tab{{ $user->UserID }}" data-bs-toggle="tab"
                                data-bs-target="#edit-admin{{ $user->UserID }}" type="button" role="tab">
                            <i class="fas fa-user-shield me-2"></i>Profile Admin
                        </button>
                    </li>
                    @elseif($user->Role === 'Agent')
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="edit-agent-tab{{ $user->UserID }}" data-bs-toggle="tab"
                                data-bs-target="#edit-agent{{ $user->UserID }}" type="button" role="tab">
                            <i class="fas fa-user-tie me-2"></i>Profile Agent
                        </button>
                    </li>
                    @elseif($user->Role === 'Owner')
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="edit-owner-tab{{ $user->UserID }}" data-bs-toggle="tab"
                                data-bs-target="#edit-owner{{ $user->UserID }}" type="button" role="tab">
                            <i class="fas fa-home me-2"></i>Profile Owner
                        </button>
                    </li>
                    @elseif($user->Role === 'Customer')
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="edit-customer-tab{{ $user->UserID }}" data-bs-toggle="tab"
                                data-bs-target="#edit-customer{{ $user->UserID }}" type="button" role="tab">
                            <i class="fas fa-user-tag me-2"></i>Profile Customer
                        </button>
                    </li>
                    @endif
                </ul>

                {{-- Form Content --}}
                <form action="{{ route('admin.users.updateWithProfile', $user->UserID) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="tab-content mt-3" id="editTabsContent{{ $user->UserID }}">
                        {{-- Edit Thông tin chung --}}
                        <div class="tab-pane fade show active" id="edit-general{{ $user->UserID }}" role="tabpanel">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">
                                            <i class="fas fa-user text-primary me-2"></i>Họ tên
                                        </label>
                                        <input type="text" class="form-control" name="name" value="{{ $user->Name }}" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">
                                            <i class="fas fa-envelope text-primary me-2"></i>Email
                                        </label>
                                        <input type="email" class="form-control" name="email" value="{{ $user->Email }}" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">
                                            <i class="fas fa-phone text-primary me-2"></i>Điện thoại
                                        </label>
                                        <input type="text" class="form-control" name="phone" value="{{ $user->Phone }}">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">
                                            <i class="fas fa-birthday-cake text-primary me-2"></i>Ngày sinh
                                        </label>
                                        <input type="date" class="form-control" name="birth" value="{{ $user->Birth }}">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">
                                            <i class="fas fa-venus-mars text-primary me-2"></i>Giới tính
                                        </label>
                                        <select class="form-select" name="sex">
                                            <option value="Nam" {{ $user->Sex === 'Nam' ? 'selected' : '' }}>Nam</option>
                                            <option value="Nữ" {{ $user->Sex === 'Nữ' ? 'selected' : '' }}>Nữ</option>
                                            <option value="Khác" {{ $user->Sex === 'Khác' ? 'selected' : '' }}>Khác</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">
                                            <i class="fas fa-id-card-alt text-primary me-2"></i>CCCD/CMND
                                        </label>
                                        <input type="text" class="form-control" name="identity_card" value="{{ $user->IdentityCard }}">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">
                                            <i class="fas fa-map-marker-alt text-primary me-2"></i>Địa chỉ
                                        </label>
                                        <input type="text" class="form-control" name="address" value="{{ $user->Address }}">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">
                                            <i class="fas fa-map text-primary me-2"></i>Tỉnh/Thành phố
                                        </label>
                                        <select class="form-select province-select" name="province" data-selected="{{ $user->Province }}">
                                            <option value="">Chọn tỉnh/thành phố</option>
                                            @if(!empty($user->Province))
                                                <option value="{{ $user->Province }}" selected>{{ $user->Province }}</option>
                                            @endif
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">
                                            <i class="fas fa-map-marked text-primary me-2"></i>Quận/Huyện
                                        </label>
                                        <select class="form-select district-select" name="district" data-selected="{{ $user->District }}">
                                            <option value="">Chọn quận/huyện</option>
                                            @if(!empty($user->District))
                                                <option value="{{ $user->District }}" selected>{{ $user->District }}</option>
                                            @endif
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">
                                            <i class="fas fa-map-pin text-primary me-2"></i>Phường/Xã
                                        </label>
                                        <select class="form-select ward-select" name="ward" data-selected="{{ $user->Ward }}">
                                            <option value="">Chọn phường/xã</option>
                                            @if(!empty($user->Ward))
                                                <option value="{{ $user->Ward }}" selected>{{ $user->Ward }}</option>
                                            @endif
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">
                                            <i class="fas fa-user-tag text-primary me-2"></i>Role
                                        </label>
                                        <select class="form-select" name="role" required>
                                            <option value="Admin" {{ $user->Role === 'Admin' ? 'selected' : '' }}>Admin</option>
                                            <option value="Agent" {{ $user->Role === 'Agent' ? 'selected' : '' }}>Agent</option>
                                            <option value="Owner" {{ $user->Role === 'Owner' ? 'selected' : '' }}>Owner</option>
                                            <option value="Customer" {{ $user->Role === 'Customer' ? 'selected' : '' }}>Customer</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">
                                            <i class="fas fa-toggle-on text-primary me-2"></i>Trạng thái
                                        </label>
                                        <select class="form-select" name="status" required>
                                            <option value="active" {{ strtolower($user->StatusUser) === 'active' ? 'selected' : '' }}>Hoạt động</option>
                                            <option value="inactive" {{ strtolower($user->StatusUser) === 'inactive' ? 'selected' : '' }}>Ngừng hoạt động</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">
                                    <i class="fas fa-lock text-primary me-2"></i>Mật khẩu mới
                                </label>
                                <input type="password" class="form-control" name="password" placeholder="Để trống nếu không muốn đổi mật khẩu">
                                <div class="form-text">Để trống nếu không muốn thay đổi mật khẩu hiện tại</div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">
                                    <i class="fas fa-image text-primary me-2"></i>Avatar
                                </label>
                                <input type="file" class="form-control" name="avatar" accept="image/*">
                                <div class="form-text">Chấp nhận file ảnh: JPG, PNG, GIF (tối đa 2MB)</div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">
                                    <i class="fas fa-toggle-on text-primary me-2"></i>Trạng thái
                                </label>
                                <select class="form-select" name="status" required>
                                    <option value="active" {{ $user->StatusUser == 'active' ? 'selected' : '' }}>
                                        <i class="fas fa-check-circle text-success"></i> Hoạt động
                                    </option>
                                    <option value="inactive" {{ $user->StatusUser == 'inactive' ? 'selected' : '' }}>
                                        <i class="fas fa-times-circle text-warning"></i> Không hoạt động
                                    </option>
                                </select>
                            </div>
                        </div>

                        {{-- Edit Profile theo Role --}}
                        @if($user->Role === 'Admin')
                        <div class="tab-pane fade" id="edit-admin{{ $user->UserID }}" role="tabpanel">
                            @php
                                $adminProfile = DB::table('profile_admin')->where('UserID', $user->UserID)->first();
                            @endphp
                            <div class="card border-danger">
                                <div class="card-header bg-danger text-white">
                                    <i class="fas fa-user-shield me-2"></i>Chỉnh sửa Profile Admin
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">
                                            <i class="fas fa-crown text-danger me-2"></i>Chức vụ
                                        </label>
                                        <select class="form-select" name="admin_chuc_vu">
                                            <option value="Nhân viên" {{ ($adminProfile->TenChucVu ?? '') === 'Nhân viên' ? 'selected' : '' }}>Nhân viên</option>
                                            <option value="Quản trị viên" {{ ($adminProfile->TenChucVu ?? '') === 'Quản trị viên' ? 'selected' : '' }}>Quản trị viên</option>
                                            <option value="Giám đốc" {{ ($adminProfile->TenChucVu ?? '') === 'Giám đốc' ? 'selected' : '' }}>Giám đốc</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @elseif($user->Role === 'Agent')
                        <div class="tab-pane fade" id="edit-agent{{ $user->UserID }}" role="tabpanel">
                            @php
                                $agentProfile = DB::table('profile_agent')->where('UserID', $user->UserID)->first();
                            @endphp
                            <div class="card border-primary">
                                <div class="card-header bg-primary text-white">
                                    <i class="fas fa-user-tie me-2"></i>Chỉnh sửa Profile Agent
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label fw-bold">
                                                    <i class="fas fa-certificate text-primary me-2"></i>Chứng chỉ
                                                </label>
                                                <textarea class="form-control" name="agent_certificate" rows="3">{{ $agentProfile->Certificate ?? '' }}</textarea>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label fw-bold">
                                                    <i class="fas fa-map text-primary me-2"></i>Tỉnh hoạt động
                                                </label>
                                                <input type="text" class="form-control" name="agent_province" value="{{ $agentProfile->ProvinceAgent ?? '' }}">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label fw-bold">
                                                    <i class="fas fa-map-marked text-primary me-2"></i>Quận/Huyện hoạt động
                                                </label>
                                                <input type="text" class="form-control" name="agent_district" value="{{ $agentProfile->DistrictAgent ?? '' }}">
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label fw-bold">
                                                    <i class="fas fa-phone text-primary me-2"></i>Liên hệ công việc
                                                </label>
                                                <input type="text" class="form-control" name="agent_contact" value="{{ $agentProfile->ContactAgent ?? '' }}">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">
                                            <i class="fas fa-id-card text-primary me-2"></i>Số thẻ Agent
                                        </label>
                                        <input type="text" class="form-control" name="agent_card_number" value="{{ $agentProfile->NumberCardAgent ?? '' }}">
                                    </div>
                                </div>
                            </div>
                        </div>
                        @elseif($user->Role === 'Owner')
                        <div class="tab-pane fade" id="edit-owner{{ $user->UserID }}" role="tabpanel">
                            @php
                                $ownerProfile = DB::table('profile_owner')->where('UserID', $user->UserID)->first();
                            @endphp
                            <div class="card border-success">
                                <div class="card-header bg-success text-white">
                                    <i class="fas fa-home me-2"></i>Chỉnh sửa Profile Owner
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label fw-bold">
                                                    <i class="fas fa-phone text-success me-2"></i>Liên hệ Owner
                                                </label>
                                                <input type="text" class="form-control" name="owner_contact" value="{{ $ownerProfile->ContactOwner ?? '' }}">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label fw-bold">
                                                    <i class="fas fa-id-card text-success me-2"></i>Số thẻ Owner
                                                </label>
                                                <input type="text" class="form-control" name="owner_card_number" value="{{ $ownerProfile->NumberCardOwner ?? '' }}">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">
                                            <i class="fas fa-file-alt text-success me-2"></i>Giấy tờ
                                        </label>
                                        <textarea class="form-control" name="owner_giay_to" rows="3">{{ $ownerProfile->GiayTo ?? '' }}</textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @elseif($user->Role === 'Customer')
                        <div class="tab-pane fade" id="edit-customer{{ $user->UserID }}" role="tabpanel">
                            @php
                                $customerProfile = DB::table('profile_customer')->where('UserID', $user->UserID)->first();
                            @endphp
                            <div class="card border-secondary">
                                <div class="card-header bg-secondary text-white">
                                    <i class="fas fa-user-tag me-2"></i>Chỉnh sửa Profile Customer
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">
                                            <i class="fas fa-heart text-secondary me-2"></i>Danh sách yêu thích
                                        </label>
                                        <textarea class="form-control" name="customer_whitelist" rows="3">{{ $customerProfile->Whitelist ?? '' }}</textarea>
                                        <div class="form-text">Danh sách các BĐS yêu thích của khách hàng</div>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">
                                            <i class="fas fa-home text-secondary me-2"></i>Loại BĐS ưa thích
                                        </label>
                                        <select class="form-select" name="customer_preferred_type">
                                            <option value="">Chọn loại BĐS</option>
                                            <option value="Nhà ở" {{ ($customerProfile->PreferredPropertyType ?? '') === 'Nhà ở' ? 'selected' : '' }}>Nhà ở</option>
                                            <option value="Chung cư" {{ ($customerProfile->PreferredPropertyType ?? '') === 'Chung cư' ? 'selected' : '' }}>Chung cư</option>
                                            <option value="Đất nền" {{ ($customerProfile->PreferredPropertyType ?? '') === 'Đất nền' ? 'selected' : '' }}>Đất nền</option>
                                            <option value="Biệt thự" {{ ($customerProfile->PreferredPropertyType ?? '') === 'Biệt thự' ? 'selected' : '' }}>Biệt thự</option>
                                            <option value="Nhà phố" {{ ($customerProfile->PreferredPropertyType ?? '') === 'Nhà phố' ? 'selected' : '' }}>Nhà phố</option>
                                            <option value="Văn phòng" {{ ($customerProfile->PreferredPropertyType ?? '') === 'Văn phòng' ? 'selected' : '' }}>Văn phòng</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="fas fa-times me-2"></i>Hủy
                        </button>
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-save me-2"></i>Lưu thay đổi
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal Xóa -->
<div class="modal fade" id="deleteModal{{ $user->UserID }}" tabindex="-1" aria-labelledby="deleteModalLabel{{ $user->UserID }}" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="deleteModalLabel{{ $user->UserID }}">
                    <i class="fas fa-exclamation-triangle me-2"></i>Xác nhận xóa
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="text-center">
                    <i class="fas fa-user-times fa-4x text-danger mb-3"></i>
                    <h5>Bạn có chắc chắn muốn vô hiệu hóa user này?</h5>
                    <p class="text-muted">
                        User: <strong>{{ $user->Name }}</strong> ({{ $user->Email }})<br>
                        Hành động này sẽ chuyển user sang trạng thái "Ngừng hoạt động".
                    </p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-2"></i>Hủy
                </button>
                <form action="{{ route('admin.users.delete', $user->UserID) }}" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-user-times me-2"></i>Vô hiệu hóa
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
// JavaScript functions for profile management
function activateUser(userId) {
    if (confirm('Bạn có chắc muốn kích hoạt lại user này?')) {
        // AJAX call to activate user
        fetch(`/admin/users/${userId}/activate`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
            },
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Có lỗi xảy ra: ' + data.message);
            }
        });
    }
}

function createAdminProfile(userId) {
    // Redirect to create admin profile
    window.location.href = `/admin/users/${userId}/create-admin-profile`;
}

function createAgentProfile(userId) {
    // Redirect to create agent profile
    window.location.href = `/admin/users/${userId}/create-agent-profile`;
}

function createOwnerProfile(userId) {
    // Redirect to create owner profile
    window.location.href = `/admin/users/${userId}/create-owner-profile`;
}

function createCustomerProfile(userId) {
    // Redirect to create customer profile
    window.location.href = `/admin/users/${userId}/create-customer-profile`;
}
</script>

@endif
