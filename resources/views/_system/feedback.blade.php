@extends('_layout._layadmin.app')
@section('feedback')

<style>
/**
 * Feedback Layout CSS
 * 2-row, 2-column layout for feedback management
 */

/* Variables as CSS Custom Properties */
:root {
    --primary-color: #007bff;
    --success-color: #28a745;
    --danger-color: #dc3545;
    --warning-color: #ffc107;
    --info-color: #17a2b8;
    --light-color: #f8f9fa;
    --dark-color: #343a40;
    --border-radius: 8px;
    --box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    --transition: all 0.3s ease;
}

/* Feedback Layout Container */
.feedback-layout {
    padding: 20px;
}

.feedback-layout .page-header {
    margin-bottom: 30px;
}

.feedback-layout .page-header h1 {
    color: var(--dark-color);
    font-weight: 600;
    margin-bottom: 10px;
}

.feedback-layout .page-header .breadcrumb {
    background: transparent;
    padding: 0;
    margin: 0;
}

/* Filters Row */
.filters-row {
    background: white;
    border-radius: var(--border-radius);
    box-shadow: var(--box-shadow);
    padding: 25px;
    margin-bottom: 25px;
    border: 1px solid #e9ecef;
}

.filters-row .filters-title {
    color: var(--dark-color);
    font-weight: 600;
    font-size: 1.1rem;
    margin-bottom: 20px;
    display: flex;
    align-items: center;
}

.filters-row .filters-title i {
    margin-right: 10px;
    color: var(--primary-color);
}

.filters-row .form-label {
    font-weight: 600;
    color: var(--dark-color);
    margin-bottom: 8px;
    font-size: 0.9rem;
}

.filters-row .form-control, .filters-row .form-select {
    border: 2px solid #e9ecef;
    border-radius: 6px;
    padding: 12px 15px;
    font-size: 0.95rem;
    transition: var(--transition);
}

.filters-row .form-control:focus, .filters-row .form-select:focus {
    border-color: var(--primary-color);
    box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
}

/* Agent Search with Autocomplete */
.autocomplete-container {
    position: relative;
}

.agent-suggestions {
    position: absolute;
    top: 100%;
    left: 0;
    right: 0;
    background: white;
    border: 1px solid #e9ecef;
    border-top: none;
    border-radius: 0 0 6px 6px;
    max-height: 200px;
    overflow-y: auto;
    z-index: 1000;
    display: none;
}

.suggestion-item {
    padding: 12px 15px;
    cursor: pointer;
    border-bottom: 1px solid #f1f3f4;
    transition: var(--transition);
}

.suggestion-item:hover {
    background-color: var(--light-color);
}

.suggestion-item:last-child {
    border-bottom: none;
}

/* User Role Badges */
.suggestion-item .badge {
    font-size: 0.75rem;
    padding: 4px 8px;
    border-radius: 12px;
    margin-left: 8px;
}

.suggestion-item .badge.badge-primary {
    background-color: #007bff;
    color: white;
}

.suggestion-item .badge.badge-info {
    background-color: #17a2b8;
    color: white;
}

/* Action Buttons */
.filters-row .btn {
    border-radius: 6px;
    padding: 12px 25px;
    font-weight: 600;
    transition: var(--transition);
    border: none;
}

.filters-row .btn-primary {
    background: var(--primary-color);
    color: white;
}

.filters-row .btn-primary:hover {
    background: #0056b3;
    transform: translateY(-1px);
}

.filters-row .btn-outline-secondary {
    border: 2px solid #6c757d;
    color: #6c757d;
}

.filters-row .btn-outline-secondary:hover {
    background: #6c757d;
    color: white;
    transform: translateY(-1px);
}

/* Feedback Content Row */
.feedback-content-row .feedback-column .column-header {
    background: white;
    border-radius: var(--border-radius) var(--border-radius) 0 0;
    padding: 20px 25px;
    border-bottom: 1px solid #e9ecef;
}

