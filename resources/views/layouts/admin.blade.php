<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - admin Dashboard</title>

    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@300;400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.css') }}">

    <link rel="stylesheet" href="{{ asset('assets/vendors/iconly/bold.css') }}">

    <link rel="stylesheet" href="{{ asset('assets/vendors/perfect-scrollbar/perfect-scrollbar.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendors/bootstrap-icons/bootstrap-icons.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/app.css') }}">
    <link rel="shortcut icon" href="{{ asset('assets/images/favicon.svg') }}" type="image/x-icon">
</head>

<body>
    <div id="app">
        <div id="sidebar" class="active">
            <div class="sidebar-wrapper active">
                <div class="sidebar-header">
                    <div class="d-flex justify-content-between">
                        <div class="logo">
                            <a href="{{ route('dashboard') }}"><img src="{{ asset('assets/images/logo/logo.png') }}"
                                    alt="Logo" srcset=""></a>
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
                                <span>Data Kulakan</span>
                            </a>

                            <ul class="submenu">

                                <li class="submenu-item">
                                    <a href="{{ route('kulakan.index') }}">Kulakan</a>
                                </li>

                                <li class="submenu-item">
                                    <a href="{{ route('suppliers.index') }}">Supplier</a>
                                </li>

                            </ul>
                        </li>

                        <li class="sidebar-item  has-sub">
                            <a href="#" class='sidebar-link'>
                                <i class="bi bi-collection-fill"></i>
                                <span>Data Barang</span>
                            </a>
                            <ul class="submenu">
                                <li class="submenu-item">
                                    <a href="{{ route('barang.index') }}">Barang</a>
                                </li>

                                <li class="submenu-item">
                                    <a href="{{ route('barang-masuk.index') }}">Barang Masuk</a>
                                </li>

                                <li class="submenu-item">
                                    <a href="{{ route('tipe-barang.index') }}">Tipe Barang</a>
                                </li>

                                <li class="submenu-item">
                                    <a href="{{ route('kategoris.index') }}">Kategori</a>
                                </li>
                            </ul>
                        </li>

                        <li class="sidebar-item">
                            <a href="{{ route('admin.users') }}" class='sidebar-link'>
                                <i class="bi bi-person-check"></i>
                                <span>Verifikasi Akun</span>
                            </a>
                        </li>

                        <li class="sidebar-item">
                            <a href="{{ route('laporan.barang-masuk') }}" class="sidebar-link">

                                <i class="bi bi-file-earmark-text"></i>

                                <span>Laporan Barang Masuk</span>
                            </a>
                        </li>
                        <li class="sidebar-item">
                            <a href="{{ route('laporan.penjualan') }}" class="sidebar-link">

                                <i class="bi bi-cash-stack"></i>

                                <span>Laporan Penjualan</span>
                            </a>
                        </li>
                        <li class="sidebar-item">
                            <a href="{{ route('laporan.barang-terlaris') }}" class="sidebar-link">

                                <i class="bi bi-bar-chart"></i>

                                <span>Barang Terlaris</span>
                            </a>
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
            <header class="mb-3">
                <a href="#" class="burger-btn d-block d-xl-none">
                    <i class="bi bi-justify fs-3"></i>
                </a>
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
</body>

</html>
