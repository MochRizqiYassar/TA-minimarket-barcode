@extends('layouts.admin')

@section('content')
    <div class="container-fluid px-2 px-md-4">
        <h2>Data Barang</h2>

        <a href="{{ route('barang.create') }}" class="btn btn-primary mb-3">+ Tambah Barang</a>

        <div class="container-fluid">

            <div class="card shadow-sm border-0">

                <!-- BODY -->
                <div class="card-body">

                    <div class="card shadow-sm border-0 card-table">

                        <div class="card-body">

                            <div class="table-responsive">

                                <table class="table table-hover align-middle">

                                    <thead class="table-light">
                                        <tr>
                                            <th>Foto</th>
                                            <th>Barcode</th>
                                            <th>Nama</th>
                                            <th>Kategori</th>
                                            <th>Tipe</th>
                                            <th>Stok</th>
                                            <th>Harga Beli</th>
                                            <th>Harga Jual</th>
                                            <th class="text-center">Aksi</th>
                                        </tr>
                                    </thead>

                                    <tbody>
                                        @foreach ($barangs as $b)
                                            <tr>

                                                <!-- FOTO -->
                                                <td>
                                                    @if ($b->foto)
                                                        <img src="{{ asset('storage/' . $b->foto) }}" width="55"
                                                            height="55" class="rounded border" style="object-fit:cover;">
                                                    @else
                                                        <span class="text-muted small">
                                                            Tidak ada
                                                        </span>
                                                    @endif
                                                </td>

                                                <!-- BARCODE -->
                                                <td class="text-center">

                                                    <div class="barcode-wrapper">
                                                        {!! DNS1D::getBarcodeHTML($b->barcode, 'C128', 1.3, 35) !!}
                                                    </div>

                                                    <small>
                                                        {{ $b->barcode }}
                                                    </small>

                                                </td>

                                                <!-- DATA -->
                                                <td>{{ $b->nama_barang }}</td>

                                                <td>
                                                    <span class="badge bg-info">
                                                        {{ $b->kategori->nama_kategori }}
                                                    </span>
                                                </td>

                                                <td>{{ $b->tipeBarang->nama_tipe }}</td>

                                                <td>
                                                    <span class="badge bg-success">
                                                        {{ $b->stok }}
                                                    </span>
                                                </td>

                                                <td>
                                                    Rp {{ number_format($b->harga_beli) }}
                                                </td>

                                                <td>
                                                    Rp {{ number_format($b->harga_jual) }}
                                                </td>

                                                <!-- AKSI -->
                                                <td>

                                                    <div class="d-flex gap-1 justify-content-center">

                                                        <a href="{{ route('barang.show', $b) }}"
                                                            class="btn btn-info btn-sm aksi-btn">
                                                            <i class="bi bi-eye"></i>
                                                        </a>

                                                        <a href="{{ route('barang.edit', $b) }}"
                                                            class="btn btn-warning btn-sm aksi-btn">
                                                            <i class="bi bi-pencil"></i>
                                                        </a>

                                                        <form action="{{ route('barang.destroy', $b) }}" method="POST"
                                                            onsubmit="return confirm('Yakin hapus data?')">

                                                            @csrf
                                                            @method('DELETE')

                                                            <button class="btn btn-danger btn-sm aksi-btn">
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

                    <!-- PAGINATION -->
                    <div class="d-flex justify-content-center mt-4">
                        {{ $barangs->links() }}
                    </div>

                </div>
            </div>

        </div>
        <div class="d-flex justify-content-center mt-3">
            {{ $barangs->links() }}
        </div>
    </div>
@endsection
