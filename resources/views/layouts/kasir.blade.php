<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Dashboard - Kasir Dashboard</title>

    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@300;400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.css') }}">

    <link rel="stylesheet" href="{{ asset('assets/vendors/iconly/bold.css') }}">

    <link rel="stylesheet" href="{{ asset('assets/vendors/perfect-scrollbar/perfect-scrollbar.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendors/bootstrap-icons/bootstrap-icons.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/app.css') }}">
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}">
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#0d6efd">
    <link rel="apple-touch-icon" href="/images/icons/icon-192.jpg">
    <style>
    .product-list-scroll {
        max-height: 70vh;
        overflow-y: auto;
        overflow-x: hidden;
    }

    .product-item {
        cursor: pointer;
        transition: 0.2s;
    }

    .product-item:hover {
        transform: scale(1.02);
    }

    /* ===== SIDEBAR ===== */

    .sidebar-header {
        padding-bottom: 0.5rem !important;
    }

    .sidebar-menu {
        margin-top: -10px;
    }

    .sidebar-title {
        margin-top: 0 !important;
        margin-bottom: 10px !important;
        padding-top: 0 !important;
    }

    .logo img {
        margin-bottom: -10px;
    }
</style>
</head>
<script>
    if ('serviceWorker' in navigator) {
        navigator.serviceWorker.register('/sw.js')
            .then(() => console.log('Service Worker Registered'));
    }
</script>

<body>
    <div id="app">
        <div id="sidebar" class="active">
            <div class="sidebar-wrapper active">
                <div class="sidebar-header">
                    <div class="d-flex justify-content-between">
                        <div class="logo">
                            <a href="{{ route('dashboard') }}">

                                <img src="{{ asset('assets/images/logo/toko1.png') }}" alt="Toko1"
                                    style="width: 220px; height: auto;">

                            </a>
                        </div>
                        <div class="toggler">
                            <a href="#" class="sidebar-hide d-xl-none d-block"><i
                                    class="bi bi-x bi-middle"></i></a>
                        </div>
                    </div>
                </div>
                <div class="sidebar-menu">
                    <ul class="menu">
                        <li class="sidebar-title">Menu</li>

                        <li class="sidebar-item active ">
                            <a href="{{ route('dashboard') }}" class='sidebar-link'>
                                <i class="bi bi-grid-fill"></i>
                                <span>Dashboard</span>
                            </a>
                        </li>
                        <li class="sidebar-item has-sub">
                            <a href="#" class='sidebar-link'>
                                <i class="bi bi-stack"></i>
                                <span>Data Penjualan</span>
                            </a>

                            <ul class="submenu">

                                <li class="submenu-item">
                                    <a href="{{ route('penjualan.index') }}">Penjualan</a>
                                </li>
                            </ul>
                        </li>
                        <li class="sidebar-item  ">
                            <a href="{{ route('logout') }}" class='sidebar-link'
                                onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                <i class="bi bi-cash"></i>
                                <span>logout</span>
                            </a>
                            <form id="logout-form" action="{{ route('logout') }}" method="POST"
                                style="display: none;">
                                @csrf
                            </form>
                        </li>

                    </ul>
                </div>
                <button class="sidebar-toggler btn x"><i data-feather="x"></i></button>
            </div>
        </div>
        <div id="main">
            <header class="mb-3 d-flex justify-content-between align-items-center">

    <a href="#" class="burger-btn d-block d-xl-none">
        <i class="bi bi-justify fs-3"></i>
    </a>

    <!-- RIGHT HEADER -->
    <div class="ms-auto d-flex align-items-center" style="gap: 2px;">

        <!-- 👤 PROFILE -->
        <div class="dropdown">

            <button
                class="btn border-0 shadow-none d-flex align-items-center justify-content-center"
                type="button"
                data-bs-toggle="dropdown"
                style="width: 38px; height: 38px;">

                <i class="bi bi-person-circle fs-4"></i>
            </button>

            <ul class="dropdown-menu dropdown-menu-end shadow">

                <li class="dropdown-header">
                    {{ auth()->user()->name ?? 'Kasir' }}
                </li>

                <li>
                    <a href="{{ route('profile.edit') }}" class="dropdown-item">
                        <i class="bi bi-person me-2"></i>
                        Profile
                    </a>
                </li>

                <li>
                    <hr class="dropdown-divider">
                </li>

                <li>
                    <a href="{{ route('logout') }}"
                       class="dropdown-item text-danger"
                       onclick="event.preventDefault();
                       document.getElementById('logout-form').submit();">

                        <i class="bi bi-box-arrow-right me-2"></i>
                        Logout
                    </a>
                </li>

            </ul>

        </div>

    </div>

</header>

            <div class="page-heading">
                <h3>@yield('title')</h3>
            </div>

            <div class="page-content">
                @yield('content')
            </div>

            <footer>
                <div class="footer clearfix mb-0 text-muted">
                    <div class="float-start">
                        <p>2021 &copy; Mazer</p>
                    </div>
                    <div class="float-end">
                        <p>Crafted with <span class="text-danger"><i class="bi bi-heart"></i></span> by <a
                                href="http://ahmadsaugi.com">A. Saugi</a></p>
                    </div>
                </div>
            </footer>
        </div>
    </div>
    <script src="{{ asset('assets/vendors/perfect-scrollbar/perfect-scrollbar.min.js') }}"></script>
    <script src="{{ asset('assets/js/bootstrap.bundle.min.js') }}"></script>

    <script src="{{ asset('assets/vendors/apexcharts/apexcharts.js') }}"></script>
    <script src="{{ asset('assets/js/pages/dashboard.js') }}"></script>

    <script src="{{ asset('assets/js/main.js') }}"></script>
    <div id="network-status"
        style="
        position: fixed;
        bottom: 10px;
        right: 10px;
        z-index: 9999;
        padding: 10px 15px;
        border-radius: 8px;
        color: white;
        font-weight: bold;
     ">
    </div>
    <script>
        function updateNetworkStatus() {

            const status = document.getElementById('network-status');

            if (navigator.onLine) {

                status.innerHTML = '🟢 Online';

                status.style.background = 'green';

            } else {

                status.innerHTML = '🔴 Offline';

                status.style.background = 'red';
            }
        }

        window.addEventListener('online', updateNetworkStatus);

        window.addEventListener('offline', updateNetworkStatus);

        updateNetworkStatus();
    </script>
    <script src="/js/offline-db.js"></script>

    <script src="/js/offline-sync.js"></script>
</body>

</html>
