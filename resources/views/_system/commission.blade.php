@extends('_layout._layadmin.app')
@section('commission')
@if(isset($error))
    <div class="alert alert-danger">
        {{ $error }}
    </div>
@else
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-percentage me-2"></i>Quản lý Hoa hồng
        </h1>

        <!-- Search Form -->
        <div class="search-box">
            <form id="searchCommissionForm" class="d-flex align-items-center">
                <input type="date" id="searchDate" class="form-control me-2" placeholder="Tìm theo ngày thanh toán">
                <button type="submit" class="btn btn-outline-primary">
                    <i class="fa fa-search"></i>
                </button>

            </form>
        </div>
    </div>

    <!-- Tabs Navigation -->
    <ul class="nav nav-tabs mb-4" id="commissionTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="sale-tab" data-bs-toggle="tab" data-bs-target="#sale-tab-pane"
                    type="button" role="tab" aria-controls="sale-tab-pane" aria-selected="true">
                <i class="fas fa-home me-2"></i>Hoa hồng Bán ({{ isset($saleCommissions) ? $saleCommissions->count() : 0 }})
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="rent-tab" data-bs-toggle="tab" data-bs-target="#rent-tab-pane"
                    type="button" role="tab" aria-controls="rent-tab-pane" aria-selected="false">
                <i class="fas fa-key me-2"></i>Hoa hồng Thuê ({{ isset($rentCommissions) ? $rentCommissions->count() : 0 }})
            </button>
        </li>
    </ul>

    <!-- Tabs Content -->
    <div class="tab-content" id="commissionTabsContent">
        <!-- Sale Tab -->
        <div class="tab-pane fade show active" id="sale-tab-pane" role="tabpanel" aria-labelledby="sale-tab">
            <div id="sale-commission-container">
                @if(isset($saleCommissions))
                    @include('_system.partialview.commission_tab_content', ['commissions' => $saleCommissions, 'columns' => $columns, 'type' => 'Sale'])
                @else
                    <div class="alert alert-warning">Không có dữ liệu Sale Commission</div>
                @endif
            </div>
        </div>

        <!-- Rent Tab -->
        <div class="tab-pane fade" id="rent-tab-pane" role="tabpanel" aria-labelledby="rent-tab">
            <div id="rent-commission-container">
                @if(isset($rentCommissions))
                    @include('_system.partialview.commission_tab_content', ['commissions' => $rentCommissions, 'columns' => $columns, 'type' => 'Rent'])
                @else
                    <div class="alert alert-warning">Không có dữ liệu Rent Commission</div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Commission Detail Modal - Bootstrap 5 Compatible -->
<div class="modal fade" id="commissionDetailModal" tabindex="-1" aria-labelledby="commissionDetailModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <!-- Default modal content before AJAX loads -->
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="commissionDetailModalLabel">
                    <i class="fas fa-info-circle me-2"></i>Chi tiết hoa hồng
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center py-5">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Đang tải...</span>
                </div>
                <p class="mt-3 text-muted">Đang tải thông tin chi tiết...</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<!-- jQuery CDN - Required for commission functionality -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<!-- Chart.js for Revenue Analytics -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.js"></script>

<style>
/* ===== MODERN COMMISSION SYSTEM STYLES ===== */

/* Statistics Cards */
.stats-card {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 16px;
    padding: 24px;
    color: white;
    border: none;
    box-shadow: 0 8px 25px rgba(0,0,0,0.1);
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    position: relative;
    overflow: hidden;
}

.stats-card::before {
    content: '';
    position: absolute;
    top: 0;
    right: 0;
    width: 100px;
    height: 100px;
    background: rgba(255,255,255,0.1);
    border-radius: 50%;
    transform: translate(30px, -30px);
}

.stats-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 12px 35px rgba(0,0,0,0.15);
}

.stats-card.primary-gradient {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

.stats-card.success-gradient {
    background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
}

.stats-card.warning-gradient {
    background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);
}

