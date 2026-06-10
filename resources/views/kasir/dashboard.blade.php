@extends('layouts.kasir')

@section('content')
    @php
        use App\Models\Penjualan;
        use App\Models\Barang;
        use Carbon\Carbon;

        $penjualanHariIni = Penjualan::whereDate('created_at', today())->count();

        $totalBarang = Barang::count();

        $transaksiBulanIni = Penjualan::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();
    @endphp

    <section class="section">

        <!-- HEADER -->
        <div class="dashboard-header mb-4">

            <div>

                <h2 class="fw-bold mb-1 text-dark">
                    Dashboard Kasir
                </h2>

                <p class="text-muted mb-0">
                    Monitoring aktivitas penjualan toko
                </p>

            </div>

            <div class="dashboard-date">

                <i class="bi bi-calendar3 me-2"></i>

                {{ now()->translatedFormat('d F Y') }}

            </div>

        </div>

        <!-- HERO -->
        <div class="retail-hero mb-4">

            <div class="hero-overlay"></div>

            <div class="hero-content">

                <div>

                    <h3 class="fw-bold text-white mb-2">
                        Halo,
                        {{ auth()->user()->name }}
                    </h3>

                    <p class="text-white opacity-75 mb-0">
                        Kelola Penjualan penjualan dengan cepat dan efisien.
                    </p>

                </div>

                <div class="hero-icon">

                    <i class="bi bi-cart-check"></i>

                </div>

            </div>

        </div>

        <!-- STATISTIC -->
        <div class="row g-4 align-items-stretch">

            <!-- PENJUALAN HARI INI -->
            <div class="col-12 col-md-6 col-xl-3">

                <div class="retail-card retail-primary">

                    <div class="retail-icon">

                        <i class="bi bi-receipt"></i>

                    </div>

                    <div>

                        <p class="retail-label">
                            Penjualan Hari Ini
                        </p>

                        <h2 class="retail-value">
                            {{ $penjualanHariIni }}
                        </h2>

                    </div>

                </div>

            </div>

            <!-- TOTAL BARANG -->
            <div class="col-12 col-md-6 col-xl-3">

                <div class="retail-card retail-success">

                    <div class="retail-icon">

                        <i class="bi bi-box-seam"></i>

                    </div>

                    <div>

                        <p class="retail-label">
                            Total Barang
                        </p>

                        <h2 class="retail-value">
                            {{ $totalBarang }}
                        </h2>

                    </div>

                </div>

            </div>

            <!-- TRANSAKSI BULAN INI -->
            <div class="col-12 col-md-6 col-xl-3">

                <div class="retail-card retail-warning">

                    <div class="retail-icon">
                        <i class="bi bi-cash-stack"></i>
                    </div>

                    <div>

                        <p class="retail-label">
                            Penjualan Bulan Ini
                        </p>

                        <h2 class="retail-value">
                            {{ $transaksiBulanIni }}
                        </h2>

                    </div>

                </div>

            </div>

        </div>

        <!-- BANNER -->
        <div class="row mt-4">

            <div class="col-12">

                <div class="retail-banner">

                    <div class="banner-pattern"></div>

                    <div class="banner-content">

                        <div>

                            <h4 class="fw-bold mb-2">
                                Sistem Kasir Arthapura
                            </h4>

                            <p class="mb-0 text-muted">
                                Penjualan lebih cepat dengan scanner barcode & sistem retail modern.
                            </p>

                        </div>

                        <div class="banner-icons">

                            <i class="bi bi-upc-scan"></i>
                            <i class="bi bi-cart-check"></i>
                            <i class="bi bi-credit-card"></i>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </section>

    <style>
        .dashboard-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 10px;
        }

        .dashboard-date {
            background: white;
            padding: 10px 16px;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            font-weight: 600;
        }

        .retail-hero {
            position: relative;
            overflow: hidden;
            border-radius: 24px;
            padding: 40px;
            background: linear-gradient(135deg, #3b82f6, #1e3a8a);
            min-height: 220px;
        }

        .hero-overlay {
            position: absolute;
            top: 0;
            right: 0;
            width: 300px;
            height: 300px;
            background: rgba(255, 255, 255, 0.08);
            border-radius: 50%;
            transform: translate(100px, -100px);
        }

        .hero-content {
            position: relative;
            z-index: 2;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .hero-icon {
            font-size: 90px;
            color: rgba(255, 255, 255, 0.2);
        }

        .retail-card {
            background: white;
            border-radius: 22px;
            padding: 28px;
            display: flex;
            flex-direction: row;
            align-items: center;
            gap: 20px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.05);
            transition: 0.3s;
            position: relative;
            overflow: hidden;
            height: 100%;
        }

        .retail-card:hover {
            transform: translateY(-5px);
        }

        .retail-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 6px;
            height: 100%;
        }

        .retail-primary::before {
            background: #0d6efd;
        }

        .retail-success::before {
            background: #198754;
        }

        .retail-warning::before {
            background: #fd7e14;
        }

        .retail-info::before {
            background: #0dcaf0;
        }

        .retail-icon {
            width: 44px;
            height: 44px;
            border-radius: 12px;
            display: flex;
            justify-content: center;
            align-items: center;
            font-size: 22px;
            color: white;
            flex-shrink: 0;
        }

        .retail-primary .retail-icon {
            background: #0d6efd;
        }

        .retail-success .retail-icon {
            background: #198754;
        }

        .retail-warning .retail-icon {
            background: #fd7e14;
        }

        .retail-info .retail-icon {
            background: #0dcaf0;
        }

        .retail-label {
            margin-bottom: 6px;
            color: #6c757d;
            font-weight: 600;
        }

        .retail-value {
            margin: 0;
            font-weight: 800;
            color: #1f2937;
        }

        .retail-banner {
            background: white;
            border-radius: 24px;
            padding: 35px;
            position: relative;
            overflow: hidden;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.05);
        }

        .banner-pattern {
            position: absolute;
            right: -50px;
            bottom: -50px;
            width: 220px;
            height: 220px;
            border-radius: 50%;
            background: rgba(13, 110, 253, 0.05);
        }

        .banner-content {
            position: relative;
            z-index: 2;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 20px;
        }

        .banner-icons {
            display: flex;
            gap: 18px;
            font-size: 40px;
            color: #0d6efd;
            opacity: 0.8;
        }

        @media(max-width:768px) {

            .hero-content {
                flex-direction: column;
                align-items: flex-start;
            }

            .hero-icon {
                display: none;
            }

            .retail-card {
                padding: 22px;
            }

        }
    </style>
@endsection
