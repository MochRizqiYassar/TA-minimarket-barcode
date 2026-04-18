@extends('layouts.admin')

@section('content')
<div class="container">
    <h2>Ambil Barang ke Etalase</h2>

    <form action="{{ route('barang-masuk.store') }}" method="POST">
        @csrf

        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Barang</th>
                    <th>Total Stok Kulakan</th>
                    <th>Qty Ambil</th>
                    <th>Tanggal Expired</th>
                </tr>
            </thead>
            <tbody>

                @foreach($details as $item)
<tr>
    <td>{{ $item['nama_barang'] }}</td>

    <td>{{ $item['stok'] }}</td>

    <td>
        <input type="hidden"
               name="items[{{ $item['id_barang'] }}][id_barang]"
               value="{{ $item['id_barang'] }}">

        <input type="number"
               name="items[{{ $item['id_barang'] }}][jumlah]"
               class="form-control"
               min="0">
    </td>

    <td>
        <input type="date"
               name="items[{{ $item['id_barang'] }}][tanggal_expired]"
               class="form-control">
    </td>
</tr>
@endforeach

            </tbody>
        </table>

        <button class="btn btn-success">Simpan</button>
    </form>
</div>
@endsection
