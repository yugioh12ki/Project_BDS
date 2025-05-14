@if ($errors->any())
    <div class="alert alert-danger">
        @foreach ($errors->all() as $error)
            <div>{{ $error }}</div>
        @endforeach
    </div>
@endif

@if (empty($users))
    <div class="alert alert-danger">
        Không tìm thấy user nào.
    </div>
@else
    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    @foreach($columns as $column)
                        <th>{{ $column }}</th>

                    @endforeach
                    <th>Chức năng</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($users as $user)
                    <tr>
                        @foreach ($columns as $column)

                            <td>{{ $user->$column }}</td>
                        @endforeach
                        <td>
                            <div class="d-flex gap-2">
                                <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#editModal{{ $user->UserID }}">
                                    <i class="fas fa-edit"></i> Sửa
                                </button>

                                <!-- Nút mở modal -->
                                <button type="button" class="btn btn-danger btn-sm btn-delete" data-bs-toggle="modal" data-bs-target="#deleteModal{{ $user->UserID }}">
                                    <i class="fas fa-trash"></i> Xóa
                                </button>
                            </div>
                        </td>
                    </tr>

                    <!-- Modal riêng cho từng người dùng -->
                    <div class="modal fade" id="deleteModal{{ $user->UserID }}" tabindex="-1" aria-labelledby="deleteModalLabel{{ $user->UserID }}" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="deleteModalLabel{{ $user->UserID }}">Xác nhận xóa</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    Bạn có chắc chắn muốn xóa người dùng <strong>{{ $user->Name }}</strong>? Hành động này không thể hoàn tác.
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                                    <form action="{{ route('admin.users.delete', $user->UserID) }}" method="POST" style="display: inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger">Xóa</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Modal chỉnh sửa -->
                    <div class="modal fade" id="editModal{{ $user->UserID }}" tabindex="-1" aria-labelledby="editModalLabel{{ $user->UserID }}" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="editModalLabel{{ $user->UserID }}">Chỉnh sửa người dùng</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <form action="{{ route('admin.users.edit', $user->UserID) }}" method="POST">
                                    @csrf
                                    @method('PUT')
                                    <div class="modal-body">
                                        @include('_system.partialview.edit_user', ['user' => $user])
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                                        <button type="submit" class="btn btn-primary">Lưu thay đổi</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                @endforeach
            </tbody>
        </table>
    </div>
@endif

@if (session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if ($errors->any())
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        @foreach ($errors->all() as $error)
            <div>{{ $error }}</div>
        @endforeach
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif


