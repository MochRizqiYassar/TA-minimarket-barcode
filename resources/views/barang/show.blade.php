@extends('layouts.admin')

@section('content')
<div class="container">
    <h2>Detail Barang</h2>

    <a href="{{ route('barang.index') }}" class="btn btn-secondary mb-3">← Kembali</a>

    <div class="card p-3">

        {{-- FOTO --}}
        @if($barang->foto)
            <div class="mb-3">
                <img src="{{ asset('storage/' . $barang->foto) }}" width="150" class="rounded shadow">
            </div>
        @endif

        {{-- DATA BARANG --}}
        <table class="table table-bordered">
            <tr>
                <th>Barcode</th>
                <td>{{ $barang->barcode }}</td>
            </tr>
            <tr>
                <th>Nama Barang</th>
                <td>{{ $barang->nama_barang }}</td>
            </tr>
            <tr>
                <th>Kategori</th>
                <td>{{ $barang->kategori->nama_kategori }}</td>
            </tr>
            <tr>
                <th>Tipe</th>
                <td>{{ $barang->tipeBarang->nama_tipe }}</td>
            </tr>
            <tr>
                <th>Stok</th>
                <td>{{ $barang->stok }}</td>
            </tr>
            <tr>
                <th>Harga Beli</th>
                <td>Rp {{ number_format($barang->harga_beli) }}</td>
            </tr>
            <tr>
                <th>Harga Jual</th>
                <td>Rp {{ number_format($barang->harga_jual) }}</td>
            </tr>
        </table>

        {{-- EXPIRED --}}
        <h5 class="mt-4">Daftar Tanggal Expired</h5>

        @if($barang->barangMasuks->count() > 0)
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Tanggal Masuk</th>
                        <th>Tanggal Expired</th>
                        <th>Jumlah</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($barang->barangMasuks as $i => $bm)
                    <tr>
                        <td>{{ $i+1 }}</td>
                        <td>{{ $bm->tanggal_masuk }}</td>
                        <td>
                            {{ $bm->tanggal_expired?->format('d-m-Y') ?? '-' }}
                        </td>
                        <td>{{ $bm->jumlah }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <div class="alert alert-info">
                Belum ada data barang masuk
            </div>
        @endif

    </div>
</div>
@endsection
