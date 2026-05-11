@extends('layouts.admin')

@section('content')

@php
    use App\Models\Barang;
    use App\Models\BarangMasuk;
    use App\Models\Supplier;
    use App\Models\Penjualan;

    $totalBarang = Barang::count();

    $penjualanHariIni = Penjualan::whereDate('created_at', today())->count();

    $barangMasukHariIni = BarangMasuk::whereDate('created_at', today())->count();

    $totalSupplier = Supplier::count();
@endphp

<section class="section">

    <!-- HEADER -->
    <div class="dashboard-header mb-4">

        <div>

            <h2 class="fw-bold mb-1 text-dark">
                Dashboard Arthapura Retail
            </h2>

            <p class="text-muted mb-0">
                Sistem manajemen toko retail modern
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
                    Selamat Datang,
                    {{ auth()->user()->name }}
                </h3>

                <p class="text-white opacity-75 mb-0">
                    Pantau stok, penjualan, dan operasional toko dalam satu dashboard.
                </p>

            </div>

            <div class="hero-icon">

                <i class="bi bi-shop"></i>

            </div>

        </div>

    </div>

    <!-- STATISTIC -->
    <div class="row g-4">

        <!-- TOTAL BARANG -->
        <div class="col-12 col-md-6 col-xl-3">

            <div class="retail-card retail-primary">

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

        <!-- PENJUALAN -->
        <div class="col-12 col-md-6 col-xl-3">

            <div class="retail-card retail-success">

                <div class="retail-icon">

                    <i class="bi bi-cart-check"></i>

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

        <!-- BARANG MASUK -->
        <div class="col-12 col-md-6 col-xl-3">

            <div class="retail-card retail-warning">

                <div class="retail-icon">

                    <i class="bi bi-box-arrow-in-down"></i>

                </div>

                <div>

                    <p class="retail-label">
                        Barang Masuk Hari Ini
                    </p>

                    <h2 class="retail-value">
                        {{ $barangMasukHariIni }}
                    </h2>

                </div>

            </div>

        </div>

        <!-- SUPPLIER -->
        <div class="col-12 col-md-6 col-xl-3">

            <div class="retail-card retail-info">

                <div class="retail-icon">

                    <i class="bi bi-truck"></i>

                </div>

                <div>

                    <p class="retail-label">
                        Total Supplier
                    </p>

                    <h2 class="retail-value">
                        {{ $totalSupplier }}
                    </h2>

                </div>

            </div>

        </div>

    </div>

    <!-- DECORATION -->
    <div class="row mt-4">

        <div class="col-12">

            <div class="retail-banner">

                <div class="banner-pattern"></div>

                <div class="banner-content">

                    <div>

                        <h4 class="fw-bold mb-2">
                            Arthapura Retail Management
                        </h4>

                        <p class="mb-0 text-muted">
                            Kelola inventori, stok, dan transaksi toko lebih cepat dan efisien.
                        </p>

                    </div>

                    <div class="banner-icons">

                        <i class="bi bi-upc-scan"></i>
                        <i class="bi bi-receipt"></i>
                        <i class="bi bi-bag-check"></i>

                    </div>

                </div>

            </div>

        </div>

    </div>

</section>

<style>

.dashboard-header{
    display:flex;
    justify-content:space-between;
    align-items:center;
    flex-wrap:wrap;
    gap:10px;
}

.dashboard-date{
    background:white;
    padding:10px 16px;
    border-radius:12px;
    box-shadow:0 2px 10px rgba(0,0,0,0.05);
    font-weight:600;
}

.retail-hero{
    position:relative;
    overflow:hidden;
    border-radius:24px;
    padding:40px;
    background:linear-gradient(135deg,#3b82f6,#1e3a8a);
    min-height:220px;
}

.hero-overlay{
    position:absolute;
    top:0;
    right:0;
    width:300px;
    height:300px;
    background:rgba(255,255,255,0.08);
    border-radius:50%;
    transform:translate(100px,-100px);
}

.hero-content{
    position:relative;
    z-index:2;
    display:flex;
    justify-content:space-between;
    align-items:center;
}

.hero-icon{
    font-size:90px;
    color:rgba(255,255,255,0.2);
}

.retail-card{
    background:white;
    border-radius:22px;
    padding:28px;
    display:flex;
    align-items:center;
    gap:20px;
    box-shadow:0 5px 20px rgba(0,0,0,0.05);
    transition:0.3s;
    position:relative;
    overflow:hidden;
}

.retail-card:hover{
    transform:translateY(-5px);
}

.retail-card::before{
    content:'';
    position:absolute;
    top:0;
    left:0;
    width:6px;
    height:100%;
}

.retail-primary::before{
    background:#0d6efd;
}

.retail-success::before{
    background:#198754;
}

.retail-warning::before{
    background:#fd7e14;
}

.retail-info::before{
    background:#0dcaf0;
}

.retail-icon{
    width:65px;
    height:65px;
    border-radius:18px;
    display:flex;
    justify-content:center;
    align-items:center;
    font-size:28px;
    color:white;
}

.retail-primary .retail-icon{
    background:#0d6efd;
}

.retail-success .retail-icon{
    background:#198754;
}

.retail-warning .retail-icon{
    background:#fd7e14;
}

.retail-info .retail-icon{
    background:#0dcaf0;
}

.retail-label{
    margin-bottom:6px;
    color:#6c757d;
    font-weight:600;
}

.retail-value{
    margin:0;
    font-weight:800;
    color:#1f2937;
}

.retail-banner{
    background:white;
    border-radius:24px;
    padding:35px;
    position:relative;
    overflow:hidden;
    box-shadow:0 5px 20px rgba(0,0,0,0.05);
}

.banner-pattern{
    position:absolute;
    right:-50px;
    bottom:-50px;
    width:220px;
    height:220px;
    border-radius:50%;
    background:rgba(13,110,253,0.05);
}

.banner-content{
    position:relative;
    z-index:2;
    display:flex;
    justify-content:space-between;
    align-items:center;
    flex-wrap:wrap;
    gap:20px;
}

.banner-icons{
    display:flex;
    gap:18px;
    font-size:40px;
    color:#0d6efd;
    opacity:0.8;
}

@media(max-width:768px){

    .hero-content{
        flex-direction:column;
        align-items:flex-start;
    }

    .hero-icon{
        display:none;
    }

    .retail-card{
        padding:22px;
    }

}

</style>

@endsection
