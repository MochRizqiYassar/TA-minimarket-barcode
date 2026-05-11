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
            <header class="mb-3 d-flex justify-content-between align-items-center">

                <a href="#" class="burger-btn d-block d-xl-none">
                    <i class="bi bi-justify fs-3"></i>
                </a>

                @php

                    $stokMenipis = \App\Models\Barang::all()->filter(function ($barang) {
                        return $barang->is_stok_menipis;
                    });

                    $barangExpired = \App\Models\BarangMasuk::with('barang')
                        ->get()
                        ->filter(function ($item) {
                            return in_array($item->status_expired, ['warning', 'kritis', 'expired']);
                        });

                    $totalNotif = $stokMenipis->count() + $barangExpired->count();

                @endphp

                <!-- RIGHT HEADER -->
                <div class="ms-auto d-flex align-items-center" style="gap: 2px;">

                    <!-- 🔔 NOTIF -->
                    <div class="dropdown">

                        <button
                            class="btn border-0 shadow-none position-relative d-flex align-items-center justify-content-center"
                            type="button" data-bs-toggle="dropdown" style="width: 38px; height: 38px;">

                            <i class="bi bi-bell fs-5"></i>

                            @if ($totalNotif > 0)
                                <span
                                    class="position-absolute top-0 start-100 translate-middle p-1 bg-danger border border-light rounded-circle">
                                </span>
                            @endif
                        </button>

                        <ul class="dropdown-menu dropdown-menu-end shadow" style="width: 320px;">

                            <li class="dropdown-header fw-bold">
                                Notifikasi Stok
                            </li>

                            @forelse($stokMenipis as $barang)
                                <li>
                                    <a href="{{ route('barang.index') }}" class="dropdown-item small">

                                        <strong>
                                            {{ $barang->nama_barang }}
                                        </strong>

                                        <br>

                                        <span class="text-danger">
                                            Etalase: {{ $barang->stok }}
                                            |
                                            Gudang: {{ $barang->stok_gudang }}
                                        </span>

                                    </a>
                                </li>
                            @empty
                                <li>
                                    <span class="dropdown-item text-muted">
                                        Tidak ada notif
                                    </span>
                                </li>
                            @endforelse

                            <li>
                                <hr class="dropdown-divider">
                            </li>

                            <li class="dropdown-header fw-bold text-danger">
                                Expired
                            </li>

                            @forelse($barangExpired as $item)
                                <li>

                                    <a href="{{ route('barang-masuk.index') }}" class="dropdown-item small">

                                        <strong>
                                            {{ $item->barang?->nama_barang }}
                                        </strong>

                                        <br>

                                        @if ($item->status_expired == 'expired')
                                            <span class="text-danger fw-bold">
                                                Sudah expired
                                            </span>
                                        @elseif($item->status_expired == 'kritis')
                                            <span class="text-danger">
                                                Expired {{ $item->sisa_hari_expired }} hari lagi
                                            </span>
                                        @else
                                            <span class="text-warning">
                                                Expired {{ $item->sisa_hari_expired }} hari lagi
                                            </span>
                                        @endif

                                    </a>

                                </li>
                            @empty
                                <li>
                                    <span class="dropdown-item text-muted">
                                        Tidak ada barang expired
                                    </span>
                                </li>
                            @endforelse

                        </ul>

                    </div>

                    <!-- 👤 PROFILE -->
                    <div class="dropdown">

                        <button class="btn border-0 shadow-none d-flex align-items-center justify-content-center"
                            type="button" data-bs-toggle="dropdown" style="width: 38px; height: 38px;">

                            <i class="bi bi-person-circle fs-4"></i>
                        </button>

                        <ul class="dropdown-menu dropdown-menu-end shadow">

                            <li class="dropdown-header">
                                {{ auth()->user()->name ?? 'User' }}
                            </li>

                            <li>
                                <a href="{{ route('profile.edit') }}" class="dropdown-item">
                                    <i class="bi bi-person me-2"></i> Profile
                                </a>
                            </li>

                            <li>
                                <hr class="dropdown-divider">
                            </li>

                            <li>
                                <a href="{{ route('logout') }}" class="dropdown-item text-danger"
                                    onclick="event.preventDefault(); document.getElementById('logout-form').submit();">

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
</body>

</html>
