<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Auth Tài Khoản</title>
    @vite([
        'resources/sass/header.scss',
        'resources/sass/footer.scss',
        'resources/sass/login.scss',
        'resources/sass/register.scss',
        'resources/js/dscactinh.js'
        ])
    {{-- Từ bản laravel 12x trở lên sử dụng @vite để điều hướng scss --}}
    {{-- <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script> --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
</head>
<header>
    @include('_layout.header')
</header>

<body>
    @yield('login')
    @yield('register')
    @yield('home')
</body>
<footer>
    @include('_layout.footer')
</footer>

<script>
    $(document).ready(function() {
        // Xử lý các modal giao dịch
        $('.modal').on('hidden.bs.modal', function () {
            // Xóa các thông báo lỗi hoặc thành công khi đóng modal
            $(this).find('.alert').remove();
        });

        // Thiết lập mặc định cho tab trong modal
        $('.modal').on('shown.bs.modal', function () {
            // Đảm bảo tab đầu tiên hiển thị
            $(this).find('.nav-tabs .nav-link:first').tab('show');
        });
    });
</script>

</html>
