@extends('_layout._layhome.home')

@section('content')
<div class="container-fluid py-4">
    <!-- Alert Messages -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i>
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-warning alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-triangle me-2"></i>
            <strong>Có lỗi xảy ra:</strong>
            <ul class="mb-0 mt-2">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Search and Filter Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-body">
                    <form method="GET" action="{{ route('customer.contact-agent') }}">
                        <!-- Main Filter Row -->
                        <div class="row g-3 mb-3">
                            <div class="col-md-4">
                                <label class="form-label fw-bold">
                                    <i class="fas fa-search me-1"></i>Tìm kiếm
                                </label>
                                <input type="text" class="form-control" name="search"
                                       value="{{ $request->search }}"
                                       placeholder="Tên môi giới hoặc khu vực...">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-bold">
                                    <i class="fas fa-map-marker-alt me-1"></i>Tỉnh/Thành
                                </label>
                                <select class="form-select" name="province">
                                    <option value="">Tất cả tỉnh/thành</option>
                                    @foreach($provinces as $province)
                                        <option value="{{ $province }}"
                                                {{ $request->province == $province ? 'selected' : '' }}>
                                            {{ $province }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-bold">
                                    <i class="fas fa-star me-1"></i>Đánh giá tối thiểu
                                </label>
                                <select class="form-select" name="min_rating">
                                    <option value="">Tất cả đánh giá</option>
                                    <option value="5" {{ $request->min_rating == '5' ? 'selected' : '' }}>
                                        5 sao ⭐⭐⭐⭐⭐
                                    </option>
                                    <option value="4" {{ $request->min_rating == '4' ? 'selected' : '' }}>
                                        4 sao trở lên ⭐⭐⭐⭐
                                    </option>
                                    <option value="3" {{ $request->min_rating == '3' ? 'selected' : '' }}>
                                        3 sao trở lên ⭐⭐⭐
                                    </option>
                                    <option value="2" {{ $request->min_rating == '2' ? 'selected' : '' }}>
                                        2 sao trở lên ⭐⭐
                                    </option>
                                    <option value="1" {{ $request->min_rating == '1' ? 'selected' : '' }}>
                                        1 sao trở lên ⭐
                                    </option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">&nbsp;</label>
                                <div class="d-grid">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-search me-1"></i>Tìm kiếm
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Quick Filter Row -->
                        <div class="row g-2">
                            <div class="col-md-6">
                                <div class="d-flex flex-wrap gap-2">
                                    <span class="text-muted small me-2 align-self-center">Sắp xếp nhanh:</span>
                                    <button type="button" class="btn btn-outline-primary btn-sm quick-sort"
                                            data-sort="name_asc">
                                        <i class="fas fa-sort-alpha-down me-1"></i>Tên A-Z
                                    </button>
                                    <button type="button" class="btn btn-outline-success btn-sm quick-sort"
                                            data-sort="rating_desc">
                                        <i class="fas fa-star me-1"></i>Đánh giá cao
                                    </button>
                                    <button type="button" class="btn btn-outline-info btn-sm quick-sort"
                                            data-sort="newest">
                                        <i class="fas fa-clock me-1"></i>Mới nhất
                                    </button>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="d-flex flex-wrap gap-2 justify-content-md-end">
                                    <span class="text-muted small me-2 align-self-center">Khu vực phổ biến:</span>
                                    <button type="button" class="btn btn-outline-warning btn-sm quick-filter"
                                            data-province="TP. Hồ Chí Minh">
                                        <i class="fas fa-map-marker-alt me-1"></i>TP.HCM
                                    </button>
                                    <button type="button" class="btn btn-outline-danger btn-sm quick-filter"
                                            data-province="Hà Nội">
                                        <i class="fas fa-map-marker-alt me-1"></i>Hà Nội
                                    </button>
                                    <button type="button" class="btn btn-outline-secondary btn-sm"
                                            onclick="clearAllFilters()">
                                        <i class="fas fa-times me-1"></i>Xóa bộ lọc
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Hidden inputs for quick actions -->
                        <input type="hidden" name="sort" id="sortInput" value="{{ $request->sort }}">
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Agents Grid -->
    <div class="row">
        @if($agents->count() > 0)
            @foreach($agents as $agent)
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="card agent-card h-100 shadow-sm border-0">
                        <div class="card-header bg-gradient-primary text-white">
                            <div class="d-flex align-items-center">
                                <div class="agent-avatar me-3">
                                    <div class="rounded-circle d-flex align-items-center justify-content-center"
                                         style="width: 50px; height: 50px; background: rgba(255,255,255,0.2); color: white; font-weight: bold; font-size: 1.2rem;">
                                        {{ substr($agent->Name, 0, 1) }}
                                    </div>
                                </div>
                                <div class="flex-grow-1">
                                    <h5 class="mb-1 text-white">{{ $agent->Name }}</h5>
                                    <small class="text-white-50">
                                        <i class="fas fa-envelope me-1"></i>{{ $agent->Email }}
                                    </small>
                                </div>
                            </div>
                        </div>

                        <div class="card-body">
                            <!-- Contact Info -->
                            <div class="contact-info mb-3">
                                <div class="d-flex align-items-center mb-2">
                                    <i class="fas fa-phone text-success me-2"></i>
                                    <span class="fw-medium">{{ $agent->Phone ?? 'Chưa có số điện thoại' }}</span>
                                </div>

                                @if($agent->profile_agent)
                                    <div class="d-flex align-items-center mb-2">
                                        <i class="fas fa-map-marker-alt text-primary me-2"></i>
                                        <span>
                                            {{ $agent->profile_agent->ProvinceAgent ?? 'Chưa cập nhật' }}
                                            @if($agent->profile_agent->DistrictAgent)
                                                - {{ $agent->profile_agent->DistrictAgent }}
                                            @endif
                                        </span>
                                    </div>

                                    @if($agent->profile_agent->Certificate)
                                        <div class="d-flex align-items-start mb-2">
                                            <i class="fas fa-certificate text-warning me-2 mt-1"></i>
                                            <small class="text-muted">{{ $agent->profile_agent->Certificate }}</small>
                                        </div>
                                    @endif
                                @endif
                            </div>

                            <!-- Rating Section -->
                            <div class="rating-section mb-3 p-3 bg-light rounded">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="fw-bold">
                                        <i class="fas fa-star text-warning me-1"></i>Đánh giá:
                                    </span>
                                    <span class="badge bg-primary">{{ $agent->total_ratings }} lượt</span>
                                </div>

                                <div class="d-flex align-items-center justify-content-between">
                                    <div class="stars-display d-flex align-items-center">
                                        @for($i = 1; $i <= 5; $i++)
                                            @if($agent->average_rating >= $i)
                                                <i class="fas fa-star text-warning me-1"></i>
                                            @elseif($agent->average_rating >= $i - 0.5)
                                                <i class="fas fa-star-half-alt text-warning me-1"></i>
                                            @else
                                                <i class="far fa-star text-warning me-1"></i>
                                            @endif
                                        @endfor
                                    </div>
                                    <div class="rating-number">
                                        <span class="fw-bold text-primary fs-5">
                                            {{ $agent->average_rating > 0 ? number_format($agent->average_rating, 1) : '0.0' }}
                                        </span>
                                        <span class="text-muted small">/5</span>
                                    </div>
                                </div>

                                @if($agent->average_rating > 0)
                                    <div class="mt-2">
                                        <div class="progress" style="height: 6px;">
                                            <div class="progress-bar bg-warning"
                                                 style="width: {{ ($agent->average_rating / 5) * 100 }}%"></div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="card-footer bg-transparent border-0 pt-0">
                            <div class="row g-2">
                                <div class="col-6">
                                    @if($agent->Phone)
                                        <a href="https://zalo.me/{{ $agent->Phone }}"
                                           target="_blank"
                                           class="btn btn-success btn-sm w-100">
                                            <i class="fab fa-zalo me-1"></i>Zalo
                                        </a>
                                    @else
                                        <button class="btn btn-secondary btn-sm w-100" disabled>
                                            <i class="fab fa-zalo me-1"></i>Chưa có SĐT
                                        </button>
                                    @endif
                                </div>
                                <div class="col-6">
                                    <button type="button"
                                            class="btn btn-primary btn-sm w-100"
                                            data-bs-toggle="modal"
                                            data-bs-target="#feedbackModal"
                                            data-agent-id="{{ $agent->UserID }}"
                                            data-agent-name="{{ $agent->Name }}">
                                        <i class="fas fa-star me-1"></i>Đánh giá
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        @else
            <div class="col-12">
                <div class="text-center py-5">
                    <i class="fas fa-user-times fa-4x text-muted mb-3"></i>
                    <h4 class="text-muted">Không tìm thấy môi giới nào</h4>
                    <p class="text-muted">Thử thay đổi tiêu chí tìm kiếm của bạn</p>
                </div>
            </div>
        @endif
    </div>

    <!-- Pagination -->
    @if($hasMorePages || $currentPage > 1)
        <div class="row mt-4">
            <div class="col-12">
                <nav aria-label="Agent pagination">
                    <ul class="pagination justify-content-center">
                        @if($currentPage > 1)
                            <li class="page-item">
                                <a class="page-link" href="{{ request()->fullUrlWithQuery(['page' => $currentPage - 1]) }}">
                                    <i class="fas fa-chevron-left"></i> Trước
                                </a>
                            </li>
                        @endif

                        <li class="page-item active">
                            <span class="page-link">{{ $currentPage }}</span>
                        </li>

                        @if($hasMorePages)
                            <li class="page-item">
                                <a class="page-link" href="{{ request()->fullUrlWithQuery(['page' => $currentPage + 1]) }}">
                                    Sau <i class="fas fa-chevron-right"></i>
                                </a>
                            </li>
                        @endif
                    </ul>
                </nav>
            </div>
        </div>
    @endif
</div>

<!-- Feedback Modal -->
<div class="modal fade" id="feedbackModal" tabindex="-1" aria-labelledby="feedbackModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-gradient-primary text-white">
                <h5 class="modal-title" id="feedbackModalLabel">
                    <i class="fas fa-star text-warning me-2"></i>Đánh giá môi giới
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form action="{{ route('customer.submit-feedback') }}" method="POST" id="feedbackForm">
                @csrf
                <div class="modal-body">
                    <input type="hidden" id="agentId" name="agent_id">

                    <!-- Agent Info Card -->
                    <div class="agent-info-card mb-4 p-3 bg-light rounded-lg">
                        <div class="d-flex align-items-center">
                            <div class="agent-avatar-modal me-3">
                                <div class="rounded-circle d-flex align-items-center justify-content-center bg-primary text-white"
                                     style="width: 60px; height: 60px; font-weight: bold; font-size: 1.5rem;" id="agentAvatar">
                                </div>
                            </div>
                            <div>
                                <h6 class="mb-1 fw-bold" id="agentName"></h6>
                                <small class="text-muted">Môi giới bất động sản</small>
                            </div>
                        </div>
                    </div>

                    <!-- Rating Section -->
                    <div class="rating-section-modal mb-4">
                        <label class="form-label fw-bold mb-3">
                            <i class="fas fa-star text-warning me-2"></i>Đánh giá của bạn
                        </label>
                        <div class="rating-input-modal d-flex justify-content-center mb-3">
                            @for($i = 1; $i <= 5; $i++)
                                <input type="radio" id="star{{ $i }}" name="rating" value="{{ $i }}" required>
                                <label for="star{{ $i }}" class="star-label-modal me-2">
                                    <i class="fas fa-star"></i>
                                </label>
                            @endfor
                        </div>
                        <div class="rating-description text-center">
                            <small class="text-muted" id="ratingText">Chọn số sao để đánh giá</small>
                        </div>
                    </div>

                    <!-- Title Input -->
                    <div class="mb-3">
                        <label for="feedbackTitle" class="form-label fw-bold">
                            <i class="fas fa-heading me-2"></i>Tiêu đề đánh giá
                        </label>
                        <input type="text" class="form-control" id="feedbackTitle" name="title" required
                               placeholder="Nhập tiêu đề cho đánh giá của bạn"
                               maxlength="255">
                        <div class="d-flex justify-content-between">
                            <small class="text-muted">Tối đa 255 ký tự</small>
                            <small class="text-muted" id="titleCounter">0/255</small>
                        </div>
                    </div>

                    <!-- Comment Input -->
                    <div class="mb-3">
                        <label for="feedbackComment" class="form-label fw-bold">
                            <i class="fas fa-comment me-2"></i>Nhận xét chi tiết (không bắt buộc)
                        </label>
                        <textarea class="form-control" id="feedbackComment" name="comment" rows="5"
                                  placeholder="Chia sẻ trải nghiệm của bạn với môi giới này...
Ví dụ: Dịch vụ tư vấn, thái độ phục vụ, chuyên môn, độ tin cậy..."
                                  maxlength="1000"></textarea>
                        <div class="d-flex justify-content-between">
                            <small class="text-muted">Tối đa 1000 ký tự</small>
                            <small class="text-muted" id="commentCounter">0/1000</small>
                        </div>
                    </div>

                    <!-- Guidelines -->
                    <div class="guidelines mb-3">
                        <h6 class="fw-bold text-primary">
                            <i class="fas fa-info-circle me-2"></i>Hướng dẫn đánh giá
                        </h6>
                        <ul class="small text-muted mb-0">
                            <li>⭐ 1 sao: Rất không hài lòng - Dịch vụ rất kém</li>
                            <li>⭐⭐ 2 sao: Không hài lòng - Dịch vụ chưa tốt</li>
                            <li>⭐⭐⭐ 3 sao: Bình thường - Dịch vụ ổn</li>
                            <li>⭐⭐⭐⭐ 4 sao: Hài lòng - Dịch vụ tốt</li>
                            <li>⭐⭐⭐⭐⭐ 5 sao: Rất hài lòng - Dịch vụ xuất sắc</li>
                        </ul>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i>Hủy
                    </button>
                    <button type="submit" class="btn btn-primary" id="submitBtn">
                        <i class="fas fa-paper-plane me-1"></i>Gửi đánh giá
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@section('styles')
<style>
/* Agent Card Styles */
.agent-card {
    transition: transform 0.2s ease, box-shadow 0.2s ease;
    border-radius: 15px;
    overflow: hidden;
}

.agent-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.15) !important;
}

