<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    @vite([
        'resources/js/app.js',
        'resources/sass/appowner.scss',
        'resources/js/header_owner.js',
        'resources/js/appointment-owner.js',
        'resources/js/dscactinh.js',
        'resources/js/dscactinh_edit.js',
        'resources/js/notifications-owner.js',
        ])
    <title>@yield('title', 'Trang Chủ Sở Hữu')</title>

    @yield('styles')

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
        @yield('content')
    </div>

    <!-- Footer -->
    @include('_layout._layowner.footer')

    @stack('scripts')
</body>
</html>