.stats-icon {
    position: absolute;
    top: 20px;
    right: 20px;
    width: 50px;
    height: 50px;
    background: rgba(255,255,255,0.2);
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 2;
}

.stats-icon i {
    font-size: 24px;
    color: white;
}

.stats-content {
    position: relative;
    z-index: 2;
}

.stats-number {
    font-size: 2.5rem;
    font-weight: 800;
    margin: 0;
    line-height: 1;
}

.stats-label {
    font-size: 1rem;
    margin: 8px 0 4px;
    font-weight: 600;
    opacity: 0.9;
}

.stats-badge {
    display: inline-block;
    background: rgba(255,255,255,0.2);
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 0.8rem;
    font-weight: 500;
}

/* Empty State */
.empty-state-wrapper {
    display: flex;
    justify-content: center;
    align-items: center;
    min-height: 400px;
}

.empty-state-card {
    text-align: center;
    padding: 60px 40px;
    background: white;
    border-radius: 20px;
    box-shadow: 0 8px 25px rgba(0,0,0,0.06);
    border: 1px solid #f0f0f0;
}

.empty-icon {
    width: 80px;
    height: 80px;
    margin: 0 auto 20px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
}

.empty-icon i {
    font-size: 36px;
    color: white;
}

.empty-title {
    color: #2d3748;
    font-weight: 700;
    font-size: 1.5rem;
    margin-bottom: 12px;
}

.empty-description {
    color: #64748b;
    font-size: 1rem;
    margin: 0;
    line-height: 1.6;
}

/* Commission List Panel */
.commission-list-panel {
    background: white;
    border-radius: 20px;
    box-shadow: 0 8px 25px rgba(0,0,0,0.06);
    border: 1px solid #f0f0f0;
    overflow: hidden;
}

.panel-header {
    background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
    padding: 24px;
    border-bottom: 1px solid #e2e8f0;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.panel-title {
    font-size: 1.25rem;
    font-weight: 700;
    color: #2d3748;
    margin: 0;
}

.panel-count {
    background: #667eea;
    color: white;
    padding: 6px 16px;
    border-radius: 20px;
    font-size: 0.9rem;
    font-weight: 600;
}

.commission-list-scroll {
    max-height: 600px;
    overflow-y: auto;
    padding: 8px;
}

.commission-list-scroll::-webkit-scrollbar {
    width: 6px;
}

.commission-list-scroll::-webkit-scrollbar-track {
    background: #f1f5f9;
    border-radius: 10px;
}

.commission-list-scroll::-webkit-scrollbar-thumb {
    background: #cbd5e1;
    border-radius: 10px;
}

.commission-list-scroll::-webkit-scrollbar-thumb:hover {
    background: #94a3b8;
}

/* Commission Cards */
.commission-card {
    background: white;
    border: 2px solid #f1f5f9;
    border-radius: 16px;
    margin-bottom: 12px;
    padding: 20px;
    cursor: pointer;
    transition: all 0.3s ease;
    position: relative;
}

.commission-card:hover {
    border-color: #667eea;
    box-shadow: 0 8px 25px rgba(102, 126, 234, 0.15);
    transform: translateY(-2px);
}

.commission-card.selected {
    border-color: #667eea;
    background: linear-gradient(135deg, #667eea08 0%, #764ba208 100%);
    box-shadow: 0 8px 25px rgba(102, 126, 234, 0.2);
}

.commission-card-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 16px;
}

