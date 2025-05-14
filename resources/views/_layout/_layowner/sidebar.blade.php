<!-- Sidebar -->
<nav id="sidebarMenu" class="collapse d-lg-block sidebar bg-light">
    <div class="position-sticky">
        <div class="list-group list-group-flush">

            <a href="{{ route('owner.dashboard') }}" class="list-group-item list-group-item-action py-3">
                <i class="bi bi-speedometer2 me-2"></i> Bảng điều khiển
            </a>

            <!-- Dropdown tài sản -->
            <a class="list-group-item list-group-item-action py-3" data-bs-toggle="collapse" href="#propertySubmenu" role="button">
                <i class="bi bi-house me-2"></i> Tài sản
            </a>
            <div class="collapse ps-3" id="propertySubmenu">
                <a href="{{ route('owner.property.create') }}" class="list-group-item list-group-item-action py-2">Thêm BĐS</a>
                <a href="{{ route('owner.property.index') }}" class="list-group-item list-group-item-action py-2">Danh sách</a>
            </div>

            <a href="{{ route('owner.appointments.index') }}" class="list-group-item list-group-item-action py-3">
                <i class="bi bi-calendar-event me-2"></i> Lịch hẹn
            </a>

            <a href="{{ route('owner.transactions.index') }}" class="list-group-item list-group-item-action py-3">
                <i class="bi bi-receipt me-2"></i> Giao dịch
            </a>
        </div>
    </div>
</nav>
