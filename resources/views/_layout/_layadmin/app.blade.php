<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <script src="{{ asset('js/global-functions.js') }}"></script>

    @vite([
        'resources/js/app.js',
        'resources/sass/app.scss',
        'resources/js/dscactinh.js',
        'resources/js/dscactinh_edit.js',
    ])
    <title>Quản lý BĐS - Admin</title>


</head>
<header>
    @include('_layout._layadmin.header')
</header>
<body>
    <div class="layout-container">
        <div class="side-bar">
            @include('_layout._layadmin.menu')
        </div>

        <div class="content">
            @yield('dashboard')
            @yield('user')
            @yield('property')
            @yield('appointment')
            @yield('transaction')
            @yield('feedback')
            @yield('commission')
            @yield('chatbotai')
        </div>
    </div>

</body>
</html>
