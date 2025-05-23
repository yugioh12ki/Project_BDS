<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    @vite([
        'resources/js/app.js',
        'resources/sass/appowner.scss',
        'resources/js/header_owner.js',
        'resources/js/sidebar.js',
        'resources/js/dscactinh.js',
        'resources/js/dscactinh_edit.js',
        'resources/js/create_property_listing.js',
        ])
    <title>@yield('title', 'Trang Chủ Sở Hữu')</title>
    
    

</head>
<header>
    @include('_layout._layowner.header')
</header>

<body>
    {{-- Remove old menu toggle button --}}
    {{-- <button class="menu-toggle" aria-label="Mở menu">
        &#9776;
    </button> --}}

    {{-- Dropdown menu xổ xuống - This was the old sidebar, now it's the nav bar included above --}}
    {{-- @include('_layout._layowner.sidebar') --}}

    <!-- Nội dung chính -->
    <div class="content-area">
        @yield('property')
        @yield('appointment')
        @yield('transaction')
        @yield('show')
        @yield('dashboard')
    </div>
    
    <!-- Footer -->
    @include('_layout._layowner.footer')
</body>
</html>