.bg-gradient-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

.agent-avatar {
    position: relative;
}

.contact-info i {
    width: 20px;
    text-align: center;
}

.rating-section {
    border: 1px solid #e9ecef;
}

.stars i {
    font-size: 1.1rem;
}

/* Modal Rating Input Styles */
.rating-input-modal {
    display: flex;
    gap: 10px;
    margin-bottom: 10px;
}

.rating-input-modal input[type="radio"] {
    display: none;
}

.star-label-modal {
    font-size: 2rem;
    color: #ddd;
    cursor: pointer;
    transition: all 0.2s ease;
}

.star-label-modal:hover {
    color: #ffc107;
    transform: scale(1.1);
}

.rating-input-modal input[type="radio"]:checked + .star-label-modal {
    color: #ffc107;
    transform: scale(1.1);
}

/* Alert auto-dismiss */
.alert {
    animation: slideDown 0.3s ease-out;
}

@keyframes slideDown {
    from { opacity: 0; transform: translateY(-20px); }
    to { opacity: 1; transform: translateY(0); }
}

/* Quick filter buttons */
.quick-sort.active,
.quick-filter.active {
    background-color: var(--bs-primary);
    color: white;
    border-color: var(--bs-primary);
}

/* Pagination Styles */
.page-link {
    border-radius: 8px;
    margin: 0 2px;
    border: none;
    background: transparent;
    color: #667eea;
}

