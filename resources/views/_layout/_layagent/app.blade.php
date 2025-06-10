<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

    @yield('styles')
    @vite([
        'resources/js/app.js',
        'resources/sass/appagent.scss',
        'resources/js/header_agent.js',
        'resources/js/autocomplete.js',
        'resources/js/appointment-agent.js',
        'resources/js/property-district.js',
        'resources/sass/scss_agent/transactions.scss',
        'resources/sass/scss_agent/create-transaction-modal.scss',
        'resources/js/notifications-agent.js',
        'resources/js/create-transaction-modal-fixed.js',
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
        @yield('detail-property')
    </div>

    <!-- Footer -->
    @include('_layout._layagent.footer')

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Custom Scripts -->
    @yield('scripts')

</body>
</html>
