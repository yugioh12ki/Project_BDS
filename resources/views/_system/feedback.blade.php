@extends('_layout._layadmin.app')
@section('feedback')

@if(isset($error))
    <div class="alert alert-danger">
        {{ $error }}
    </div>
@endif

@if(session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger">
        {{ session('error') }}
    </div>
@endif

@if($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif



<h1>Danh sách Feedback</h1>

<div class="filter-container">
    <div class="d-flex justify-content-between align-items-center mb-3">
        {{-- Combobox chọn type --}}
        <div class="d-flex align-items-center">
            <label for="type-select" class="me-2">Lọc danh sách:</label>
            <select id="type-select" class="form-control" style="width: 200px;">
                <option value="all">Tất cả</option>
                <option value="Hủy bỏ">Hủy bỏ</option>
                <option value="Đã duyệt">Đã duyệt</option>
                <option value="Chờ duyệt">Chờ duyệt</option>
            </select>
        </div>

        {{-- Combox chọn rating (có thể đánh sao 0.5 -> 5.0) --}}
        <div class="d-flex align-items-center">
            <label for="rating-range-select" class="me-2">Đánh giá:</label>
            <select id="rating-range-select" class="form-control" style="width: 200px;">
            <option value="all">Tất cả</option>
            <option value="1">1 sao</option>
            <option value="2">2 sao</option>
            <option value="3">3 sao</option>
            <option value="4">4 sao</option>
            <option value="5">5 sao</option>
            </select>
        </div>
    </div>
</div>
<div class="table-container">
    @if(isset($error))
        <div class="alert alert-danger">{{ $error }}</div>
    @else
    @include('_system.partialview.feedback_table', [
        'feedbacks' => $feedbacks,
        'columns' => $columns,
        'users' => $users ?? [],
    ])
    @endif
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const statusSelect = document.getElementById('type-select');
    const ratingSelect = document.getElementById('rating-range-select');

    function filterFeedbacks() {
        const status = statusSelect.value;
        const ratingValue = ratingSelect.value;
        let min = 'all';
        let max = 'all';

        if (ratingValue !== 'all') {
            min = ratingValue;
            max = ratingValue;
        }

        // Sử dụng route Laravel để lấy đúng URL
        const url = `{{ route('admin.feedback.filter') }}?status=${encodeURIComponent(status)}&min=${encodeURIComponent(min)}&max=${encodeURIComponent(max)}`;

        document.querySelector('.table-container').innerHTML = '<div class="text-center"><div class="spinner-border" role="status"><span class="visually-hidden">Đang tải...</span></div></div>';

        fetch(url)
            .then(response => {
                if (!response.ok) throw new Error('Network response was not ok');
                return response.text();
            })
            .then(html => {
                document.querySelector('.table-container').innerHTML = html;
            })
            .catch(error => {
                console.error('Error:', error);
                document.querySelector('.table-container').innerHTML =
                    '<div class="alert alert-danger">Có lỗi xảy ra khi tải dữ liệu. Vui lòng thử lại sau.</div>';
            });
    }

    statusSelect.addEventListener('change', filterFeedbacks);
    ratingSelect.addEventListener('change', filterFeedbacks);
});
</script>

@endSection()
