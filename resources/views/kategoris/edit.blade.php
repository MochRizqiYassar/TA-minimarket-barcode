    @extends('layouts.admin')

@section('content')
<div class="container">
    <h2>Edit Kategori</h2>

    <form action="{{ route('kategoris.update', $kategori) }}" method="POST">
        @csrf @method('PUT')

        <div class="mb-3">
            <label>Nama Kategori</label>
            <input type="text" name="nama_kategori"
                value="{{ $kategori->nama_kategori }}"
                class="form-control" required>
        </div>

        <button class="btn btn-success">Update</button>
    </form>
</div>
@endsection
