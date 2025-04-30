<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    @vite([
        'resources/scss/scss_admin/header.scss',
        'resources/scss/scss_admin/menu.scss',
        'resources/scss/scss_admin/main.scss',
    ])
    <title>Document</title>
    <script src="resources/js/auth.js"></script>
</head>
<header>
    @include('_layout._layadmin.header')
</header>
<body>
    <div class="layout-container">
        @include('_layout._layadmin.menu')
        <div class="content">
            @yield('dashboard')
            @yield('user')
        </div>

    </div>
</body>
</html>
