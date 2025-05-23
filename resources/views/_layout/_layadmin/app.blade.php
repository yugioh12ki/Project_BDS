<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

    <!-- Scripts -->
    <script src="{{ asset('js/global-functions.js') }}"></script>

    @vite([
        'resources/js/app.js',
        'resources/sass/app.scss',
        'resources/js/dscactinh.js',
        'resources/js/dscactinh_edit.js',
    ])

    <title>@yield('title', 'Quản lý BĐS - Admin Dashboard')</title>

    <!-- Additional CSS -->
    <style>
        :root {
            --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            --secondary-gradient: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            --success-gradient: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
            --warning-gradient: linear-gradient(135deg, #ffecd2 0%, #fcb69f 100%);
            --danger-gradient: linear-gradient(135deg, #ff9a9e 0%, #fecfef 100%);
            --info-gradient: linear-gradient(135deg, #a8edea 0%, #fed6e3 100%);
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
        }

        .layout-container {
            display: flex;
            min-height: 100vh;
        }

        .side-bar {
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            z-index: 1000;
            transition: all 0.3s ease;
        }

        .content {
            margin-left: 280px;
            flex: 1;
            padding: 0;
            transition: margin-left 0.3s ease;
            min-height: 100vh;
            width: calc(100% - 280px);
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .side-bar {
                transform: translateX(-100%);
            }

            .content {
                margin-left: 0;
                width: 100%;
            }

            .side-bar.mobile-open {
                transform: translateX(0);
            }
        }

        /* Loading animation */
        .loading-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(255, 255, 255, 0.9);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 9999;
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s ease;
        }

        .loading-overlay.active {
            opacity: 1;
            visibility: visible;
        }

        .loading-spinner {
            width: 40px;
            height: 40px;
            border: 4px solid #f3f3f3;
            border-top: 4px solid #667eea;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        /* Custom scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
        }

        ::-webkit-scrollbar-track {
            background: #f1f1f1;
        }

        ::-webkit-scrollbar-thumb {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 4px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: linear-gradient(135deg, #5a6fd8 0%, #6a4190 100%);
        }
    </style>
</head>

<body>
    <!-- Loading Overlay -->
    <div class="loading-overlay" id="loadingOverlay">
        <div class="loading-spinner"></div>
    </div>

    <!-- Sidebar Overlay for mobile -->
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <!-- Header -->
    <header>
        @include('_layout._layadmin.header')
    </header>

    <div class="layout-container">
        <!-- Sidebar -->
        <div class="side-bar" id="sidebar">
            @include('_layout._layadmin.menu')
        </div>

        <!-- Main Content -->
        <div class="content" id="mainContent">
            @yield('dashboard')
            @yield('user')
            @yield('property')
            @yield('appointment')
            @yield('transaction')
            @yield('feedback')
            @yield('commission')
            @yield('chatbotai')
            @yield('tiepnhanhoso')
        </div>
    </div>

    <!-- Scripts -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Check if Bootstrap is loaded
            if (typeof bootstrap === 'undefined') {
                console.warn('Bootstrap is not defined in the global scope. Some features may not work correctly.');
            }

            // Mobile menu toggle
            const sidebarToggle = document.querySelector('.sidebar-toggle');
            const sidebar = document.getElementById('sidebar');
            const sidebarOverlay = document.getElementById('sidebarOverlay');
            const mainContent = document.getElementById('mainContent');

            if (sidebarToggle) {
                sidebarToggle.addEventListener('click', function() {
                    sidebar.classList.toggle('collapsed');
                    if (window.innerWidth <= 768) {
                        sidebar.classList.toggle('mobile-open');
                        sidebarOverlay.classList.toggle('active');
                        document.body.style.overflow = sidebar.classList.contains('mobile-open') ? 'hidden' : '';
                    }
                });
            }

            // Close mobile menu when clicking overlay
            if (sidebarOverlay) {
                sidebarOverlay.addEventListener('click', function() {
                    sidebar.classList.remove('mobile-open');
                    sidebarOverlay.classList.remove('active');
                    document.body.style.overflow = '';
                });
            }

            // Handle window resize
            window.addEventListener('resize', function() {
                if (window.innerWidth > 768) {
                    sidebar.classList.remove('mobile-open');
                    sidebarOverlay.classList.remove('active');
                    document.body.style.overflow = '';
                }
            });

            // Smooth scrolling for anchor links
            document.querySelectorAll('a[href^="#"]').forEach(anchor => {
                anchor.addEventListener('click', function (e) {
                    e.preventDefault();
                    const target = document.querySelector(this.getAttribute('href'));
                    if (target) {
                        target.scrollIntoView({
                            behavior: 'smooth',
                            block: 'start'
                        });
                    }
                });
            });

            // Auto-hide loading overlay
            setTimeout(() => {
                const loadingOverlay = document.getElementById('loadingOverlay');
                if (loadingOverlay) {
                    loadingOverlay.classList.remove('active');
                }
            }, 500);

            // Add loading state for navigation
            document.querySelectorAll('a').forEach(link => {
                link.addEventListener('click', function(e) {
                    const href = this.getAttribute('href');
                    if (href && href.startsWith('/') && !href.startsWith('#')) {
                        const loadingOverlay = document.getElementById('loadingOverlay');
                        if (loadingOverlay) {
                            loadingOverlay.classList.add('active');
                        }
                    }
                });
            });
        });

        // Global error handler
        window.addEventListener('error', function(e) {
            console.error('Error occurred:', e.error);
            // You can add custom error handling here
        });

        // Show loading function for AJAX calls
        window.showLoading = function() {
            const loadingOverlay = document.getElementById('loadingOverlay');
            if (loadingOverlay) {
                loadingOverlay.classList.add('active');
            }
        };

        // Hide loading function
        window.hideLoading = function() {
            const loadingOverlay = document.getElementById('loadingOverlay');
            if (loadingOverlay) {
                loadingOverlay.classList.remove('active');
            }
        };
    </script>
</body>
</html>
