@if ($errors->any())
    <div class="alert alert-danger">
        @foreach ($errors->all() as $error)
            <div>{{ $error }}</div>
        @endforeach
    </div>
@endif

@if(empty($properties))
    <div class="alert alert-danger">
        <strong>Không có dữ liệu</strong>
    </div>
@else
<div class="table-responsive">
    <table>
        <thead>
            <tr>
                @foreach ($columns as $column )

                        <th>{{ $column }}</th>

                @endforeach

                <th>Chức năng</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($properties as $property)
                <tr>
                    @foreach ($columns as $column)
                        @if ($column === 'PropertyType')
                            <td>{{ optional($property->danhMuc)->ten_pro ?? 'No data'}}</td>
                            @elseif ($column === 'OwnerID')
                            <td>{{ optional($property->chusohuu)->Name ?? 'No data'}}</td>
                            @elseif ($column === 'AgentID')
                            <td>{{ optional($property->moigioi)->Name ?? ' '}}</td>
                            @elseif ($column === 'ApprovedBy')
                            <td>{{ optional($property->quantri)->Name ?? ' '}}</td>
                            @elseif ($column === 'PostedDate' || $column === 'ApprovedDate' )
                            <td>{{ $property->$column === '0000-00-00' ? '': $property->$column }}</td>
                            @else
                            <td>{{ $property->$column }}</td>
                        @endif
                    @endforeach
                    <td>
                        <div class="d-flex gap-2">
                            <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#editModal{{ $property->PropertyID }}">
                                <i class="fas fa-edit"></i> Sửa
                            </button>

                            <!-- Nút mở modal -->
                            <button type="button" class="btn btn-danger btn-sm btn-delete" data-bs-toggle="modal" data-bs-target="#deleteModal{{ $property->PropertyID }}">
                                <i class="fas fa-trash"></i> Xóa
                            </button>
                        </div>
                    </td>

                </tr>
                <!-- Modal riêng cho từng Bat dong san -->
                <!-- Modal xóa -->
                <div class="modal fade" id="deleteModal{{ $property->PropertyID }}" tabindex="-1" aria-labelledby="deleteModalLabel{{ $property->Property_ID }}" aria-hidden="true">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="deleteModalLabel{ $property->Property_ID }}">Xác nhận xóa</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                Bạn có chắc chắn muốn xóa bài đăng <strong>{{ $property-> PropertyID }}</strong>
                                ? Hành động này không thể hoàn tác.
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                                <form action="" method="POST" style="display: inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger">Xóa</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Modal chỉnh sửa -->
                <div class="modal fade" id="editModal{{ $property->PropertyID }}" tabindex="-1" aria-labelledby="editModalLabel{{ $property->PropertyID }}" aria-hidden="true">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <form action="" method="POST">
                                @csrf
                                @method('PUT')
                                <div class="modal-header">
                                    <h5 class="modal-title" id="editModalLabel{{ $property->PropertyID }}">Chỉnh sửa Bất Động Sản</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <ul class="nav nav-tabs" id="propertyTab{{ $property->PropertyID }}" role="tablist">
                                        <li class="nav-item" role="presentation">
                                            <button class="nav-link active" id="info-tab{{ $property->PropertyID }}" data-bs-toggle="tab" data-bs-target="#info{{ $property->PropertyID }}" type="button" role="tab">Thông tin bất động sản</button>
                                        </li>
                                        <li class="nav-item" role="presentation">
                                            <button class="nav-link" id="detail-tab{{ $property->PropertyID }}" data-bs-toggle="tab" data-bs-target="#detail{{ $property->PropertyID }}" type="button" role="tab">Chi tiết bất động sản</button>
                                        </li>
                                    </ul>
                                    <div class="tab-content mt-3">
                                        @include('_system.partialview.edit_property', [
                                            'property' => $property,
                                            'owners' => $owners,
                                            'agents' => $agents,
                                            'admins' => $admins,
                                            'categories' => $categories,
                                            'modalId' => $property->PropertyID // truyền id để dùng cho tab-pane
                                        ])
                                    </div>
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