.commission-id-section {
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.commission-id {
    font-size: 1.1rem;
    font-weight: 700;
    color: #2d3748;
}

.commission-type-badge {
    display: inline-block;
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 0.8rem;
    font-weight: 600;
    text-transform: uppercase;
}

.commission-type-badge.sale {
    background: linear-gradient(135deg, #10b981 0%, #34d399 100%);
    color: white;
}

.commission-type-badge.rent {
    background: linear-gradient(135deg, #3b82f6 0%, #60a5fa 100%);
    color: white;
}

.commission-amount-section {
    text-align: right;
}

.amount-main {
    display: block;
    font-size: 1.5rem;
    font-weight: 800;
    color: #059669;
    line-height: 1;
}

.amount-percentage {
    display: block;
    font-size: 0.9rem;
    color: #667eea;
    font-weight: 600;
    margin-top: 4px;
}

.commission-card-body {
    margin-bottom: 16px;
}

.agent-section {
    background: #f8fafc;
    border-radius: 12px;
    padding: 16px;
}

.agent-name {
    font-size: 1rem;
    font-weight: 600;
    color: #2d3748;
    margin-bottom: 8px;
    display: flex;
    align-items: center;
}

.agent-name.no-agent {
    color: #64748b;
    font-style: italic;
}

.agent-contact {
    font-size: 0.9rem;
    color: #64748b;
    display: flex;
    align-items: center;
}

.commission-card-footer {
    border-top: 1px solid #f1f5f9;
    padding-top: 16px;
}

.payment-status {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.status-paid {
    color: #10b981;
    font-weight: 600;
    display: flex;
    align-items: center;
}

.status-pending {
    color: #f59e0b;
    font-weight: 600;
    display: flex;
    align-items: center;
}

.payment-date {
    color: #64748b;
    font-size: 0.85rem;
}

/* Detail Panel */
.detail-panel {
    background: white;
    border-radius: 20px;
    box-shadow: 0 8px 25px rgba(0,0,0,0.06);
    border: 1px solid #f0f0f0;
    min-height: 600px;
    display: flex;
    flex-direction: column;
}

/* No Selection State */
.no-selection-state {
    display: flex;
    align-items: center;
    justify-content: center;
    flex: 1;
    padding: 60px 40px;
}

.no-selection-content {
    text-align: center;
}

.no-selection-icon {
    width: 80px;
    height: 80px;
    margin: 0 auto 24px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
}

.no-selection-icon i {
    font-size: 36px;
    color: white;
}

.no-selection-title {
    font-size: 1.5rem;
    font-weight: 700;
    color: #2d3748;
    margin-bottom: 12px;
}

.no-selection-description {
    color: #64748b;
    font-size: 1rem;
    line-height: 1.6;
    margin: 0;
}

/* Detail Content */
.detail-content {
    padding: 24px;
    flex: 1;
}

.detail-info-card {
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 16px;
    margin-bottom: 20px;
    overflow: hidden;
}

.info-card-header {
    background: white;
    padding: 20px 24px;
    border-bottom: 1px solid #e2e8f0;
}

.info-card-title {
    font-size: 1.1rem;
    font-weight: 700;
    color: #2d3748;
    margin: 0;
    display: flex;
    align-items: center;
}

.info-card-body {
    padding: 24px;
    background: white;
}

.info-card-body .table {
    margin: 0;
}

.info-card-body .table td {
    padding: 12px 0;
    border-top: 1px solid #f1f5f9;
    vertical-align: middle;
}

.info-card-body .table tr:first-child td {
    border-top: none;
}

.info-card-body .fw-bold.text-muted {
    color: #64748b !important;
    font-weight: 600 !important;
    width: 40%;
}

/* Responsive Design */
@media (max-width: 991.98px) {
    .stats-card {
        margin-bottom: 16px;
    }

    .commission-list-panel,
    .detail-panel {
        margin-bottom: 24px;
    }

    .panel-header {
        padding: 20px;
    }

    .commission-card {
        padding: 16px;
    }

    .detail-content {
        padding: 20px;
    }
}

@media (max-width: 767.98px) {
    .stats-number {
        font-size: 2rem;
    }

    .commission-card-header {
        flex-direction: column;
        align-items: flex-start;
        gap: 12px;
    }

    .commission-amount-section {
        text-align: left;
    }

    .amount-main {
        font-size: 1.25rem;
    }
}

/* Search box styling */
.search-box .form-control {
    border: 2px solid #e2e8f0;
    border-radius: 12px;
    padding: 12px 16px;
    font-size: 0.95rem;
    transition: all 0.3s ease;
}

.search-box .form-control:focus {
    border-color: #667eea;
    box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
}

.search-box .btn {
    border-radius: 12px;
    padding: 12px 20px;
    font-weight: 600;
}

/* Tab styling */
.nav-tabs {
    border-bottom: 2px solid #e2e8f0;
}

.nav-tabs .nav-link {
    border: none;
    border-radius: 12px 12px 0 0;
    padding: 16px 24px;
    font-weight: 600;
    color: #64748b;
    background: #f8fafc;
    margin-right: 8px;
    transition: all 0.3s ease;
}

.nav-tabs .nav-link.active {
    background: white;
    color: #667eea;
    border-bottom: 3px solid #667eea;
}

.nav-tabs .nav-link:hover {
    color: #667eea;
}

/* Animation for cards */
@keyframes slideInUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.commission-card,
.detail-info-card {
    animation: slideInUp 0.4s ease-out;
}        /* Modern Admin Dashboard Styles */
        .admin-dashboard-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 25px;
        }

        /* KPI Cards */
        .kpi-card {
            transition: all 0.3s ease;
            border-radius: 12px;
            overflow: hidden;
        }

        .kpi-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.15) !important;
        }

        .kpi-icon {
            width: 56px;
            height: 56px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 12px;
        }

        .trend-indicator {
            font-size: 0.75rem;
        }

        .trend-indicator i {
            font-size: 0.7rem;
        }

        /* Performance Metrics */
        .performance-metric {
            position: relative;
        }

        .progress {
            border-radius: 10px;
            overflow: hidden;
        }

        .progress-bar {
            border-radius: 10px;
        }

        .bg-gradient-primary {
            background: linear-gradient(45deg, #667eea, #764ba2) !important;
        }

        .bg-gradient-success {
            background: linear-gradient(45deg, #56ab2f, #a8e6cf) !important;
        }

        .bg-gradient-info {
            background: linear-gradient(45deg, #2196f3, #21cbf3) !important;
        }

        /* Revenue Breakdown */
        .revenue-breakdown-summary {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
        }

        .breakdown-item {
            padding: 4px 0;
        }

        /* Chart Container */
        .revenue-chart-container {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            border-radius: 8px;
            padding: 20px;
        }

        /* Admin Table Styles */
        .table > :not(caption) > * > * {
            padding: 12px 8px;
            border-bottom: 1px solid #e9ecef;
        }

        .table thead th {
            background: #f8f9fa;
            border-bottom: 2px solid #dee2e6;
            font-weight: 600;
            color: #495057;
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .table tbody tr {
            transition: all 0.2s ease;
        }

        .table tbody tr:hover {
            background-color: #f8f9fa;
            transform: scale(1.01);
        }

        .agent-avatar {
            transition: all 0.3s ease;
        }

        .agent-avatar:hover {
            transform: scale(1.1);
            background: #667eea !important;
        }

        /* Badge Styles */
        .badge {
            font-size: 0.75rem;
            padding: 6px 10px;
            border-radius: 6px;
        }

        /* Button Group Styles */
        .btn-group-sm .btn {
            padding: 4px 8px;
            font-size: 0.75rem;
        }

        /* Analytics Cards Enhancement */
        .card {
            border-radius: 12px;
            border: none;
        }

        .card-header {
            border-radius: 12px 12px 0 0 !important;
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        }

        .card-title {
            font-size: 0.95rem;
            font-weight: 600;
        }

        /* Revenue Chart Styling */
        #revenueChart {
            border-radius: 8px;
        }

        /* Modern Button Styles */
        .btn-outline-primary {
            border-color: #667eea;
            color: #667eea;
        }

        .btn-outline-primary:hover {
            background: #667eea;
            border-color: #667eea;
        }

        .btn-outline-primary.active {
            background: #667eea;
            border-color: #667eea;
        }

        /* Responsive Enhancements */
        @media (max-width: 768px) {
            .admin-dashboard-header {
                padding: 15px;
                text-align: center;
            }

            .admin-dashboard-header .d-flex {
                flex-direction: column;
                gap: 15px;
            }

            .kpi-card {
                margin-bottom: 15px;
            }

            .table-responsive {
                font-size: 0.85rem;
            }
        }

        /* Additional Analytics Styling */
        .analytics-filters .btn {
            font-size: 0.8rem;
            padding: 6px 12px;
        }

        .revenue-breakdown-summary h6 {
            color: #495057;
            font-weight: 600;
            margin-bottom: 15px;
        }

        /* Enhanced Card Animations */
        .card {
            animation: fadeInUp 0.6s ease-out;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Modal Button Styles */
        .btn-view-modal {
            transition: all 0.3s ease;
        }

        .btn-view-modal:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.15);
        }

        /* Modal Fixes for Bootstrap 5 */
        .modal-backdrop {
            z-index: 1040 !important;
            background-color: rgba(0, 0, 0, 0.5) !important;
            opacity: 1 !important; /* Ensure backdrop is visible */
        }

        /* Core Modal Styles */
        .modal {
            z-index: 1050 !important;
        }

        .modal.fade .modal-dialog {
            transition: transform 0.3s ease-out !important;
            transform: translate(0, -50px) !important;
        }

        .modal.show .modal-dialog {
            transform: none !important;
        }

        .modal-dialog {
            max-width: 96% !important;
            margin: 1.75rem auto !important;
        }

        .modal-content {
            border-radius: 0.5rem !important;
            border: none !important;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.2) !important;
        }

        .modal-xl {
            max-width: 1140px !important;
        }

        /* Modal Header Styles */
        .modal-header {
            border-bottom: 1px solid rgba(0, 0, 0, 0.1) !important;
            padding: 1rem 1.5rem !important;
        }

        .modal-header.bg-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
        }

        /* Modal Body Styles */
        .modal-body {
            padding: 1.5rem !important;
        }

        /* Modal Footer Styles */
        .modal-footer {
            border-top: 1px solid rgba(0, 0, 0, 0.1) !important;
            padding: 1rem 1.5rem !important;
        }

        /* Modal Spinner */
        .spinner-border {
            width: 3rem !important;
            height: 3rem !important;
            border-width: 0.25rem !important;
        }

        /* Fix for body padding when modal is open */
        body.modal-open {
            padding-right: 17px !important;
            overflow: hidden !important;
        }

        /* Nice animation when modal opens */
        .modal.show .modal-content {
            animation: fadeInDown 0.3s ease-out !important;
        }

        @keyframes fadeInDown {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: scaleY(0);
            }
            to {
                opacity: 1;
                transform: scaleY(1);
            }
        }



        /* Performance Metric Styling */
        .performance-metric .progress {
            background-color: #e9ecef;
        }

        .performance-metric small {
            font-size: 0.7rem;
            color: #6c757d;
        }



        /* ===== TABLE COLUMN WIDTH TOGGLE STYLES ===== */

        /* Default Compact Mode */
        #commissionTable.compact .col-commission-id {
            width: 8%;
        }

        #commissionTable.compact .col-agent {
            width: 15%;
        }

        #commissionTable.compact .col-property {
            width: 20%;
        }

        #commissionTable.compact .col-transaction {
            width: 15%;
        }

        #commissionTable.compact .col-commission {
            width: 12%;
        }

        #commissionTable.compact .col-status {
            width: 10%;
        }

        #commissionTable.compact .col-date {
            width: 10%;
        }

        #commissionTable.compact .col-actions {
            width: 10%;
        }

        /* Expanded Mode */
        #commissionTable.expanded .col-commission-id {
            width: 6%;
        }

        #commissionTable.expanded .col-agent {
            width: 22%;
        }

        #commissionTable.expanded .col-property {
            width: 28%;
        }

        #commissionTable.expanded .col-transaction {
            width: 18%;
        }

        #commissionTable.expanded .col-commission {
            width: 10%;
        }

        #commissionTable.expanded .col-status {
            width: 8%;
        }

        #commissionTable.expanded .col-date {
            width: 8%;
        }

        /* Hidden elements in compact mode */
        #commissionTable.compact .agent-email,
        #commissionTable.compact .property-title-full,
        #commissionTable.compact .property-address-full,
        #commissionTable.compact .transaction-type {
            display: none !important;
        }

        /* Show elements in expanded mode */
        #commissionTable.expanded .agent-email,
        #commissionTable.expanded .property-title-full,
        #commissionTable.expanded .property-address-full,
        #commissionTable.expanded .transaction-type {
            display: block !important;
        }

        /* Hide elements in expanded mode */
        #commissionTable.expanded .property-title-short,
        #commissionTable.expanded .property-address-short {
            display: none !important;
        }

        /* Table transitions */
        #commissionTable {
            transition: all 0.3s ease;
        }

        #commissionTable th,
        #commissionTable td {
            transition: width 0.3s ease;
        }

        /* Toggle button states */
        #toggleTableWidth.expanded i {
            transform: rotate(45deg);
        }

        #toggleTableWidth {
            transition: all 0.3s ease;
        }