.page-link:hover {
    background: #667eea;
    color: white;
}

.page-item.active .page-link {
    background: #667eea;
    border-color: #667eea;
}

/* Loading state */
.btn.loading {
    position: relative;
    pointer-events: none;
}

.btn.loading::after {
    content: "";
    position: absolute;
    width: 16px;
    height: 16px;
    top: 50%;
    left: 50%;
    margin-left: -8px;
    margin-top: -8px;
    border: 2px solid transparent;
    border-top-color: currentColor;
    border-radius: 50%;
    animation: spin 1s linear infinite;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

/* Character counters */
#titleCounter,
#commentCounter {
    font-size: 0.75rem;
    font-weight: 500;
}

/* Responsive improvements */
@media (max-width: 768px) {
    .d-flex.gap-2 {
        gap: 0.5rem !important;
    }

    .btn-sm {
        font-size: 0.75rem;
        padding: 0.25rem 0.5rem;
    }

    .star-label-modal {
        font-size: 1.5rem;
    }
}
</style>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-dismiss alerts after 5 seconds
    setTimeout(function() {
        const alerts = document.querySelectorAll('.alert');
        alerts.forEach(alert => {
            const bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        });
    }, 5000);

    // Handle feedback modal
    const feedbackModal = document.getElementById('feedbackModal');
    const feedbackForm = document.getElementById('feedbackForm');

    feedbackModal.addEventListener('show.bs.modal', function(event) {
        const button = event.relatedTarget;
        const agentId = button.getAttribute('data-agent-id');
        const agentName = button.getAttribute('data-agent-name');

        // Update modal content
        document.getElementById('agentId').value = agentId;
        document.getElementById('agentName').textContent = agentName;

        // Update avatar with first letter
        const avatar = document.getElementById('agentAvatar');
        avatar.textContent = agentName.charAt(0).toUpperCase();

        // Reset form
        feedbackForm.reset();
        document.getElementById('agentId').value = agentId; // Restore after reset

        // Reset rating display
        updateStarDisplay();
        updateRatingText(0);

        // Reset character counters
        updateCharacterCount('feedbackTitle', 'titleCounter', 255);
        updateCharacterCount('feedbackComment', 'commentCounter', 1000);
    });

    // Handle star rating display
    const starLabels = document.querySelectorAll('.star-label-modal');
    const ratingInputs = document.querySelectorAll('input[name="rating"]');

    function updateStarDisplay() {
        const checkedValue = parseInt(document.querySelector('input[name="rating"]:checked')?.value || 0);

        starLabels.forEach((label, index) => {
            const starValue = index + 1;
            if (starValue <= checkedValue) {
                label.style.color = '#ffc107';
                label.style.transform = 'scale(1.1)';
            } else {
                label.style.color = '#ddd';
                label.style.transform = 'scale(1)';
            }
        });
    }

    function updateRatingText(rating) {
        const ratingText = document.getElementById('ratingText');
        const messages = {
            0: 'Chọn số sao để đánh giá',
            1: 'Rất không hài lòng - Dịch vụ rất kém',
            2: 'Không hài lòng - Dịch vụ chưa tốt',
            3: 'Bình thường - Dịch vụ ổn',
            4: 'Hài lòng - Dịch vụ tốt',
            5: 'Rất hài lòng - Dịch vụ xuất sắc'
        };
        ratingText.textContent = messages[rating] || messages[0];
    }

    // Add event listeners for rating
    ratingInputs.forEach(input => {
        input.addEventListener('change', function() {
            updateStarDisplay();
            updateRatingText(parseInt(this.value));
        });
    });

    // Add hover effects for stars
    starLabels.forEach((label, index) => {
        label.addEventListener('mouseenter', function() {
            const hoverValue = index + 1;
            starLabels.forEach((l, i) => {
                if (i < hoverValue) {
                    l.style.color = '#ffc107';
                    l.style.transform = 'scale(1.1)';
                } else {
                    l.style.color = '#ddd';
                    l.style.transform = 'scale(1)';
                }
            });
        });

        label.addEventListener('mouseleave', updateStarDisplay);
    });

    // Character counters
    function updateCharacterCount(inputId, counterId, maxLength) {
        const input = document.getElementById(inputId);
        const counter = document.getElementById(counterId);

        if (input && counter) {
            const currentLength = input.value.length;
            counter.textContent = `${currentLength}/${maxLength}`;

            if (currentLength > maxLength * 0.9) {
                counter.style.color = '#dc3545';
            } else if (currentLength > maxLength * 0.7) {
                counter.style.color = '#ffc107';
            } else {
                counter.style.color = '#6c757d';
            }
        }
    }

    // Add character counter listeners
    document.getElementById('feedbackTitle').addEventListener('input', function() {
        updateCharacterCount('feedbackTitle', 'titleCounter', 255);
    });

    document.getElementById('feedbackComment').addEventListener('input', function() {
        updateCharacterCount('feedbackComment', 'commentCounter', 1000);
    });

    // Handle form submission
    feedbackForm.addEventListener('submit', function(e) {
        const submitBtn = document.getElementById('submitBtn');

        // Add loading state
        submitBtn.classList.add('loading');
        submitBtn.disabled = true;

        // Reset after 3 seconds in case of network issues
        setTimeout(() => {
            submitBtn.classList.remove('loading');
            submitBtn.disabled = false;
        }, 3000);
    });

    // Quick sort functionality
    document.querySelectorAll('.quick-sort').forEach(button => {
        button.addEventListener('click', function() {
            const sortValue = this.dataset.sort;
            document.getElementById('sortInput').value = sortValue;

            // Submit form
            this.closest('form').submit();
        });
    });

    // Quick filter functionality
    document.querySelectorAll('.quick-filter').forEach(button => {
        button.addEventListener('click', function() {
            const provinceValue = this.dataset.province;
            document.querySelector('select[name="province"]').value = provinceValue;

            // Submit form
            this.closest('form').submit();
        });
    });

    // Clear all filters
    window.clearAllFilters = function() {
        const form = document.querySelector('form[method="GET"]');
        const inputs = form.querySelectorAll('input, select');

        inputs.forEach(input => {
            if (input.type === 'hidden') return;
            input.value = '';
        });

        form.submit();
    };
});
</script>
@endsection