.feedback-content-row .feedback-column .column-header h4 {
    margin: 0;
    font-weight: 600;
    display: flex;
    align-items: center;
}

.feedback-content-row .feedback-column .column-header h4 i {
    margin-right: 10px;
    width: 20px;
    text-align: center;
}

.feedback-content-row .feedback-column .column-header h4 .badge {
    margin-left: 10px;
    font-size: 14px;
    padding: 6px 12px;
}

.feedback-content-row .feedback-column .column-header.pending-header {
    background-color: #ff9800; /* Màu cam */
}

.feedback-content-row .feedback-column .column-header.pending-header h4 {
    color: white;
}

.feedback-content-row .feedback-column .column-header.pending-header h4 .badge {
    background-color: white;
    color: #ff9800;
}

.feedback-content-row .feedback-column .column-header.approved-header {
    background-color: #4caf50; /* Màu xanh lá */
}

.feedback-content-row .feedback-column .column-header.approved-header h4 {
    color: white;
}

.feedback-content-row .feedback-column .column-header.approved-header h4 .badge {
    background-color: white;
    color: #4caf50;
}

.feedback-content-row .feedback-column .cards-container {
    background: white;
    border-radius: 0 0 var(--border-radius) var(--border-radius);
    padding: 20px;
    min-height: 600px;
    box-shadow: var(--box-shadow);
}

.feedback-content-row .feedback-column .cards-container .cards-grid {
    display: grid;
    grid-template-columns: 1fr;
    gap: 15px;
    max-height: 600px;
    overflow-y: auto;
    padding-right: 5px;
}

/* Custom scrollbar */
.feedback-content-row .feedback-column .cards-container .cards-grid::-webkit-scrollbar {
    width: 6px;
}

.feedback-content-row .feedback-column .cards-container .cards-grid::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 3px;
}

.feedback-content-row .feedback-column .cards-container .cards-grid::-webkit-scrollbar-thumb {
    background: #c1c1c1;
    border-radius: 3px;
}

.feedback-content-row .feedback-column .cards-container .cards-grid::-webkit-scrollbar-thumb:hover {
    background: #a8a8a8;
}

/* Feedback Cards */
.feedback-card {
    background: white;
    border-radius: var(--border-radius);
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    transition: var(--transition);
    border: 1px solid #e9ecef;
    overflow: hidden;
}

.feedback-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 16px rgba(0, 0, 0, 0.15);
}

.feedback-card .card-header {
    background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
    padding: 15px 20px;
    border-bottom: 1px solid #e9ecef;
}

.feedback-card .card-header .customer-info {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 10px;
}

.feedback-card .card-header .customer-name {
    font-weight: 600;
    color: var(--dark-color);
    font-size: 1rem;
}

.feedback-card .card-header .feedback-date {
    color: #6c757d;
    font-size: 0.875rem;
}

.feedback-card .rating {
    display: flex;
    align-items: center;
    margin-bottom: 10px;
}

.feedback-card .rating .stars {
    color: var(--warning-color);
    margin-right: 10px;
    font-size: 1.1rem;
}

.feedback-card .rating .fa-star.active,
.feedback-card .rating .fa-star-half-alt.active {
    color: var(--warning-color);
}

.feedback-card .rating .fa-star:not(.active),
.feedback-card .rating .far.fa-star {
    color: #e9ecef;
}

.feedback-card .rating-text {
    font-size: 0.875rem;
    color: #6c757d;
}

.feedback-card .card-body {
    padding: 20px;
}

.feedback-card .feedback-title {
    font-weight: 600;
    color: var(--dark-color);
    margin-bottom: 10px;
    font-size: 1rem;
}

