@extends('_layout._layowner.app')

@section('content')
<h2>Danh sách bất động sản của bạn</h2>

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

<button type="button" class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#addPropertyModal">
    <i class="bi bi-plus-circle"></i> Thêm Bất Động Sản
</button>

<div class="container-fluid">
    <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
        @forelse($properties as $property)
            <div class="col">
                <div class="card h-100 shadow-sm border">
                    <div class="card-body">
                        <h5 class="card-title">{{ $property->Title }}</h5>
                        <h6 class="card-subtitle mb-2 text-muted">
                            {{ $property->Province }} - {{ $property->District }}, {{ $property->Ward }}
                        </h6>
                        <div class="mb-2">
                            <span class="badge bg-info text-dark">{{ $property->danhMuc->ten_pro ?? '' }}</span>
                            <span class="badge bg-secondary">
                                @if($property->TypePro == 'Rent')
                                    Cho thuê
                                @elseif($property->TypePro == 'Sale')
                                    Bán
                                @elseif($property->TypePro == 'Rented')
                                    Đã cho thuê
                                @elseif($property->TypePro == 'Sold')
                                    Đã bán
                                @else
                                    {{ $property->TypePro }}
                                @endif
                            </span>
                            <span class="badge bg-warning text-dark">{{ number_format($property->Price) }}₫</span>
                        </div>
                        <p class="card-text">{{ $property->Description }}</p>
                        <ul class="list-unstyled small mb-0">
                            <li><i class="bi bi-geo-alt"></i> {{ $property->Address }}</li>
                            <li><i class="bi bi-calendar"></i> {{ $property->PostedDate }}</li>
                            <li><i class="bi bi-activity"></i> {{ $property->Status  }}</li>
                            <li><strong>Mã BĐS:</strong> {{ $property->PropertyID }}</li>
                        </ul>
                    </div>
                </div>
            </div>
        @empty
            <div class="col">
                <div class="alert alert-info">Bạn chưa có bất động sản nào.</div>
            </div>
        @endforelse
    </div>
</div>

<div class="modal fade" id="addPropertyModal" tabindex="-1" aria-labelledby="addPropertyModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="addPropertyModalLabel">Thêm Bất Động Sản</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
      </div>
      <div class="modal-body">
        @include('_system.partialview.create_property')
      </div>
    </div>
  </div>
</div>

<script>
document.getElementById('addPropertyModal').addEventListener('shown.bs.modal', function () {
    const saleType = document.getElementById('sale_type');
    const waterPriceGroup = document.getElementById('water_price_group');
    const powerPriceGroup = document.getElementById('power_price_group');
    const utilitiesGroup = document.getElementById('utilities_group');

    function toggleFields() {
        if (saleType.value === 'sale') {
            waterPriceGroup.style.display = 'none';
            powerPriceGroup.style.display = 'none';
            utilitiesGroup.style.display = 'none';
        } else {
            waterPriceGroup.style.display = '';
            powerPriceGroup.style.display = '';
            utilitiesGroup.style.display = '';
        }
    }
    if (saleType) {
        saleType.removeEventListener('change', toggleFields); 
        saleType.addEventListener('change', toggleFields);
        toggleFields();
    }
});
</script>
@endsection
