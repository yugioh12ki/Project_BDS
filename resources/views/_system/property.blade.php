@extends('_layout._layadmin.app')

@section('property')
@if(isset($error))
    <div class="fa fa-danger">
        {{ $error }}
    </div>
@else
<h1>Danh sách Property</h1>

<div class="d-flex justify-content-between align-items-center mb-3">
    {{-- Combobox chọn type --}}
    <div class="d-flex align-items-center">
        <label for="type-select" class="me-2">Loại hình:</label>
        <select id="type-select" class="form-control" style="width: 200px;">
            <option value="all">Sale/Rent</option>
            <option value="Sale">Sale</option>
            <option value="Rent">Rent</option>
        </select>
    </div>

    <div style="margin: 20px"></div>

    {{-- Ô input và button tìm kiếm --}}
    <div class="d-flex align-items-center">
        <form action="#" class="d-flex align-items-center">
            <input type="text" name="keyword" id="search-input" class="form-control" placeholder="Nhập từ khóa tìm kiếm" style="width: 300px;">
            <button type="submit" class="btn btn-secondary ms-2">
                <i class="fa fa-search"></i> Tìm kiếm
            </button>
        </form>
    </div>
</div>

<button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#propertyModal">
    <i class="fa fa-plus"></i> Thêm mới
</button>



<div id="property-list">
    @if(isset($error))
    <div class="alert alert-danger">{{ $error }}</div>
    @else
    @include('_system.partialview.property_table', ['property' => $properties, 'columns' => $columns])
    @endif
</div>


{{-- Modal thêm property --}}
<div class="modal fade" id="propertyModal" tabindex="-1" aria-labelledby="propertyModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <form>
          <div class="modal-header">
            <h5 class="modal-title" id="propertyModalLabel">Thêm Bất Động Sản</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
          </div>
          <div class="modal-body">
            <ul class="nav nav-tabs" id="propertyTab" role="tablist">
              <li class="nav-item" role="presentation">
                <button class="nav-link active" id="info-tab" data-bs-toggle="tab" data-bs-target="#info" type="button" role="tab">Thông tin bất động sản</button>
              </li>
              <li class="nav-item" role="presentation">
                <button class="nav-link" id="detail-tab" data-bs-toggle="tab" data-bs-target="#detail" type="button" role="tab">Chi tiết bất động sản</button>
              </li>
            </ul>
            <div class="tab-content mt-3">
                @include('_system.partialview.create_property', ['owners' => $owners, 'agents' => $agents, 'categories' => $categories])
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
            <button type="submit" class="btn btn-primary">Lưu</button>
          </div>
        </form>
      </div>
    </div>
</div>

<script>
    const typeSelect = document.getElementById('type-select');
    const propertyList = document.getElementById('property-list');

    if (typeSelect && propertyList) {
        typeSelect.addEventListener('change', function () {
            const type = this.value;

            // Gửi yêu cầu AJAX để lấy danh sách property theo role
            fetch(`/admin/property/type/${type}`)
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
</script>


@endif
@endsection