.feedback-card .feedback-comment {
    color: #6c757d;
    line-height: 1.5;
    margin-bottom: 15px;
    display: -webkit-box;
    -webkit-line-clamp: 3;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.feedback-card .agent-info {
    display: flex;
    align-items: center;
    margin-bottom: 15px;
    padding: 10px;
    background: var(--light-color);
    border-radius: 6px;
}

.feedback-card .agent-info i {
    color: var(--info-color);
    margin-right: 8px;
}

.feedback-card .agent-info .agent-name {
    font-weight: 500;
    color: var(--dark-color);
}

.feedback-card .card-actions {
    display: flex;
    gap: 10px;
    justify-content: flex-end;
    padding: 15px 20px;
    background: #f8f9fa;
    border-top: 1px solid #e9ecef;
}

.feedback-card .card-actions .btn {
    padding: 8px 16px;
    font-size: 0.875rem;
    border-radius: 5px;
    font-weight: 500;
    transition: var(--transition);
}

.feedback-card .card-actions .btn-success {
    background: var(--success-color);
    border: none;
    color: white;
}

.feedback-card .card-actions .btn-success:hover {
    background: #218838;
    transform: translateY(-1px);
}

.feedback-card .card-actions .btn-danger {
    background: var(--danger-color);
    border: none;
    color: white;
}

.feedback-card .card-actions .btn-danger:hover {
    background: #c82333;
    transform: translateY(-1px);
}

/* Status-specific styling */
.feedback-card.pending-card {
    border-left: 4px solid var(--warning-color);
}

.feedback-card.approved-card {
    border-left: 4px solid var(--success-color);
}

/* Empty State */
.empty-state {
    text-align: center;
    padding: 60px 20px;
    color: #6c757d;
}

.empty-state i {
    font-size: 4rem;
    margin-bottom: 20px;
    opacity: 0.3;
}

.empty-state p {
    font-size: 1.1rem;
    margin-bottom: 0;
}

/* Loading Overlay */
.loading-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.5);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 9999;
    display: none;
}

.loading-overlay .spinner-border {
    width: 3rem;
    height: 3rem;
    color: white;
}

