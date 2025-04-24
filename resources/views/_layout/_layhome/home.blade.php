<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trang Bất Động Sản</title>
    @vite([
        'resources/scss/header.scss',
        'resources/scss/footer.scss',
        'resources/scss/home.scss',
        'resources/js/home.js'
        ])
    {{-- Từ bản laravel 12x trở lên sử dụng @vite để điều hướng scss --}}
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
</head>
<header>
    @include('_layout.header')
</header>

<body>
    @yield('home')
</body>
<footer>
    @include('_layout.footer')
</footer>

</html>
