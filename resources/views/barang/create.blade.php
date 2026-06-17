@extends('layouts.admin')

@section('content')
    <div class="container">
        <h2>Tambah Barang</h2>

        {{-- ERROR --}}
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if (($kategoris ?? collect())->isEmpty() || ($tipeBarangs ?? collect())->isEmpty())
            <div class="alert alert-warning">
                Kategori dan/atau Tipe Barang belum ada. Tambahkan dahulu sebelum membuat barang baru.
            </div>
        @endif

        <form action="{{ route('barang.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <input type="text" name="barcode" value="{{ old('barcode') }}" placeholder="Barcode (kosongkan untuk auto)"
                class="form-control mb-2">

            <input type="text" name="nama_barang" value="{{ old('nama_barang') }}" placeholder="Nama Barang"
                class="form-control mb-2" required>

            <select name="id_kategori" class="form-control mb-2" required>
                <option value="" disabled {{ old('id_kategori') ? '' : 'selected' }}>-- Pilih Kategori --</option>
                @foreach ($kategoris as $k)
                    <option value="{{ $k->id_kategori }}" {{ old('id_kategori') == $k->id_kategori ? 'selected' : '' }}>
                        {{ $k->nama_kategori }}
                    </option>
                @endforeach
            </select>

            <select name="id_tipe_barang" class="form-control mb-2" required>
                <option value="" disabled {{ old('id_tipe_barang') ? '' : 'selected' }}>-- Pilih Tipe Barang --</option>
                @foreach ($tipeBarangs as $t)
                    <option value="{{ $t->id_tipe_barang }}"
                        {{ old('id_tipe_barang') == $t->id_tipe_barang ? 'selected' : '' }}>
                        {{ $t->nama_tipe }}
                    </option>
                @endforeach
            </select>

            <input type="number" name="harga_beli" value="{{ old('harga_beli') }}" placeholder="Harga Beli"
                class="form-control mb-2" required>

            <input type="number" name="harga_jual" value="{{ old('harga_jual') }}" placeholder="Harga Jual"
                class="form-control mb-2" required>

            <input type="file" name="foto" class="form-control mb-2">
            <small class="text-muted d-block mb-2">Maks. 2MB (jpg, jpeg, png)</small>

            <div class="mb-3">
                <label>Stok Minimum Etalase</label>
                <input type="number" name="stok_minimum_etalase" class="form-control"
                    value="{{ old('stok_minimum_etalase', 5) }}" min="0">
            </div>

            <div class="mb-3">
                <label>Stok Minimum Gudang</label>
                <input type="number" name="stok_minimum_gudang" class="form-control"
                    value="{{ old('stok_minimum_gudang', 10) }}" min="0">
            </div>

            <button class="btn btn-success">Simpan</button>
        </form>
    </div>
@endsection
