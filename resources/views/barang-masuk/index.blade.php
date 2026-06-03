@extends('layouts.admin')

@section('content')
    <div class="container-fluid py-3">

        <!-- HEADER -->
        <div class="d-flex justify-content-between align-items-center flex-wrap mb-3">

            <h2 class="fw-bold mb-2">
                Barang Masuk
            </h2>

            <a href="{{ route('barang-masuk.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-circle"></i>
                Tambah
            </a>

        </div>

        <!-- CARD -->
        <div class="card border-0 shadow-sm rounded-4">

            <div class="card-body">

                <!-- TABLE RESPONSIVE -->
                <div class="table-responsive">

                    <table class="table table-hover align-middle">

                        <thead class="table-light">
                            <tr>
                                <th>Barang</th>
                                <th>Harga Beli</th>
                                <th>Supplier</th>
                                <th>Jumlah</th>
                                <th>Tanggal</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>

                        <tbody>

                            @foreach ($barangMasuks as $bm)
                                <tr>

                                    <!-- BARANG -->
                                    <td class="fw-semibold">
                                        {{ $bm->nama_barang }}
                                    </td>

                                    <!-- HARGA -->
                                    <td class="text-success fw-bold">
                                        Rp {{ number_format($bm->harga_beli, 0, ',', '.') }}
                                    </td>

                                    <!-- SUPPLIER -->
                                    <td>
                                        <span class="badge bg-info">
                                            {{ $bm->kulakan->supplier->nama_supplier }}
                                        </span>
                                    </td>

                                    <!-- JUMLAH -->
                                    <td>
                                        <span class="badge bg-success">
                                            {{ $bm->jumlah }}
                                        </span>
                                    </td>

                                    <!-- TANGGAL -->
                                    <td>
                                        {{ \Carbon\Carbon::parse($bm->tanggal_masuk)->format('d/m/Y') }}
                                    </td>

                                    <!-- AKSI -->
                                    <td>

                                        <div class="d-flex justify-content-center">

                                            <form action="{{ route('barang-masuk.destroy', $bm) }}" method="POST"
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

    </div>
@endsection
