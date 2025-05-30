@extends('_layout._layhome.home')

@section('home')
<div class="container mt-4">
    <div class="property-detail-page">
        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">Trang chủ</a></li>
                <li class="breadcrumb-item"><a href="{{ route('customer.search', ['type' => $property->PropertyType]) }}">{{ $property->danhMuc->ten_pro ?? 'Bất động sản' }}</a></li>
                <li class="breadcrumb-item active" aria-current="page">{{ $property->Title }}</li>
            </ol>
        </nav>

        <!-- 1. Tiêu đề & Mô tả ngắn -->
        <div class="property-header">
            <div class="row">
                <div class="col-md-8">
            <h1>{{ $property->Title }}</h1>
            <p class="location">
                <i class="bi bi-geo-alt-fill"></i>
                {{ $property->Address }}, {{ $property->Ward }}, {{ $property->District }}, {{ $property->Province }}
            </p>
                    <div class="property-short-description">
                        <p>{{ \Illuminate\Support\Str::limit($property->Description, 250) }}</p>
                    </div>
                </div>
                <div class="col-md-4 text-end">
                    <div class="property-price">
                        <h2>{{ number_format($property->Price, 0, ',', '.') }} đ</h2>
                        @if($property->chiTiet->first())
                            @if($property->chiTiet->first()->Area > 0)
                                <p>{{ $property->chiTiet->first()->getDienTichText() }} - {{ number_format(($property->Price / $property->chiTiet->first()->Area), 0, ',', '.') }} đ/m²</p>
                            @else
                                <p>{{ $property->chiTiet->first()->getDienTichText() }}</p>
                            @endif
                        @else
                            <p>N/A m²</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- 2. Hình ảnh & Thư viện ảnh -->
        <div class="property-gallery">
            <!-- Main gallery image -->
            <div class="main-image">
                @if($property->images->count() > 0)
                    @php
                        // Get first image or primary image if exists
                        $mainImage = $property->images->first();
                        // Chỉ lấy phần đường dẫn sau public/
                        $imagePath = str_replace('storage/app/public/', '', $mainImage->ImagePath);
                    @endphp
                    <img src="{{ asset('storage/' . $imagePath) }}" alt="{{ $mainImage->Caption ?? $property->Title }}" id="mainImage">
                @else
                    <img src="{{ asset('storage/properties/no-image.jpg') }}" alt="{{ $property->Title }}" id="mainImage">
                @endif
            </div>
            
            <!-- Thumbnail gallery -->
            <div class="thumbnail-list">
                @if($property->images->count() > 0)
                    @foreach($property->images as $image)
                        @php
                            // Chỉ lấy phần đường dẫn sau public/
                            $thumbPath = str_replace('storage/app/public/', '', $image->ImagePath);
                        @endphp
                        <div class="thumbnail {{ $loop->first ? 'active' : '' }}" onclick="changeImage('{{ asset('storage/' . $thumbPath) }}')">
                            <img src="{{ asset('storage/' . $thumbPath) }}" alt="{{ $image->Caption ?? $property->Title }}">
                        </div>
                    @endforeach
                @else
                    <!-- No images found, display placeholder -->
                    <div class="thumbnail active">
                        <img src="{{ asset('storage/properties/no-image.jpg') }}" alt="{{ $property->Title }}">
                    </div>
                @endif
            </div>
        </div>

        <div class="row mt-4">
                <div class="col-md-8">
                <!-- 3. Thông tin chi tiết về bất động sản -->
                <div class="card info-card mb-4">
                    <div class="card-header">
                        <h3>Thông tin chi tiết</h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>Loại BĐS:</strong> {{ $property->danhMuc->ten_pro ?? 'N/A' }}</p>
                                @if($property->chiTiet->first())
                                    <p><strong>Diện tích:</strong> {{ $property->chiTiet->first()->getDienTichText() }}</p>
                                    <p><strong>Số phòng ngủ:</strong> {{ $property->chiTiet->first()->getSoPhongNguText() }}</p>
                                    <p><strong>Số phòng tắm/WC:</strong> {{ $property->chiTiet->first()->getSoPhongTamWCText() }}</p>
                                    <p><strong>Số tầng:</strong> {{ $property->chiTiet->first()->getSoTangText() }}</p>
                                @else
                                    <p><strong>Diện tích:</strong> N/A m²</p>
                                    <p><strong>Số phòng ngủ:</strong> N/A</p>
                                    <p><strong>Số phòng tắm/WC:</strong> N/A</p>
                                    <p><strong>Số tầng:</strong> N/A</p>
                                @endif
                            </div>
                            <div class="col-md-6">
                                @if($property->chiTiet->first())
                                    <p><strong>Đường rộng:</strong> {{ $property->chiTiet->first()->getDuongRongText() }}</p>
                                    <p><strong>Pháp lý:</strong> {{ $property->chiTiet->first()->getPhapLyText() }}</p>
                                    <p><strong>Nội thất:</strong> {{ $property->chiTiet->first()->getNoiThatText() }}</p>
                                    <p><strong>Giá điện:</strong> {{ $property->chiTiet->first()->getGiaDienText() }}</p>
                                    <p><strong>Giá nước:</strong> {{ $property->chiTiet->first()->getGiaNuocText() }}</p>
                                @else
                                    <p><strong>Đường rộng:</strong> N/A</p>
                                    <p><strong>Pháp lý:</strong> N/A</p>
                                    <p><strong>Nội thất:</strong> N/A</p>
                                    <p><strong>Giá điện:</strong> 0 đ/kWh</p>
                                    <p><strong>Giá nước:</strong> 0 đ/m³</p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 4. Vị trí & Kết nối giao thông -->
                <div class="card mb-4">
                        <div class="card-header">
                        <h3>Vị trí & Kết nối giao thông</h3>
                        </div>
                        <div class="card-body">
                        <div class="property-location-details">
                            <p><strong>Địa chỉ đầy đủ:</strong> {{ $property->Address }}, {{ $property->Ward }}, {{ $property->District }}, {{ $property->Province }}</p>
                            
                            <div class="location-advantages">
                                <h4>Thuận lợi di chuyển</h4>
                                <ul>
                                    <li>Cách trung tâm thành phố: khoảng 5km</li>
                                    <li>Cách trường học gần nhất: 500m</li>
                                    <li>Cách bệnh viện: 2km</li>
                                    <li>Cách siêu thị/chợ: 300m</li>
                                </ul>
                            </div>
                            
                            <div class="surrounding-amenities">
                                <h4>Tiện ích xung quanh</h4>
                                <ul>
                                    <li>Gần công viên</li>
                                    <li>Gần trung tâm thương mại</li>
                                    <li>Hệ thống giao thông thuận tiện</li>
                                </ul>
                            </div>
                            </div>
                        </div>
                    </div>

                <!-- 6. Bản đồ & Vị trí trên Google Maps -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h3>Bản đồ & Vị trí</h3>
                    </div>
                    <div class="card-body">
                        <div class="google-map">
                            @php
                                $mapAddress = urlencode($property->Address . ', ' . $property->Ward . ', ' . $property->District . ', ' . $property->Province);
                                $googleMapUrl = "https://www.google.com/maps/embed/v1/place?key=AIzaSyBFw0Qbyq9zTFTd-tUY6dZWTgaQzuU17R8&q=" . $mapAddress;
                            @endphp
                            <iframe 
                                src="{{ $googleMapUrl }}" 
                                width="100%" 
                                height="400" 
                                style="border:0;" 
                                allowfullscreen="" 
                                loading="lazy">
                            </iframe>
                        </div>
                    </div>
                </div>

                <!-- 7. Thông tin pháp lý & tiến độ thanh toán -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h3>Thông tin pháp lý & Tiến độ thanh toán</h3>
                        </div>
                        <div class="card-body">
                        <div class="legal-info">
                            <h4>Thông tin pháp lý</h4>
                            <ul>
                                <li><strong>Loại giấy tờ:</strong> {{ $property->chiTiet->first()->legal ?? 'Chưa có thông tin' }}</li>
                                <li><strong>Tình trạng sở hữu:</strong> Sở hữu lâu dài</li>
                                <li><strong>Giấy phép xây dựng:</strong> Đầy đủ</li>
                                <li><strong>Quy hoạch:</strong> Theo quy hoạch của thành phố</li>
                            </ul>
                        </div>
                        
                        <div class="payment-schedule mt-4">
                            <h4>Tiến độ thanh toán</h4>
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Đợt</th>
                                            <th>Thời gian</th>
                                            <th>Tỷ lệ thanh toán</th>
                                            <th>Ghi chú</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>Đặt cọc</td>
                                            <td>Khi ký hợp đồng</td>
                                            <td>10%</td>
                                            <td>Giữ chỗ</td>
                                        </tr>
                                        <tr>
                                            <td>Đợt 1</td>
                                            <td>Sau 15 ngày</td>
                                            <td>40%</td>
                                            <td>Ký hợp đồng mua bán</td>
                                        </tr>
                                        <tr>
                                            <td>Đợt 2</td>
                                            <td>Sau 30 ngày</td>
                                            <td>50%</td>
                                            <td>Nhận bàn giao</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 8. Thông tin về chủ đầu tư & nhà phát triển -->
                <div class="card mb-4">
                        <div class="card-header">
                        <h3>Thông tin chủ đầu tư</h3>
                        </div>
                        <div class="card-body">
                        <div class="developer-info">
                            @if($property->chusohuu)
                                <div class="developer-details">
                                    <h4>{{ $property->chusohuu->name }}</h4>
                                    <p>{{ $property->chusohuu->address ?? 'Không có thông tin địa chỉ' }}</p>
                                    <p><strong>Email:</strong> {{ $property->chusohuu->email }}</p>
                                    @if($property->chusohuu->phone)
                                        <p><strong>Điện thoại:</strong> {{ $property->chusohuu->phone }}</p>
                                    @endif
                                </div>
                                <div class="developer-projects mt-3">
                                    <h4>Các dự án đã thực hiện</h4>
                                    <p>Thông tin đang được cập nhật.</p>
                                </div>
                            @else
                                <p>Không có thông tin về chủ đầu tư.</p>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- 9. Mô tả chi tiết & điểm nổi bật -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h3>Mô tả chi tiết & Điểm nổi bật</h3>
                    </div>
                    <div class="card-body">
                        <div class="property-description">
                            <h4>Mô tả chi tiết</h4>
                            <div class="description-content">
                                {!! nl2br(e($property->Description)) !!}
                            </div>
                        </div>
                        
                        <div class="property-highlights mt-4">
                            <h4>Điểm nổi bật</h4>
                            <ul class="highlights-list">
                                <li>Vị trí đắc địa, thuận tiện di chuyển</li>
                                <li>Thiết kế hiện đại, tiện nghi đầy đủ</li>
                                <li>Hệ thống an ninh 24/7</li>
                                <li>Khu vực dân cư văn minh, an ninh</li>
                                <li>Tiện ích xung quanh đầy đủ</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <!-- 5. Thông tin liên hệ -->
                <div class="card contact-card sticky-top" style="top: 20px;">
                    <div class="card-header">
                        <h3>Thông tin liên hệ</h3>
                    </div>
                    <div class="card-body">
                        @if($property->moigioi)
                            <div class="agent-info">
                                <div class="agent-avatar">
                                    <img src="{{ asset('storage/properties/agent.jpg') }}" alt="{{ $property->moigioi->name }}">
                                </div>
                                <div class="agent-details">
                                    <h4>{{ $property->moigioi->name }}</h4>
                                    <p>Môi giới bất động sản</p>
                                    <div class="agent-rating">
                                        <i class="bi bi-star-fill"></i>
                                        <i class="bi bi-star-fill"></i>
                                        <i class="bi bi-star-fill"></i>
                                        <i class="bi bi-star-fill"></i>
                                        <i class="bi bi-star-half"></i>
                                        <span>(4.5/5)</span>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="contact-details mt-3">
                                <p><i class="bi bi-telephone-fill"></i> {{ substr($property->moigioi->phone, 0, 7) . '***' }}</p>
                                <p><i class="bi bi-envelope-fill"></i> {{ $property->moigioi->email }}</p>
                                <p><i class="bi bi-geo-alt-fill"></i> {{ $property->moigioi->address ?? 'Không có thông tin địa chỉ' }}</p>
                            </div>
                        @else
                            <p>Không có thông tin môi giới.</p>
                        @endif
                        
                        @auth
                        <div class="contact-form mt-4">
                            <h4>Liên hệ ngay</h4>
                            <form>
                                <div class="mb-3">
                                    <input type="text" class="form-control" placeholder="Họ và tên">
                                </div>
                                <div class="mb-3">
                                    <input type="email" class="form-control" placeholder="Email">
                                </div>
                                <div class="mb-3">
                                    <input type="tel" class="form-control" placeholder="Số điện thoại">
                                </div>
                                <div class="mb-3">
                                    <textarea class="form-control" rows="3" placeholder="Tin nhắn"></textarea>
                                </div>
                                <div class="contact-buttons">
                                    <button type="submit" class="btn btn-primary w-100 mb-2">
                                        <i class="bi bi-send-fill"></i> Gửi tin nhắn
                                    </button>
                                    <a href="tel:{{ $property->moigioi->phone ?? '' }}" class="btn btn-success w-100">
                                        <i class="bi bi-telephone-fill"></i> Gọi ngay
                                    </a>
                                </div>
                            </form>
                        </div>
                        @else
                            <p>Vui lòng đăng nhập để liên hệ với môi giới.</p>
                            <a href="{{ route('login') }}" class="btn btn-primary">Đăng nhập</a>
                        @endauth
                    </div>
                </div>
            </div>
        </div>

        <!-- Bất động sản liên quan -->
        <div class="related-properties mt-5">
            <h3>Bất động sản liên quan</h3>

            <div class="row property-cards">
                @foreach($relatedProperties as $relatedProperty)
                    <div class="col-md-4 mb-4">
                        <div class="property-card">
                            <div class="property-image">
                                @if($relatedProperty->images->count() > 0)
                                    @php
                                        $mainImage = $relatedProperty->images->first();
                                        $imagePath = str_replace('storage/app/public/', '', $mainImage->ImagePath);
                                    @endphp
                                    <img src="{{ asset('storage/' . $imagePath) }}" alt="{{ $relatedProperty->Title }}">
                                @else
                                    <img src="{{ asset('storage/properties/no-image.jpg') }}" alt="{{ $relatedProperty->Title }}">
                                @endif
                            </div>
                            <div class="property-content">
                                <h3 class="property-title">{{ $relatedProperty->Title }}</h3>
                                <p class="property-location">
                                    <i class="bi bi-geo-alt"></i>
                                    {{ $relatedProperty->District }}, {{ $relatedProperty->Province }}
                                </p>
                                <div class="property-details">
                                    <div class="detail">
                                        <i class="bi bi-rulers"></i>
                                        <span>{{ $relatedProperty->chiTiet->first()->Area ?? 'N/A' }} m²</span>
                                    </div>
                                    <div class="detail">
                                        <i class="bi bi-building"></i>
                                        <span>{{ $relatedProperty->danhMuc->ten_pro ?? 'N/A' }}</span>
                                    </div>
                                    <div class="detail">
                                        <i class="bi bi-currency-dollar"></i>
                                        <span>{{ number_format($relatedProperty->Price, 0, ',', '.') }} đ</span>
                                    </div>
                                </div>
                                <div class="property-footer">
                                    <a href="{{ route('property.detail', $relatedProperty->PropertyID) }}" class="btn btn-view">Xem chi tiết</a>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

<script>
    function changeImage(imgSrc) {
        document.getElementById('mainImage').src = imgSrc;
        
        // Update active thumbnail
        const thumbnails = document.querySelectorAll('.thumbnail');
        thumbnails.forEach(thumbnail => {
            thumbnail.classList.remove('active');
            if (thumbnail.querySelector('img').src === imgSrc) {
                thumbnail.classList.add('active');
            }
        });
    }
</script>
@endsection