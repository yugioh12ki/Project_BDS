@if ($errors->any())
    <div class="alert alert-danger">
        @foreach ($errors->all() as $error)
            <div>{{ $error }}</div>
        @endforeach
    </div>
@endif

<div class="row" style="margin-top: 50px;">
    <div class="col-md-6">
        <h5 class="mb-2">Danh sách hoạt động</h5>
        @php
            $activeUsers = $users->filter(fn($u) => strtolower($u->StatusUser) === 'active');
        @endphp
        <div class="table-responsive">
            <table class="table table-bordered table-hover table-striped align-middle">
                <thead class="table-primary">
                    <tr>
                        <th>UserID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th style="width:110px">Chức năng</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($activeUsers as $user)
                        <tr>
                            <td>{{ $user->UserID }}</td>
                            <td>{{ $user->Name }}</td>
                            <td>{{ $user->Email }}</td>
                            <td>{{ $user->Phone }}</td>
                            <td>
                                <div class="d-flex gap-2">
                                    <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#editModal{{ $user->UserID }}">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button type="button" class="btn btn-danger btn-sm btn-delete" data-bs-toggle="modal" data-bs-target="#deleteModal{{ $user->UserID }}">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                                @include('_system.partialview.user_modals', ['user' => $user])
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-center">Không có user hoạt động.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="col-md-6">
        <h5 class="mb-2">Danh sách ngừng hoạt động</h5>
        @php
            $inactiveUsers = $users->filter(fn($u) => strtolower($u->StatusUser) === 'inactive');
        @endphp
        <div class="table-responsive">
            <table class="table table-bordered table-hover table-striped align-middle">
                <thead class="table-secondary">
                    <tr>
                        <th>UserID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th style="width:110px">Chức năng</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($inactiveUsers as $user)
                        <tr>
                            <td>{{ $user->UserID }}</td>
                            <td>{{ $user->Name }}</td>
                            <td>{{ $user->Email }}</td>
                            <td>{{ $user->Phone }}</td>
                            <td>
                                <div class="d-flex gap-2">
                                    <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#editModal{{ $user->UserID }}">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button type="button" class="btn btn-danger btn-sm btn-delete" data-bs-toggle="modal" data-bs-target="#deleteModal{{ $user->UserID }}">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                                @include('_system.partialview.user_modals', ['user' => $user])
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-center">Không có user ngừng hoạt động.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Phân trang --}}
@if ($users instanceof \Illuminate\Pagination\LengthAwarePaginator)
    <div class="pagination-wrapper mt-3">
        {{ $users->appends(request()->except('page'))->links() }}
    </div>
@endif


