@extends('_layout._layadmin.app')

@section('property')
@if(isset($error))
    <div class="fa fa-danger">
        {{ $error }}
    </div>
@else
<h1>Danh sách Thuộc Bất Động Sản</h1>

{{-- Thực hiện tab danh sách danh mục | Kiểm duyệt BĐS và Phân công môi giới --}}
<div style="margin: 20px">
    <ul class="nav nav-tabs" id="propertyTabs">
        <li class="nav-item">
            <a class="nav-link active" id="property-list-tab" data-bs-toggle="tab" href="#property-list-content">Kiểm Duyệt Bất Động Sản</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="property-assign-tab" data-bs-toggle="tab" href="#property-assign-content">Phân công môi giới</a>
        </li>
    </ul>
</div>

<div class="tab-content">
    <!-- Tab Kiểm duyệt bất động sản -->
    <div class="tab-pane fade show active" id="property-list-content">
        <div class="d-flex justify-content-between align-items-center mb-3">
            {{-- Combobox chọn type --}}
            <div class="d-flex align-items-center">
                <label for="type-select" class="me-2">Lọc danh sách:</label>
                <select id="type-select" class="form-control" style="width: 200px;">
                    <option value="all">Tất cả</option>
                    <option value="Sale">Sale</option>
                    <option value="Rent">Rent</option>
                    <option value="inactive">Ngừng hoạt động</option>
                    <option value="active">Đã Duyệt</option>
                    <option value="pending">Chờ duyệt</option>
                </select>
            </div>

            <div style="margin: 20px"></div>

            {{-- Ô input và button tìm kiếm --}}
            <div class="d-flex align-items-center">
                <form action="{{ route('admin.property.search') }}" class="d-flex align-items-center">
                    <input type="text" name="keyword" id="search-input" class="form-control" placeholder="Nhập từ khóa tìm kiếm" style="width: 300px;">
                    <button type="submit" class="btn btn-secondary ms-2">
                        <i class="fa fa-search"></i> Tìm kiếm
                    </button>
                </form>
            </div>
        </div>

        <div id="property-list">
            @if(isset($error))
            <div class="alert alert-danger">{{ $error }}</div>
            @else
            @include('_system.partialview.property_table', ['properties' => $properties, 'columns' => $columns])
            @endif
        </div>
    </div>

    <!-- Tab Phân công môi giới -->
    <div class="tab-pane fade" id="property-assign-content">
        <div class="mt-3">
            <!-- Thêm bảng phân công môi giới ở đây -->
            @if(isset($error))
                <div class="alert alert-info">Không có bất động sản cần phân công.</div>
            @else
                @include('_system.partialview.assign_property', ['properties' => $properties, 'columns' => $columns])
            @endif
        </div>
    </div>
</div>




<script>
    const typeSelect = document.getElementById('type-select');
    const propertyList = document.getElementById('property-list');
    const searchInput = document.getElementById('search-input');
    const searchButton = document.getElementById('search-button');


    if (typeSelect && propertyList) {
        typeSelect.addEventListener('change', function () {
            const type = this.value;
            let url = '';
            if (type === 'Sale' || type === 'Rent' || type === 'all') {
                url = `/admin/property/type/${type}`;
            } else
            {
                url = `/admin/property/status/${type}`;
            }
            fetch(url)
            .then(response => {
                if (!response.ok) {
                    throw new Error('Không tìm thấy dữ liệu.');
                }
                return response.text();
            })
            .then(html => {
                propertyList.innerHTML = html;
            })
            .catch(error => {
                console.error('Error:', error);
                propertyList.innerHTML = `
                    <div class="alert alert-danger">Không tìm thấy property nào.</div>
                `;
            });
        });
    }

    // Xử lý sự kiện khi người dùng nhập từ khóa tìm kiếm
    searchButton.addEventListener('click', function (event) {
        const keyword = searchInput.value;

        fetch(`/admin/property/search?keyword=${encodeURIComponent(keyword)}`)
            .then(response => {
            if (!response.ok) {
                throw new Error('Không tìm thấy dữ liệu.');
            }
            return response.text();
            })
            .then(html => {
                propertyList.innerHTML = html;
            })
            .catch(error => {
                console.error('Error:', error);
                propertyList.innerHTML = `
                    <div class="alert alert-danger">Không tìm thấy property nào.</div>
                `;
            });

        });



</script>


@endif
@endsection
