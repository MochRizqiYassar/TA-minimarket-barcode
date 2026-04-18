@extends('layouts.admin')

@section('content')
    <div class="container">
        <h2>Tambah Barang</h2>

        <form action="{{ route('barang.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <input type="text" name="barcode" placeholder="Barcode" class="form-control mb-2" required>

            <input type="text" name="nama_barang" placeholder="Nama Barang" class="form-control mb-2" required>

            <select name="id_kategori" class="form-control mb-2" required>
                @foreach ($kategoris as $k)
                    <option value="{{ $k->id_kategori }}">{{ $k->nama_kategori }}</option>
                @endforeach
            </select>

            <select name="id_tipe_barang" class="form-control mb-2" required>
                @foreach ($tipeBarangs as $t)
                    <option value="{{ $t->id_tipe_barang }}">{{ $t->nama_tipe }}</option>
                @endforeach
            </select>

            <input type="number" name="harga_beli" placeholder="Harga Beli" class="form-control mb-2" required>

            <input type="number" name="harga_jual" placeholder="Harga Jual" class="form-control mb-2" required>

            <input type="file" name="foto" class="form-control mb-2">

            <button class="btn btn-success">Simpan</button>
        </form>
    </div>
@endsection
