@extends('layouts.admin')

@section('content')
    <div class="container-fluid py-3">

        <!-- HEADER -->
        <div class="d-flex justify-content-between align-items-center flex-wrap mb-3">
            <h2 class="fw-bold mb-2">Data Kulakan</h2>

            <a href="{{ route('kulakan.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-circle"></i>
                Tambah Kulakan
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

                <!-- RESPONSIVE TABLE -->
                <div class="table-responsive">

                    <table class="table table-hover align-middle">

                        <thead class="table-light">
                            <tr>
                                <th>No</th>
                                <th>Supplier</th>
                                <th>Tanggal</th>
                                <th>Status</th>
                                <th>Total Harga</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach ($kulakans as $k)
                                <tr>

                                    <!-- NO -->
                                    <td>
                                        {{ $loop->iteration }}
                                    </td>

                                    <!-- SUPPLIER -->
                                    <td class="fw-semibold">
                                        {{ $k->supplier->nama_supplier }}
                                    </td>

                                    <!-- TANGGAL -->
                                    <td>
                                        {{ $k->tanggal_kulakan }}
                                    </td>

                                    <!-- STATUS -->
                                    <td>
                                        <span class="badge bg-{{ $k->status == 'approved' ? 'success' : 'warning' }}">
                                            {{ ucfirst($k->status) }}
                                        </span>
                                    </td>

                                    <!-- TOTAL -->
                                    <td class="fw-bold text-success">
                                        Rp {{ number_format($k->total_harga, 0, ',', '.') }}
                                    </td>

                                    <!-- AKSI -->
                                    <td>

                                        <div class="d-flex justify-content-center gap-1 flex-wrap">

                                            <!-- DETAIL -->
                                            <a href="{{ route('kulakan.show', $k) }}" class="btn btn-info btn-sm">
                                                <i class="bi bi-eye"></i>
                                            </a>

                                            @if ($k->status == 'pending')
                                                <!-- EDIT -->
                                                <a href="{{ route('kulakan.edit', $k) }}" class="btn btn-warning btn-sm">
                                                    <i class="bi bi-pencil"></i>
                                                </a>

                                                <!-- HAPUS -->
                                                <form action="{{ route('kulakan.destroy', $k) }}" method="POST"
                                                    onsubmit="return confirm('Yakin hapus data?')">

                                                    @csrf
                                                    @method('DELETE')

                                                    <button class="btn btn-danger btn-sm">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </form>

                                                <!-- APPROVE -->
                                                <form action="{{ route('kulakan.approve', $k) }}" method="POST">

                                                    @csrf

                                                    <button class="btn btn-success btn-sm">Approve</button>
                                                </form>
                                            @endif

                                        </div>

                                    </td>

                                </tr>
                            @endforeach
                        </tbody>

                    </table>

                </div>

            </div>

        </div>

    </div>
@endsection
