@extends('layouts.admin')

@section('content')
    <div class="container">
        <h2>Edit Barang</h2>

        {{-- ERROR --}}
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('barang.update', $barang) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <input type="text" name="barcode" value="{{ old('barcode', $barang->barcode) }}" class="form-control mb-2"
                placeholder="Barcode">

            <input type="text" name="nama_barang" value="{{ old('nama_barang', $barang->nama_barang) }}"
                class="form-control mb-2" placeholder="Nama Barang">

            <select name="id_kategori" class="form-control mb-2">
                @foreach ($kategoris as $k)
                    <option value="{{ $k->id_kategori }}"
                        {{ old('id_kategori', $barang->id_kategori) == $k->id_kategori ? 'selected' : '' }}>
                        {{ $k->nama_kategori }}
                    </option>
                @endforeach
            </select>

            <select name="id_tipe_barang" class="form-control mb-2">
                @foreach ($tipeBarangs as $t)
                    <option value="{{ $t->id_tipe_barang }}"
                        {{ old('id_tipe_barang', $barang->id_tipe_barang) == $t->id_tipe_barang ? 'selected' : '' }}>
                        {{ $t->nama_tipe }}
                    </option>
                @endforeach
            </select>

            {{-- HARGA --}}
            <input type="number" name="harga_beli" value="{{ old('harga_beli', $barang->harga_beli) }}"
                class="form-control mb-2" placeholder="Harga Beli">

            <input type="number" name="harga_jual" value="{{ old('harga_jual', $barang->harga_jual) }}"
                class="form-control mb-2" placeholder="Harga Jual">

            {{-- FOTO LAMA --}}
            @if ($barang->foto)
                <div class="mb-2">
                    <img src="{{ asset('storage/' . $barang->foto) }}" width="100" class="rounded">
                </div>
            @endif

            {{-- FOTO BARU --}}
            <input type="file" name="foto" class="form-control mb-2">

            <button class="btn btn-success">Update</button>
        </form>
    </div>
@endsection
