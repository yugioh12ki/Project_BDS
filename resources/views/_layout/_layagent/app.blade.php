<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    @vite([
        'resources/js/app.js',
        'resources/sass/appagent.scss',
        'resources/js/header_agent.js',
        'resources/js/appointments.js',
        'resources/js/autocomplete.js',
        'resources/js/appointment-agent.js',
        'resources/js/property-district.js',
        'resources/js/notifications-agent.js',
        ])
    <title>@yield('title', 'Người Môi Giới')</title>
</head>
<header>
    @include('_layout._layagent.header')
</header>

<body>
    <!-- Nội dung chính -->
    <div class="content-area">
        @yield('dashboard')
        @yield('profile-agent')
        @yield('brokers')
        @yield('appointments')
        @yield('transactions')
    </div>
    
    <!-- Footer -->
    @include('_layout._layagent.footer')
</body>
</html> 