.spinner {
    border: 4px solid #f3f3f3;
    border-top: 4px solid #3498db;
    border-radius: 50%;
    width: 50px;
    height: 50px;
    animation: spin 1s linear infinite;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

/* Alerts */
.alert {
    border-radius: var(--border-radius);
    border: none;
    padding: 15px 20px;
    margin-bottom: 20px;
}

.alert-success {
    background: linear-gradient(135deg, var(--success-color), #2ecc71);
    color: white;
}

.alert-danger {
    background: linear-gradient(135deg, var(--danger-color), #e74c3c);
    color: white;
}

/* Modal Enhancements */
.modal-content {
    border-radius: var(--border-radius);
    border: none;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
}

.modal-header {
    background: linear-gradient(135deg, var(--primary-color), #0056b3);
    color: white;
    border-radius: var(--border-radius) var(--border-radius) 0 0;
    border-bottom: none;
}

.modal-header .btn-close {
    filter: brightness(0) invert(1);
}

.modal-body {
    padding: 25px;
}

/* Responsive Design */
@media (max-width: 768px) {
    .feedback-layout {
        padding: 10px;
    }

    .filters-row {
        padding: 15px;
        margin-bottom: 20px;
    }

    .filters-row .row .col-md-3,
    .filters-row .row .col-md-2 {
        margin-bottom: 15px;
    }

    .feedback-content-row .row .col-md-6 {
        margin-bottom: 20px;
    }

    .feedback-content-row .cards-container .cards-grid {
        max-height: 400px;
    }

    .feedback-card .card-actions {
        flex-direction: column;
    }

    .feedback-card .card-actions .btn {
        margin-bottom: 5px;
    }

    .feedback-card .card-actions .btn:last-child {
        margin-bottom: 0;
    }
}

@media (max-width: 576px) {
    .feedback-content-row .feedback-column .column-header {
        padding: 15px;
    }

    .feedback-content-row .cards-container {
        padding: 15px;
        min-height: 400px;
    }

    .feedback-card .card-header {
        padding: 12px 15px;
    }

    .feedback-card .card-body {
        padding: 15px;
    }
}
</style>

<div class="feedback-layout">
    {{-- Page Header --}}
    <div class="page-header">
        <h1><i class="fas fa-comments"></i> Quản Lý Feedback</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active">Feedback</li>
            </ol>
        </nav>
    </div>

    {{-- Alert Messages --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Filters Row --}}
    <div class="filters-row">
        <div class="filters-title">
            <i class="fas fa-filter"></i>
            Bộ lọc tìm kiếm
        </div>
        <div class="row">
            <div class="col-md-4">
                <label class="form-label">Tìm kiếm người dùng</label>
                <div class="autocomplete-container">
                    <input type="text" id="userSearch" class="form-control"
                           placeholder="Nhập tên người dùng hoặc môi giới..." autocomplete="off">
                    <div id="userSuggestions" class="agent-suggestions" style="display: none;">
                        @foreach($agents as $agent)
                            <div class="suggestion-item" data-user-id="{{ $agent->UserID }}"
                                 data-user-name="{{ $agent->Name }}" data-user-role="Agent">
                                <strong>{{ $agent->Name }}</strong> <span class="badge badge-primary">Agent</span>
                                @if($agent->profile_agent)
                                    <small class="d-block text-muted">{{ $agent->profile_agent->ProvinceAgent ?? 'N/A' }}</small>
                                @endif
                            </div>
                        @endforeach
                        @foreach($customers as $customer)
                            <div class="suggestion-item" data-user-id="{{ $customer->UserID }}"
                                 data-user-name="{{ $customer->Name }}" data-user-role="Customer">
                                <strong>{{ $customer->Name }}</strong> <span class="badge badge-info">Customer</span>
                                <small class="d-block text-muted">{{ $customer->Province ?? 'N/A' }}</small>
                            </div>
                        @endforeach
                    </div>
                    <input type="hidden" id="selectedUserId" value="">
                </div>
            </div>

            <div class="col-md-3">
                <label class="form-label">Lọc theo ngày</label>
                <input type="date" id="feedbackDate" class="form-control">
            </div>

            <div class="col-md-3">
                <label class="form-label">Đánh giá</label>
                <select id="ratingFilter" class="form-control">
                    <option value="all">Tất cả</option>
                    <option value="1">1 sao</option>
                    <option value="2">2 sao</option>
                    <option value="3">3 sao</option>
                    <option value="4">4 sao</option>
                    <option value="5">5 sao</option>
                </select>
            </div>

            <div class="col-md-2">
                <label class="form-label">&nbsp;</label>
                <div class="d-flex gap-2">
                    <button type="button" id="searchBtn" class="btn btn-primary">
                        <i class="fas fa-search"></i> Tìm kiếm
                    </button>
                    <button type="button" id="viewHistoryBtn" class="btn btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#historyModal">
                        <i class="fas fa-history"></i> Lịch sử
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Feedback Content Row --}}
    <div class="feedback-content-row">
        <div class="row">
            {{-- Chờ Duyệt Column --}}
            <div class="col-md-6">
                <div class="feedback-column">
                    <div class="column-header pending-header">
                        <h4>
                            <i class="fas fa-clock"></i>
                            Chờ Duyệt
                            <span class="badge" id="pendingCount">0</span>
                        </h4>
                    </div>
                    <div class="cards-container">
                        <div class="cards-grid" id="pendingFeedbacks">
                            <div class="empty-state">
                                <i class="fas fa-inbox"></i>
                                <p>Chưa có feedback chờ duyệt</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Đã Duyệt Column --}}
            <div class="col-md-6">
                <div class="feedback-column">
                    <div class="column-header approved-header">
                        <h4>
                            <i class="fas fa-check-circle"></i>
                            Đã Duyệt
                            <span class="badge" id="approvedCount">0</span>
                        </h4>
                    </div>
                    <div class="cards-container">
                        <div class="cards-grid" id="approvedFeedbacks">
                            <div class="empty-state">
                                <i class="fas fa-inbox"></i>
                                <p>Chưa có feedback đã duyệt</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- History Modal --}}
