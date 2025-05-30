<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Trang Bất Động Sản</title>
    @vite([
        'resources/sass/header.scss',
        'resources/sass/footer.scss',
        'resources/sass/home.scss',
        'resources/sass/modern-home.scss',
        'resources/js/home.js',
        'resources/js/notifications.js',
        'resources/sass/apphome.scss',
        ])
    {{-- Từ bản laravel 12x trở lên sử dụng @vite để điều hướng scss --}}
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    @yield('styles')
</head>
<header>
    @include('_layout.header')
</header>

<body>
    @yield('home')

    <!-- Chatbox Component -->
    @include('components.chatbox')
    @yield('content')
</body>
<footer>
    @include('_layout.footer')
</footer>

@yield('scripts')
<script src="{{ asset('js/modern-home.js') }}" defer></script>
</html>
