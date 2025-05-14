<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'Trang Chủ Sở Hữu')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    {{-- Gọi CSS --}}
    <link rel="stylesheet" href="{{ asset('css/owner.css') }}">
</head>
<body>

    {{-- HEADER --}}
    @include('_layout._layowner.header')

    {{-- MAIN CONTENT --}}
    <main class="container">
        @yield('content')
    </main>

    {{-- FOOTER --}}
    @include('_layout._layowner.footer')

</body>
</html>
