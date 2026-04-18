    @extends('layouts.admin')

@section('content')
<div class="container">
    <h2>Tambah Tipe Barang</h2>

    <form action="{{ route('tipe-barang.store') }}" method="POST">
        @csrf

        <div class="mb-3">
            <label>Nama Tipe</label>
            <input type="text" name="nama_tipe" class="form-control" required>
        </div>

        <button class="btn btn-success">Simpan</button>
    </form>
</div>
@endsection
