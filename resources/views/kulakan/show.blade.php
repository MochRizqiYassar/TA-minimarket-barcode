
    @extends('layouts.admin')

@section('content')
<div class="container">
    <h2>Detail Kulakan</h2>

    <p>Supplier: {{ $kulakan->supplier->nama_supplier }}</p>
    <p>Tanggal: {{ $kulakan->tanggal_kulakan }}</p>
    <p>Status: {{ $kulakan->status }}</p>

    <table class="table">
        <thead>
            <tr>
                <th>Barang</th>
                <th>Tipe</th>
                <th>Qty</th>
                <th>Harga</th>
                <th>Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach($kulakan->detailKulakans as $d)
            <tr>
                <td>{{ $d->barang->nama_barang }}</td>
                <td>{{ $d->tipeBarang->nama_tipe }}</td>
                <td>{{ $d->banyak }}</td>
                <td>{{ $d->harga_satuan }}</td>
                <td>{{ $d->subtotal }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
