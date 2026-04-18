
    @extends('layouts.admin')

@section('content')
<div class="container">
    <h2>Detail Supplier</h2>

    <p><strong>Nama:</strong> {{ $supplier->nama_supplier }}</p>
    <p><strong>Kontak:</strong> {{ $supplier->kontak }}</p>
    <p><strong>Alamat:</strong> {{ $supplier->alamat }}</p>

    <hr>
    <h4>Riwayat Kulakan</h4>

    <table class="table">
        <thead>
            <tr>
                <th>Tanggal</th>
                <th>Status</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($supplier->kulakans as $k)
            <tr>
                <td>{{ $k->tanggal_kulakan }}</td>
                <td>{{ $k->status }}</td>
                <td>Rp {{ number_format($k->total_harga,0,',','.') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
