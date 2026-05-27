@extends('layouts.kasir')

@section('content')
    <div class="container-fluid py-3">

        <!-- HEADER -->
        <div class="d-flex justify-content-between align-items-center flex-wrap mb-3">

            <h3 class="fw-bold mb-2">
                Data Penjualan
            </h3>

            <a href="{{ route('penjualan.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-circle"></i>
                Transaksi Baru
            </a>

        </div>

        <!-- ALERT -->
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show">

                {{ session('success') }}

                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>

            </div>
        @endif

        <!-- CARD -->
        <div class="card border-0 shadow-sm rounded-4">

            <div class="card-body">

                <!-- TABLE -->
                <div class="table-responsive">

                    <table class="table table-hover align-middle">

                        <thead class="table-light">
                            <tr>
                                <th>No</th>
                                <th>Tanggal</th>
                                <th>Kasir</th>
                                <th>Total</th>
                                <th>Status</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>

                        <tbody id="penjualan-body">

                            @foreach ($penjualans as $p)
                                <tr>

                                    <!-- NO -->
                                    <td>
                                        {{ $loop->iteration }}
                                    </td>

                                    <!-- TANGGAL -->
                                    <td>
                                        {{ $p->tanggal_penjualan }}
                                    </td>

                                    <!-- KASIR -->
                                    <td class="fw-semibold">
                                        {{ $p->user->name ?? '-' }}
                                    </td>

                                    <!-- TOTAL -->
                                    <td class="fw-bold text-success">
                                        Rp {{ number_format($p->total_harga, 0, ',', '.') }}
                                    </td>

                                    <!-- STATUS -->
                                    <td>
                                        @if ($p->status == 'pending')
                                            <span class="badge bg-warning">
                                                Pending
                                            </span>
                                        @else
                                            <span class="badge bg-success">
                                                Approved
                                            </span>
                                        @endif
                                    </td>

                                    <!-- AKSI -->
                                    <td>

                                        <div class="d-flex justify-content-center gap-1 flex-wrap">

                                            @if ($p->status == 'pending')
                                                <!-- APPROVE -->
                                                <form action="{{ route('penjualan.approve', $p->id_penjualan) }}"
                                                    method="POST">

                                                    @csrf

                                                    <button class="btn btn-success btn-sm">
                                                        <i class="bi bi-check-circle"></i>
                                                    </button>

                                                </form>

                                                <!-- EDIT -->
                                                <a href="{{ route('penjualan.edit', $p->id_penjualan) }}"
                                                    class="btn btn-warning btn-sm">

                                                    <i class="bi bi-pencil"></i>

                                                </a>
                                            @endif

                                            <!-- DETAIL -->
                                            <a href="{{ route('penjualan.show', $p->id_penjualan) }}"
                                                class="btn btn-info btn-sm">

                                                <i class="bi bi-eye"></i>

                                            </a>

                                            <!-- HAPUS -->
                                            <form action="{{ route('penjualan.destroy', $p->id_penjualan) }}" method="POST"
                                                onsubmit="return confirm('Yakin hapus data?')">

                                                @csrf
                                                @method('DELETE')

                                                <button class="btn btn-danger btn-sm">

                                                    <i class="bi bi-trash"></i>

                                                </button>

                                            </form>

                                        </div>

                                    </td>

                                </tr>
                            @endforeach

                        </tbody>

                    </table>

                </div>

            </div>

        </div>

        <!-- SCRIPT OFFLINE -->
        <script>
            document.addEventListener('DOMContentLoaded', function() {

                let offlinePenjualans =
                    JSON.parse(localStorage.getItem('offline_penjualans')) || [];

                let tbody = document.getElementById('penjualan-body');

                offlinePenjualans.forEach((p, index) => {

                    tbody.innerHTML =
                        `
                    <tr style="background:#fff3cd;">

                        <td>OFFLINE</td>

                        <td>${p.tanggal_penjualan}</td>

                        <td>Kasir</td>

                        <td class="fw-bold text-warning">
                            Rp ${parseInt(p.total_harga).toLocaleString()}
                        </td>

                        <td>
                            <span class="badge bg-secondary">
                                Belum Sync
                            </span>
                        </td>

                        <td>
                            <button class="btn btn-secondary btn-sm" disabled>
                                Menunggu Online
                            </button>
                        </td>

                    </tr>
                    ` + tbody.innerHTML;

                });

            });
        </script>

    </div>
@endsection
