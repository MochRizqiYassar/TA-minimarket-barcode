@extends('layouts.kasir')

@section('content')
<div class="container">
    <h4>Detail Penjualan</h4>

    <p><strong>Tanggal:</strong> {{ $penjualan->tanggal_penjualan }}</p>
    <p><strong>Kasir:</strong> {{ $penjualan->user->name ?? '-' }}</p>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Barang</th>
                <th>Jumlah</th>
                <th>Harga</th>
                <th>Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach($penjualan->detailPenjualans as $d)
            <tr>
                <td>{{ $d->barang?->nama_barang ?? $d->nama_barang ?? 'Barang sudah dihapus' }}</td>
                <td>{{ $d->jumlah }}</td>
                <td>Rp {{ number_format($d->harga,0,',','.') }}</td>
                <td>Rp {{ number_format($d->subtotal,0,',','.') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <h5>Total: Rp {{ number_format($penjualan->total_harga,0,',','.') }}</h5>
</div>
@endsection
