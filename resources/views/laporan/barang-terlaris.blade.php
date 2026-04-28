@extends('layouts.admin')

@section('content')
    {{-- CHART JS --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <div class="container-fluid">

        {{-- HEADER --}}
        <div class="d-flex justify-content-between align-items-center mb-4">

            <div>
                <h3 class="fw-bold mb-1">
                    Laporan Barang Terlaris
                </h3>

                <small class="text-muted">
                    Ranking barang berdasarkan penjualan terbanyak tiap bulan
                </small>
            </div>

            <a href="{{ route('laporan.barang-terlaris.pdf', [
                'bulan' => request('bulan'),
                'tahun' => request('tahun'),
            ]) }}"
                class="btn btn-danger shadow-sm">

                <i class="bi bi-file-earmark-pdf"></i>
                Export PDF
            </a>

        </div>

        {{-- FILTER --}}
        <div class="card border-0 shadow-sm mb-4">

            <div class="card-body">

                <form method="GET">

                    <div class="row">

                        <div class="col-md-4">

                            <label class="fw-semibold mb-2">
                                Pilih Bulan
                            </label>

                            <select name="bulan" class="form-select">

                                @for ($i = 1; $i <= 12; $i++)
                                    <option value="{{ $i }}" {{ $bulan == $i ? 'selected' : '' }}>

                                        {{ DateTime::createFromFormat('!m', $i)->format('F') }}

                                    </option>
                                @endfor

                            </select>

                        </div>

                        <div class="col-md-4">

                            <label class="fw-semibold mb-2">
                                Pilih Tahun
                            </label>

                            <input type="number" name="tahun" value="{{ $tahun }}" class="form-control">

                        </div>

                        <div class="col-md-4 d-flex align-items-end">

                            <button class="btn btn-success w-100 shadow-sm">

                                <i class="bi bi-funnel"></i>
                                Filter Data

                            </button>

                        </div>

                    </div>

                </form>

            </div>

        </div>

        @php

            $totalTerjual = $laporans->sum('total_terjual');
            $totalOmzet = $laporans->sum('total_omzet');
            $totalLaba = $laporans->sum('total_laba');
            $jumlahBarang = $laporans->count();

        @endphp

        {{-- SUMMARY CARD --}}
        <div class="row mb-4">

            <div class="col-md-3 mb-3">

                <div class="card border-0 shadow-sm h-100">

                    <div class="card-body">

                        <div class="d-flex justify-content-between">

                            <div>

                                <small class="text-muted">
                                    Total Terjual
                                </small>

                                <h3 class="fw-bold">
                                    {{ number_format($totalTerjual) }}
                                </h3>

                            </div>

                            <div class="fs-1 text-primary">
                                <i class="bi bi-cart-fill"></i>
                            </div>

                        </div>

                    </div>

                </div>

            </div>

            <div class="col-md-3 mb-3">

                <div class="card border-0 shadow-sm h-100">

                    <div class="card-body">

                        <div class="d-flex justify-content-between">

                            <div>

                                <small class="text-muted">
                                    Total Omzet
                                </small>

                                <h4 class="fw-bold">
                                    Rp {{ number_format($totalOmzet, 0, ',', '.') }}
                                </h4>

                            </div>

                            <div class="fs-1 text-success">
                                <i class="bi bi-cash-stack"></i>
                            </div>

                        </div>

                    </div>

                </div>

            </div>

            <div class="col-md-3 mb-3">

                <div class="card border-0 shadow-sm h-100">

                    <div class="card-body">

                        <div class="d-flex justify-content-between">

                            <div>

                                <small class="text-muted">
                                    Total Laba
                                </small>

                                <h4 class="fw-bold">
                                    Rp {{ number_format($totalLaba, 0, ',', '.') }}
                                </h4>

                            </div>

                            <div class="fs-1 text-warning">
                                <i class="bi bi-bar-chart-fill"></i>
                            </div>

                        </div>

                    </div>

                </div>

            </div>

            <div class="col-md-3 mb-3">

                <div class="card border-0 shadow-sm h-100">

                    <div class="card-body">

                        <div class="d-flex justify-content-between">

                            <div>

                                <small class="text-muted">
                                    Jumlah Barang
                                </small>

                                <h3 class="fw-bold">
                                    {{ $jumlahBarang }}
                                </h3>

                            </div>

                            <div class="fs-1 text-danger">
                                <i class="bi bi-box-seam-fill"></i>
                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </div>

        {{-- CHART --}}
        <div class="row mb-4">

            {{-- BAR CHART --}}
            <div class="col-md-7 mb-3">

                <div class="card border-0 shadow-sm h-100">

                    <div class="card-body">

                        <h5 class="fw-bold mb-4">
                            Grafik Barang Terlaris
                        </h5>

                        <canvas id="bestSellerChart" height="130"></canvas>

                    </div>

                </div>

            </div>

            {{-- PIE CHART --}}
            <div class="col-md-5 mb-3">

                <div class="card border-0 shadow-sm h-100">

                    <div class="card-body">

                        <h5 class="fw-bold mb-4">
                            Persentase Penjualan
                        </h5>

                        <canvas id="pieChart"></canvas>

                    </div>

                </div>

            </div>

        </div>

        {{-- TABLE --}}
        <div class="card border-0 shadow-sm">

            <div class="card-body">

                <div class="table-responsive">

                    <table class="table align-middle table-hover">

                        <thead class="table-dark">

                            <tr>

                                <th>Ranking</th>
                                <th>Nama Barang</th>
                                <th>Total Terjual</th>
                                <th>Total Omzet</th>
                                <th>Total Laba</th>

                            </tr>

                        </thead>

                        <tbody>

                            @forelse($laporans as $item)
                                <tr>

                                    <td>
                                        <span class="badge bg-primary fs-6">
                                            #{{ $loop->iteration }}
                                        </span>
                                    </td>

                                    <td class="fw-semibold">
                                        {{ $item->nama_barang }}
                                    </td>

                                    <td>
                                        {{ number_format($item->total_terjual) }}
                                    </td>

                                    <td>
                                        Rp {{ number_format($item->total_omzet, 0, ',', '.') }}
                                    </td>

                                    <td>
                                        Rp {{ number_format($item->total_laba, 0, ',', '.') }}
                                    </td>

                                </tr>

                            @empty

                                <tr>

                                    <td colspan="5" class="text-center py-5">

                                        <div class="text-muted">

                                            <i class="bi bi-bar-chart fs-1"></i>

                                            <p class="mt-2">
                                                Tidak ada data penjualan
                                            </p>

                                        </div>

                                    </td>

                                </tr>
                            @endforelse

                        </tbody>

                    </table>

                </div>

            </div>

        </div>

    </div>

    {{-- CHART SCRIPT --}}
    <script>
        const labels = [
            @foreach ($laporans as $item)
                "{{ $item->nama_barang }}",
            @endforeach
        ];

        const totalTerjual = [
            @foreach ($laporans as $item)
                {{ $item->total_terjual }},
            @endforeach
        ];

        // BAR CHART
        new Chart(document.getElementById('bestSellerChart'), {

            type: 'bar',

            data: {

                labels: labels,

                datasets: [{

                    label: 'Jumlah Terjual',

                    data: totalTerjual,

                    borderRadius: 10,

                    backgroundColor: [
                        '#0d6efd',
                        '#198754',
                        '#ffc107',
                        '#dc3545',
                        '#6f42c1',
                        '#20c997',
                        '#fd7e14',
                        '#6610f2',
                        '#0dcaf0',
                        '#d63384'
                    ]

                }]

            },

            options: {

                responsive: true,

                plugins: {

                    legend: {
                        display: false
                    }

                }

            }

        });

        // PIE CHART
        new Chart(document.getElementById('pieChart'), {

            type: 'doughnut',

            data: {

                labels: labels,

                datasets: [{

                    data: totalTerjual,

                    backgroundColor: [
                        '#0d6efd',
                        '#198754',
                        '#ffc107',
                        '#dc3545',
                        '#6f42c1',
                        '#20c997',
                        '#fd7e14',
                        '#6610f2',
                        '#0dcaf0',
                        '#d63384'
                    ]

                }]

            },

            options: {

                responsive: true,

                plugins: {

                    legend: {
                        position: 'bottom'
                    }

                }

            }

        });
    </script>
@endsection
