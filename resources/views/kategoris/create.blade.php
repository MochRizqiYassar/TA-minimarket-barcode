
    @extends('layouts.admin')

@section('content')
<div class="container">
    <h2>Tambah Kategori</h2>

    <form action="{{ route('kategoris.store') }}" method="POST">
        @csrf

        <div class="mb-3">
            <label>Nama Kategori</label>
            <input type="text" name="nama_kategori" class="form-control" required>
        </div>

        <button class="btn btn-success">Simpan</button>
    </form>
</div>
@endsection
