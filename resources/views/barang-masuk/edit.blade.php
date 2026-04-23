@extends('layouts.admin')

@section('content')
    <div class="container">
        <h2>Edit Tanggal Expired</h2>

        <a href="{{ route('barang-masuk.index') }}" class="btn btn-secondary mb-3">← Kembali</a>

        <div class="card p-3">

            <table class="table">
                <tr>
                    <th>Nama Barang</th>
                    <td>{{ $barangMasuk->nama_barang }}</td>
                </tr>
                <tr>
                    <th>Jumlah</th>
                    <td>{{ $barangMasuk->jumlah }}</td>
                </tr>
            </table>

            <form action="{{ route('barang-masuk.update', $barangMasuk) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="mb-3">
                    <label>Jumlah</label>
                    <input type="number" name="jumlah" value="{{ $barangMasuk->jumlah }}" class="form-control">
                </div>

                <div class="mb-3">
                    <label>Tanggal Expired</label>
                    <input type="date" name="tanggal_expired"
                        value="{{ $barangMasuk->tanggal_expired?->format('Y-m-d') }}" class="form-control">
                </div>

                <button class="btn btn-success">Update</button>
            </form>

        </div>
    </div>
@endsection