</style>

<script>
// Debug Chart.js loading
window.addEventListener('load', function() {
    console.log('Page loaded, checking Chart.js...');
    if (typeof Chart !== 'undefined') {
        console.log('Chart.js is available:', Chart.version);
    } else {
        console.error('Chart.js is not available');
    }
});

// Global function to setup modal button handlers (can be called multiple times)
function setupModalButtonHandlers() {
    console.log('Checking for modal buttons...');
    console.log('Number of modal buttons found:', $('.btn-view-modal').length);

    // Debug: Print all commission IDs found
    $('.btn-view-modal').each(function(index) {
        const commissionId = $(this).data('commission-id');
        console.log('Modal Button', index + 1, 'Commission ID:', commissionId);
        if (!commissionId) {
            console.error('Button without commission ID found:', this);
        }
    });

    // Test if buttons exist in DOM
    if ($('.btn-view-modal').length === 0) {
        console.error('No .btn-view-modal buttons found in DOM!');
        console.log('Available buttons:', $('button').length);
        console.log('Buttons with data-commission-id:', $('[data-commission-id]').length);
    }
}

// Function to initialize the detail modal
function initCommissionDetailModal() {
    console.log('Initializing modal system...');

    // Check if modal element exists
    const $modal = $('#commissionDetailModal');
    if ($modal.length === 0) {
        console.error('Modal element not found in DOM!');
        return;
    }

    // Log found modal
    console.log('Found modal element:', $modal[0]);

    // Initialize all modals with Bootstrap 5
    if (typeof bootstrap !== 'undefined') {
        // Create a new modal instance
        const modalElement = $modal[0];
        const modalInstance = new bootstrap.Modal(modalElement, {
            backdrop: true,
            keyboard: true,
            focus: true
        });

        console.log('Modal initialized with Bootstrap 5');

        // Store the instance on the element
        $modal.data('bs-modal', modalInstance);
    } else {
        console.error('Bootstrap is not available for modal initialization!');
    }

    return $modal;
}

