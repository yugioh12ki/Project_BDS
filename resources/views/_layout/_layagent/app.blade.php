<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    @vite([
        'resources/js/app.js',
        'resources/sass/appagent.scss',
        'resources/js/header_agent.js',
        'resources/js/sidebar.js',
        ])
    <title>@yield('title', 'Trang Quản Lý Agent')</title>
</head>
<header>
    @include('_layout._layagent.header')
</header>

<body>
    <!-- Nội dung chính -->
    <div class="content-area">
        @yield('content')
    </div>
    
    <!-- Footer -->
    @include('_layout._layagent.footer')
</body>
</html> 