<div class="modal fade" id="historyModal" tabindex="-1" aria-labelledby="historyModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="historyModalLabel">
                    <i class="fas fa-history me-2"></i>Lịch sử feedback đã hủy bỏ
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="cancelledFeedbacks">
                    <div class="text-center py-4">
                        <div class="spinner-border" role="status">
                            <span class="visually-hidden">Đang tải...</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Loading Overlay --}}
<div class="loading-overlay" id="loadingOverlay">
    <div class="spinner"></div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // DOM elements
    const userSearch = document.getElementById('userSearch');
    const userSuggestions = document.getElementById('userSuggestions');
    const selectedUserId = document.getElementById('selectedUserId');
    const feedbackDate = document.getElementById('feedbackDate');
    const ratingFilter = document.getElementById('ratingFilter');
    const searchBtn = document.getElementById('searchBtn');
    const viewHistoryBtn = document.getElementById('viewHistoryBtn');

    const pendingContainer = document.getElementById('pendingFeedbacks');
    const approvedContainer = document.getElementById('approvedFeedbacks');
    const pendingCount = document.getElementById('pendingCount');
    const approvedCount = document.getElementById('approvedCount');

    // Initialize
    loadFeedbacks();

    // User search functionality
    userSearch.addEventListener('input', function() {
        const searchTerm = this.value.toLowerCase().trim();
        filterUserSuggestions(searchTerm);
        if (searchTerm.length > 0) {
            userSuggestions.style.display = 'block';
        } else {
            userSuggestions.style.display = 'none';
            selectedUserId.value = '';
        }
    });

    // User suggestion selection
    document.querySelectorAll('.suggestion-item').forEach(item => {
        item.addEventListener('click', function() {
            const userId = this.getAttribute('data-user-id');
            const userName = this.getAttribute('data-user-name');
            const userRole = this.getAttribute('data-user-role');

            userSearch.value = userName + ' (' + userRole + ')';
            selectedUserId.value = userId;
            userSuggestions.style.display = 'none';
        });
    });

    // Hide suggestions when clicking outside
    document.addEventListener('click', function(e) {
        if (!userSearch.contains(e.target) && !userSuggestions.contains(e.target)) {
            userSuggestions.style.display = 'none';
        }
    });

    // Search button
    searchBtn.addEventListener('click', function() {
        loadFeedbacks();
    });

    // View history button
    viewHistoryBtn.addEventListener('click', function() {
        loadCancelledHistory();
    });

    // Filter user suggestions
    function filterUserSuggestions(searchTerm) {
        const suggestions = document.querySelectorAll('.suggestion-item');
        suggestions.forEach(suggestion => {
            const userName = suggestion.getAttribute('data-user-name').toLowerCase();
            if (userName.includes(searchTerm)) {
                suggestion.style.display = 'block';
            } else {
                suggestion.style.display = 'none';
            }
        });
    }

    // Load feedbacks
    function loadFeedbacks() {
        showLoading();

        const params = new URLSearchParams();
        if (selectedUserId.value) params.append('user_id', selectedUserId.value);
        if (feedbackDate.value) params.append('date', feedbackDate.value);
        if (ratingFilter.value !== 'all') params.append('rating', ratingFilter.value);

                fetch(`{{ route('admin.feedback.search') }}?${params}`)
            .then(response => response.json())
            .then(data => {
                hideLoading();
                renderFeedbacks(data.pending || [], data.approved || []);
                updateCounts(data.pending?.length || 0, data.approved?.length || 0);
            })
            .catch(error => {
                hideLoading();
                console.error('Error:', error);
                showError('Có lỗi xảy ra khi tải dữ liệu');
            });
    }

    // Load cancelled history
    function loadCancelledHistory() {
        const modalBody = document.querySelector('#cancelledFeedbacks');
        modalBody.innerHTML = '<div class="text-center py-4"><div class="spinner-border"></div></div>';

        fetch(`{{ route('admin.feedback.cancelled') }}`)
            .then(response => response.json())
            .then(data => {
                renderCancelledHistory(data.cancelled || []);
            })
            .catch(error => {
                modalBody.innerHTML = '<div class="alert alert-danger">Không thể tải lịch sử</div>';
            });
    }

    // Render feedbacks
    function renderFeedbacks(pending, approved) {
        renderFeedbackList(pending, pendingContainer, 'pending');
        renderFeedbackList(approved, approvedContainer, 'approved');
    }

    // Render feedback list
    function renderFeedbackList(feedbacks, container, type) {
        if (feedbacks.length === 0) {
            container.innerHTML = `
                <div class="empty-state">
                    <i class="fas fa-inbox"></i>
                    <p>Không có feedback ${type === 'pending' ? 'chờ duyệt' : 'đã duyệt'}</p>
                </div>
            `;
            return;
        }

        container.innerHTML = feedbacks.map(feedback => createFeedbackCard(feedback, type)).join('');

        // Add event listeners to action buttons
        container.querySelectorAll('.approve-btn').forEach(btn => {
            btn.addEventListener('click', () => approveFeedback(btn.getAttribute('data-id')));
        });

        container.querySelectorAll('.reject-btn').forEach(btn => {
            btn.addEventListener('click', () => rejectFeedback(btn.getAttribute('data-id')));
        });
    }

    // Create feedback card HTML
    function createFeedbackCard(feedback, type) {
        // Ưu tiên quan hệ user_cus -> user, nếu không có thì dùng customer_user
        const customerName = (feedback.user__cus && feedback.user__cus.user) ? feedback.user__cus.user.Name :
                            (feedback.customer_user ? feedback.customer_user.Name : 'N/A');
        // Ưu tiên quan hệ user_agent -> user, nếu không có thì dùng agent_user
        const agentName = (feedback.user__agent && feedback.user__agent.user) ? feedback.user__agent.user.Name :
                         (feedback.agent_user ? feedback.agent_user.Name : 'N/A');
        const rating = parseFloat(feedback.Rating) || 0;

        // Tạo các sao với hỗ trợ nửa sao
        const renderStars = (rating) => {
            let stars = '';
            for (let i = 1; i <= 5; i++) {
                if (rating >= i) {
                    stars += '<span class="fas fa-star active"></span>';
                } else if (rating >= i - 0.5) {
                    stars += '<span class="fas fa-star-half-alt active"></span>';
                } else {
                    stars += '<span class="far fa-star"></span>';
                }
            }
            return stars;
        };

        return `
            <div class="feedback-card ${type}-card">
                <div class="card-header">
                    <div class="customer-info">
                        <strong>${customerName}</strong>
                        <small class="text-muted">${feedback.FeedbackDate || 'N/A'}</small>
                    </div>
                    <div class="rating">
                        ${renderStars(rating)}
                        <span class="rating-value">${rating}/5</span>
                    </div>
                </div>
                <div class="card-body">
                    <div class="feedback-title">${feedback.Title || 'Không có tiêu đề'}</div>
                    <div class="feedback-comment">${feedback.Comment || 'Không có bình luận'}</div>
                    <div class="agent-info">
                        <i class="fas fa-user-tie"></i>
                        <span>${agentName}</span>
                    </div>
                </div>
                <div class="card-actions">
                    ${type === 'pending' ? `
                        <button class="btn btn-success approve-btn" data-id="${feedback.FeedbackID}">
                            <i class="fas fa-check"></i> Duyệt
                        </button>
                        <button class="btn btn-danger reject-btn" data-id="${feedback.FeedbackID}">
                            <i class="fas fa-times"></i> Từ chối
                        </button>
                    ` : `
                        <button class="btn btn-danger reject-btn" data-id="${feedback.FeedbackID}">
                            <i class="fas fa-times"></i> Hủy duyệt
                        </button>
                    `}
                </div>
            </div>
        `;
    }

    // Render cancelled history
    function renderCancelledHistory(cancelled) {
        const container = document.getElementById('cancelledFeedbacks');

        if (cancelled.length === 0) {
            container.innerHTML = '<div class="text-center text-muted">Không có feedback nào bị hủy bỏ</div>';
            return;
        }

        container.innerHTML = cancelled.map(feedback => {
            // Ưu tiên quan hệ user_cus -> user, nếu không có thì dùng customer_user
            const customerName = (feedback.user__cus && feedback.user__cus.user) ? feedback.user__cus.user.Name :
                                (feedback.customer_user ? feedback.customer_user.Name : 'N/A');
            // Ưu tiên quan hệ user_agent -> user, nếu không có thì dùng agent_user
            const agentName = (feedback.user__agent && feedback.user__agent.user) ? feedback.user__agent.user.Name :
                             (feedback.agent_user ? feedback.agent_user.Name : 'N/A');
            const rating = parseFloat(feedback.Rating) || 0;

            // Tạo đánh giá sao
            const renderStarRating = (rating) => {
                let stars = '';
                for (let i = 1; i <= 5; i++) {
                    if (rating >= i) {
                        stars += '<i class="fas fa-star text-warning"></i>';
                    } else if (rating >= i - 0.5) {
                        stars += '<i class="fas fa-star-half-alt text-warning"></i>';
                    } else {
                        stars += '<i class="far fa-star text-warning"></i>';
                    }
                }
                return stars;
            };

            return `
                <div class="card mb-2">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3">
                                <strong>${customerName}</strong><br>
                                <small class="text-muted">${feedback.FeedbackDate || 'N/A'}</small>
                            </div>
                            <div class="col-md-2">
                                <div>${renderStarRating(rating)} <small>(${rating}/5)</small></div>
                            </div>
                            <div class="col-md-4">
                                <div class="text-truncate">${feedback.Title || 'Không có tiêu đề'}</div>
                                <small class="text-muted">${agentName}</small>
                            </div>
                            <div class="col-md-3">
                                <span class="badge bg-danger">Đã hủy bỏ</span>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        }).join('');
    }

    // Approve feedback
    function approveFeedback(feedbackId) {
        if (!confirm('Bạn có chắc chắn muốn duyệt feedback này?')) return;

        updateFeedbackStatus(feedbackId, 'Đã duyệt');
    }

    // Reject feedback
    function rejectFeedback(feedbackId) {
        if (!confirm('Bạn có chắc chắn muốn từ chối feedback này?')) return;

        updateFeedbackStatus(feedbackId, 'Hủy bỏ');
    }

    // Update feedback status
    function updateFeedbackStatus(feedbackId, status) {
        showLoading();

        const formData = new FormData();
        formData.append('status', status);
        formData.append('_token', '{{ csrf_token() }}');
        formData.append('_method', 'PATCH');

        fetch(`{{ url('admin/feedback') }}/${feedbackId}/status`, {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            hideLoading();
            if (data.success) {
                loadFeedbacks(); // Reload data
                showSuccess(data.message || 'Cập nhật thành công');
            } else {
                showError(data.message || 'Có lỗi xảy ra');
            }
        })
        .catch(error => {
            hideLoading();
            showError('Có lỗi xảy ra khi cập nhật');
        });
    }

    // Update counts
    function updateCounts(pendingNum, approvedNum) {
        pendingCount.textContent = pendingNum;
        approvedCount.textContent = approvedNum;
    }

    // Show/hide loading
    function showLoading() {
        document.getElementById('loadingOverlay').style.display = 'flex';
    }

    function hideLoading() {
        document.getElementById('loadingOverlay').style.display = 'none';
    }

    // Show messages
    function showSuccess(message) {
        // You can implement toast notifications here
        alert(message);
    }

    function showError(message) {
        // You can implement toast notifications here
        alert(message);
    }
});
</script>

@endSection()
