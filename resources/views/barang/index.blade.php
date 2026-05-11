@extends('layouts.admin')

@section('content')
    <div class="container">
        <h2>Data Barang</h2>

        <a href="{{ route('barang.create') }}" class="btn btn-primary mb-3">+ Tambah Barang</a>

        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Foto</th> <!-- TAMBAHAN -->
                    <th>Barcode</th>
                    <th>Nama</th>
                    <th>Kategori</th>
                    <th>Tipe</th>
                    <th>Stok</th>
                    <th>Harga Beli</th> <!-- TAMBAHAN -->
                    <th>Harga Jual</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <a href="{{ route('barcode.form') }}" class="btn btn-secondary btn-sm">
                    Cetak Barcode
                </a>
                @foreach ($barangs as $b)
                    <tr>
                        <!-- FOTO -->
                        <td>
                            @if ($b->foto)
                                <img src="{{ asset('storage/' . $b->foto) }}" width="60" height="60"
                                    style="object-fit: cover;">
                            @else
                                <span class="text-muted">Tidak ada</span>
                            @endif
                        </td>

                        <td style="text-align:center">
                            <div style="display:inline-block">
                                {!! DNS1D::getBarcodeHTML($b->barcode, 'C128', 1.5, 40) !!}
                                <div style="font-size:12px; margin-top:2px;">
                                    {{ $b->barcode }}
                                </div>
                            </div>
                        </td>
                        <td>{{ $b->nama_barang }}</td>
                        <td>{{ $b->kategori->nama_kategori }}</td>
                        <td>{{ $b->tipeBarang->nama_tipe }}</td>
                        <td>{{ $b->stok }}</td>

                        <!-- HARGA BELI -->
                        <td>Rp {{ number_format($b->harga_beli) }}</td>

                        <td>Rp {{ number_format($b->harga_jual) }}</td>

                        <td>
                            <a href="{{ route('barang.show', $b) }}" class="btn btn-info btn-sm">Detail</a>
                            <a href="{{ route('barang.edit', $b) }}" class="btn btn-warning btn-sm">Edit</a>

                            <form action="{{ route('barang.destroy', $b) }}" method="POST" class="d-inline">
                                @csrf @method('DELETE')
                                <button class="btn btn-danger btn-sm">Hapus</button>
                            </form>
                        </td>

                    </tr>
                @endforeach
            </tbody>
        </table>
        <div class="d-flex justify-content-center mt-3">
            {{ $barangs->links() }}
        </div>
    </div>
@endsection
