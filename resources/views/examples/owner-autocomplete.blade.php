@extends('layouts.app')

@section('title', 'Owner Autocomplete Example')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">
                        <i class="bi bi-person-search me-2 text-primary"></i>
                        Owner Autocomplete Examples
                    </h4>
                </div>
                <div class="card-body">
                    <p class="text-muted mb-4">
                        Các ví dụ sử dụng component Owner Autocomplete trong Laravel Blade
                    </p>
                    
                    <!-- Example 1: Basic Usage -->
                    <div class="row mb-5">
                        <div class="col-md-6">
                            <h5>1. Sử dụng cơ bản</h5>
                            <x-owner-autocomplete 
                                id="basic-example"
                                label="Chủ sở hữu"
                                placeholder="Nhập tên chủ sở hữu..." />
                        </div>
                        
                        <div class="col-md-6">
                            <h5>Code</h5>
                            <div class="bg-light p-3 rounded">
                                <code>
                                    &lt;x-owner-autocomplete <br>
                                    &nbsp;&nbsp;&nbsp;&nbsp;id="basic-example"<br>
                                    &nbsp;&nbsp;&nbsp;&nbsp;label="Chủ sở hữu"<br>
                                    &nbsp;&nbsp;&nbsp;&nbsp;placeholder="Nhập tên chủ sở hữu..." /&gt;
                                </code>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Example 2: In Form -->
                    <div class="row mb-5">
                        <div class="col-md-6">
                            <h5>2. Trong form với validation</h5>
                            <form id="appointment-form" class="needs-validation" novalidate>
                                @csrf
                                <x-owner-autocomplete 
                                    id="form-owner"
                                    name="owner_name"
                                    hiddenInputName="owner_id"
                                    label="Chủ sở hữu"
                                    placeholder="Chọn chủ sở hữu..."
                                    required="true"
                                    helpText="Bắt buộc chọn chủ sở hữu để tạo lịch hẹn" />
                                
                                <div class="mb-3">
                                    <label for="appointment-date" class="form-label">Ngày hẹn</label>
                                    <input type="datetime-local" class="form-control" id="appointment-date" name="appointment_date" required>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="note" class="form-label">Ghi chú</label>
                                    <textarea class="form-control" id="note" name="note" rows="3"></textarea>
                                </div>
                                
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-calendar-plus me-1"></i>
                                    Tạo lịch hẹn
                                </button>
                            </form>
                        </div>
                        
                        <div class="col-md-6">
                            <h5>Code</h5>
                            <div class="bg-light p-3 rounded">
                                <code>
                                    &lt;form id="appointment-form"&gt;<br>
                                    &nbsp;&nbsp;&lt;x-owner-autocomplete <br>
                                    &nbsp;&nbsp;&nbsp;&nbsp;id="form-owner"<br>
                                    &nbsp;&nbsp;&nbsp;&nbsp;name="owner_name"<br>
                                    &nbsp;&nbsp;&nbsp;&nbsp;hiddenInputName="owner_id"<br>
                                    &nbsp;&nbsp;&nbsp;&nbsp;required="true" /&gt;<br>
                                    &nbsp;&nbsp;...<br>
                                    &lt;/form&gt;
                                </code>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Example 3: Custom Styling -->
                    <div class="row mb-5">
                        <div class="col-md-6">
                            <h5>3. Custom styling</h5>
                            <x-owner-autocomplete 
                                id="custom-example"
                                label="Chủ sở hữu BĐS"
                                placeholder="Tìm kiếm..."
                                class="form-control-lg"
                                helpText="Tìm kiếm nâng cao với highlight"
                                :showLabel="true" />
                        </div>
                        
                        <div class="col-md-6">
                            <h5>Code</h5>
                            <div class="bg-light p-3 rounded">
                                <code>
                                    &lt;x-owner-autocomplete <br>
                                    &nbsp;&nbsp;&nbsp;&nbsp;id="custom-example"<br>
                                    &nbsp;&nbsp;&nbsp;&nbsp;class="form-control-lg"<br>
                                    &nbsp;&nbsp;&nbsp;&nbsp;label="Chủ sở hữu BĐS"<br>
                                    &nbsp;&nbsp;&nbsp;&nbsp;:showLabel="true" /&gt;
                                </code>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Example 4: Pre-selected Value -->
                    <div class="row mb-5">
                        <div class="col-md-6">
                            <h5>4. Với giá trị được chọn sẵn</h5>
                            <x-owner-autocomplete 
                                id="preselected-example"
                                label="Chủ sở hữu hiện tại"
                                value="Nguyễn Văn An"
                                :selectedOwner="[
                                    'id' => '1',
                                    'name' => 'Nguyễn Văn An',
                                    'email' => 'an@example.com',
                                    'phone' => '0123456789'
                                ]" />
                        </div>
                        
                        <div class="col-md-6">
                            <h5>Code</h5>
                            <div class="bg-light p-3 rounded">
                                <code>
                                    &lt;x-owner-autocomplete <br>
                                    &nbsp;&nbsp;&nbsp;&nbsp;id="preselected-example"<br>
                                    &nbsp;&nbsp;&nbsp;&nbsp;value="Nguyễn Văn An"<br>
                                    &nbsp;&nbsp;&nbsp;&nbsp;:selectedOwner="[<br>
                                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'id' => '1',<br>
                                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'name' => 'Nguyễn Văn An',<br>
                                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'email' => 'an@example.com'<br>
                                    &nbsp;&nbsp;&nbsp;&nbsp;]" /&gt;
                                </code>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Features List -->
                    <div class="row">
                        <div class="col-12">
                            <h5>Tính năng</h5>
                            <div class="row">
                                <div class="col-md-6">
                                    <ul class="list-unstyled">
                                        <li><i class="bi bi-check-circle-fill text-success me-2"></i>AJAX search real-time</li>
                                        <li><i class="bi bi-check-circle-fill text-success me-2"></i>Debounced input (300ms)</li>
                                        <li><i class="bi bi-check-circle-fill text-success me-2"></i>Keyboard navigation (Arrow keys, Enter, Esc)</li>
                                        <li><i class="bi bi-check-circle-fill text-success me-2"></i>Search highlighting</li>
                                        <li><i class="bi bi-check-circle-fill text-success me-2"></i>Loading states</li>
                                    </ul>
                                </div>
                                <div class="col-md-6">
                                    <ul class="list-unstyled">
                                        <li><i class="bi bi-check-circle-fill text-success me-2"></i>Error handling</li>
                                        <li><i class="bi bi-check-circle-fill text-success me-2"></i>Responsive design</li>
                                        <li><i class="bi bi-check-circle-fill text-success me-2"></i>Bootstrap integration</li>
                                        <li><i class="bi bi-check-circle-fill text-success me-2"></i>Custom events</li>
                                        <li><i class="bi bi-check-circle-fill text-success me-2"></i>Form validation support</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Demo Result Modal -->