$(document).ready(function() {
    console.log('Modern Admin Commission System Initialized');
    console.log('jQuery version:', $.fn.jquery);
    console.log('Bootstrap version:', typeof bootstrap !== 'undefined' ? bootstrap.Modal.VERSION : 'Not loaded');

    // Setup CSRF token for AJAX requests
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    // STEP 1: Initialize Chart.js FIRST (to avoid DOM conflicts)
    initializeChartAndThenModal();

    function initializeChartAndThenModal() {
        console.log('Step 1: Initializing Chart.js first...');

        // Wait for Chart.js to be available and initialize it
        function waitForChartThenInitModal() {
            if (typeof Chart !== 'undefined') {
                console.log('Chart.js is available, initializing charts...');
                initializeRevenueChart();

                // STEP 2: Initialize modal system AFTER chart is ready
                setTimeout(function() {
                    console.log('Step 2: Initializing modal system...');
                    const $commissionModal = initCommissionDetailModal();

                    // STEP 3: Setup modal button handlers AFTER modal is ready
                    setTimeout(function() {
                        console.log('Step 3: Setting up modal button handlers...');
                        setupModalButtonHandlers();
                    }, 500);
                }, 300);
            } else {
                console.log('Waiting for Chart.js to load...');
                setTimeout(waitForChartThenInitModal, 100);
            }
        }

        waitForChartThenInitModal();
    }

    function initializeRevenueChart() {
        const canvas = document.getElementById('revenueChart');
        if (!canvas) {
            console.log('Revenue chart canvas not found');
            return;
        }

        // Check if Chart.js is loaded
        if (typeof Chart === 'undefined') {
            console.error('Chart.js is not loaded');
            return;
        }

        // Destroy existing chart if exists
        if (window.revenueChartInstance) {
            window.revenueChartInstance.destroy();
        }

        const ctx = canvas.getContext('2d');

        // Sample data - would be replaced with real data from backend
        const data = {
            labels: ['Tháng 1', 'Tháng 2', 'Tháng 3', 'Tháng 4', 'Tháng 5', 'Tháng 6', 'Tháng 7'],
            datasets: [{
                label: 'Doanh thu',
                data: [65000000, 85000000, 75000000, 95000000, 110000000, 90000000, 120000000],
                backgroundColor: 'rgba(102, 126, 234, 0.1)',
                borderColor: '#667eea',
                borderWidth: 3,
                fill: true,
                tension: 0.4
            }, {
                label: 'Hoa hồng',
                data: [3250000, 4250000, 3750000, 4750000, 5500000, 4500000, 6000000],
                backgroundColor: 'rgba(40, 167, 69, 0.1)',
                borderColor: '#28a745',
                borderWidth: 3,
                fill: true,
                tension: 0.4
            }]
        };

        const config = {
            type: 'line',
            data: data,
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    title: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return new Intl.NumberFormat('vi-VN', {
                                    style: 'currency',
                                    currency: 'VND',
                                    minimumFractionDigits: 0
                                }).format(value);
                            }
                        }
                    }
                },
                interaction: {
                    intersect: false,
                    mode: 'index'
                }
            }
        };

        try {
            window.revenueChartInstance = new Chart(ctx, config);
            console.log('Revenue chart initialized successfully');
        } catch (error) {
            console.error('Error initializing chart:', error);
        }
    }    // Handle period filter buttons
    $(document).on('click', '[data-period]', function() {
        const period = $(this).data('period');
        const $btnGroup = $(this).closest('.btn-group');

        // Update active state
        $btnGroup.find('.btn').removeClass('active');
        $(this).addClass('active');

        console.log('Period changed to:', period);
        // Here you would typically reload chart data for the selected period
    });

    // Handle Commission Detail Modal - Simplified version
    $(document).on('click', '.btn-view-modal', function(e) {
        e.preventDefault();

        const commissionId = $(this).attr('data-commission-id');
        console.log('Opening modal for commission ID:', commissionId);

        if (!commissionId) {
            alert('Không tìm thấy mã hoa hồng!');
            return;
        }

        // Show modal with loading
        const $modal = $('#commissionDetailModal');
        const modalElement = $modal[0];
        const $modalContent = $modal.find('.modal-content');

        $modalContent.html(`
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">
                    <i class="fas fa-info-circle me-2"></i>Chi tiết hoa hồng #${commissionId}
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center py-5">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Đang tải...</span>
                </div>
                <p class="mt-3 text-muted">Đang tải thông tin chi tiết...</p>
            </div>
        `);

        // Initialize and show modal immediately
        $modal.modal('show');
        console.log('Modal shown using jQuery modal method');

        // Load commission details via AJAX with timeout
        $.ajax({
            url: '{{ route("admin.commission.view") }}',
            method: 'POST',
            timeout: 15000, // 15 seconds timeout
            data: {
                commission_id: commissionId,
                _token: '{{ csrf_token() }}'
            },
            beforeSend: function() {
                console.log('Starting AJAX request for commission ID:', commissionId);
                console.log('Request URL:', '{{ route("admin.commission.view") }}');
                console.log('Request data:', {
                    commission_id: commissionId,
                    _token: '{{ csrf_token() }}'
                });
            },
            success: function(response) {
                console.log('AJAX Success:', response);
                console.log('Response type:', typeof response);
                console.log('Response keys:', response ? Object.keys(response) : 'no keys');

                if (response.success && response.html) {
                    try {
                        // Replace modal content with the server response
                        $modalContent.html(response.html);

                        // Re-enable any buttons or form elements
                        $modalContent.find('button').prop('disabled', false);

                        console.log('Modal content updated successfully');
                        console.log('Modal content length:', response.html.length);
                    } catch (err) {
                        console.error('Error updating modal content:', err);
                        showErrorModal('Lỗi khi hiển thị dữ liệu: ' + err.message);
                    }
                } else {
                    console.error('Invalid response format:', response);
                    const errorMsg = response.message || response.error || 'Không tìm thấy dữ liệu hoa hồng';
                    showErrorModal(errorMsg);
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX Error:', xhr.responseText);
                let errorMsg = 'Có lỗi xảy ra khi tải dữ liệu';

                if (status === 'timeout') {
                    errorMsg = 'Đã hết thời gian chờ phản hồi từ máy chủ';
                } else if (xhr.status === 404) {
                    errorMsg = 'Không tìm thấy dữ liệu hoa hồng';
                } else if (xhr.status === 500) {
                    errorMsg = 'Lỗi máy chủ nội bộ';
                }

                showErrorModal(errorMsg + ' (' + xhr.status + ')');
            }
        });
    });

    // Helper function for showing error in modal
    function showErrorModal(message) {
        const $modal = $('#commissionDetailModal');
        const $modalContent = $modal.find('.modal-content');

        $modalContent.html(`
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">
                    <i class="fas fa-exclamation-triangle me-2"></i>Lỗi
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center py-4">
                <div class="text-danger mb-3">
                    <i class="fas fa-times-circle fa-3x"></i>
                </div>
                <p class="text-danger">${message}</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
            </div>
        `);
    }

    // Handle search form submission
    $('#searchCommissionForm').on('submit', function(e) {
        e.preventDefault();
        const searchDate = $('#searchDate').val();
        const activeTab = $('.nav-link.active').attr('id');
        const type = activeTab === 'sale-tab' ? 'Sale' : 'Rent';
        const container = activeTab === 'sale-tab' ? '#sale-commission-container' : '#rent-commission-container';

        // Show loading state
        $(container).html(`
            <div class="loading-state text-center py-5">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Đang tải...</span>
                </div>
                <p class="mt-3 text-muted">Đang tìm kiếm dữ liệu...</p>
            </div>
        `);

        $.ajax({
            url: '{{ route("admin.commission.search.type") }}',
            method: 'GET',
            data: {
                search_date: searchDate,
                type: type
            },
            success: function(response) {
                $(container).html(response);
                // Reinitialize chart if present AFTER a delay to avoid modal conflicts
                setTimeout(function() {
                    initializeRevenueChart();
                    // Re-setup modal buttons for new content
                    setupModalButtonHandlers();
                }, 300);
            },
            error: function(xhr) {
                console.error('Error:', xhr);
                $(container).html('<div class="alert alert-danger">Có lỗi xảy ra khi tìm kiếm. Vui lòng thử lại.</div>');
            }
        });
    // Handle tab changes
    $('#commissionTabs button[data-bs-toggle="tab"]').on('shown.bs.tab', function(e) {
        console.log('Tab changed, reinitializing chart and modal handlers...');

        // Clear search value
        $('#searchDate').val('');

        // Reinitialize chart for new tab
        setTimeout(function() {
            initializeRevenueChart();
            // Re-setup modal buttons for new tab content
            setupModalButtonHandlers();
        }, 200);
    });

    // Initialize tooltips
    if (typeof bootstrap !== 'undefined') {
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    }
});
</script>
@endpush

@endif
@endsection
