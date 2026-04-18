    @extends('layouts.admin')

@section('content')
<div class="container">
    <h2>Edit Tipe Barang</h2>

    <form action="{{ route('tipe-barang.update', $tipeBarang) }}" method="POST">
        @csrf @method('PUT')

        <div class="mb-3">
            <label>Nama Tipe</label>
            <input type="text" name="nama_tipe"
                value="{{ $tipeBarang->nama_tipe }}"
                class="form-control" required>
        </div>

        <button class="btn btn-success">Update</button>
    </form>
</div>
@endsection