<div class="modal fade" id="resultModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Form Submitted</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="form-result"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Form submission handler
    $('#appointment-form').on('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        const data = Object.fromEntries(formData);
        
        // Check if owner is selected
        if (!data.owner_id) {
            alert('Vui lòng chọn chủ sở hữu');
            return;
        }
        
        // Show result
        $('#form-result').html(`
            <h6>Dữ liệu form:</h6>
            <pre>${JSON.stringify(data, null, 2)}</pre>
        `);
        
        const modal = new bootstrap.Modal(document.getElementById('resultModal'));
        modal.show();
    });
    
    // Listen to owner selection events
    $('.owner-autocomplete-input').on('owner:selected', function(event, owner) {
        console.log('Owner selected:', owner);
        
        // You can add custom logic here
        // For example, load properties of the selected owner
        // loadOwnerProperties(owner.id);
    });
    
    $('.owner-autocomplete-input').on('owner:cleared', function(event) {
        console.log('Owner selection cleared');
    });
    
    // Mock data for demo (remove in production)
    if (window.location.hostname === 'localhost' || window.location.hostname === '127.0.0.1') {
        const originalAjax = $.ajax;
        $.ajax = function(options) {
            if (options.url.includes('/agent/search/owners')) {
                const term = options.data.term.toLowerCase();
                const mockOwners = [
                    { id: 1, name: 'Nguyễn Văn An', email: 'an@example.com', phone: '0123456789' },
                    { id: 2, name: 'Trần Thị Bình', email: 'binh@example.com', phone: '0987654321' },
                    { id: 3, name: 'Lê Văn Cường', email: 'cuong@example.com', phone: '0234567890' },
                    { id: 4, name: 'Phạm Thị Dung', email: 'dung@example.com', phone: '0345678901' },
                    { id: 5, name: 'Hoàng Văn Em', email: 'em@example.com', phone: '0456789012' },
                    { id: 6, name: 'Võ Thị Phương', email: 'phuong@example.com', phone: '0567890123' },
                    { id: 7, name: 'Đặng Văn Giang', email: 'giang@example.com', phone: '0678901234' }
                ];
                
                const filtered = mockOwners.filter(owner => 
                    owner.name.toLowerCase().includes(term) || 
                    owner.email.toLowerCase().includes(term) ||
                    owner.phone.includes(term)
                );
                
                setTimeout(() => {
                    options.success({ owners: filtered });
                }, 200);
                
                return;
            }
            return originalAjax.call(this, options);
        };
    }
});
</script>
@endpush

@push('styles')
<style>
    .card {
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    }
    
    code {
        font-size: 0.875rem;
        color: #e83e8c;
    }
    
    .bg-light {
        background-color: #f8f9fa !important;
    }
    
    pre {
        font-size: 0.875rem;
        margin-bottom: 0;
    }
</style>
@endpush
