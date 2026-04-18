
    @extends('layouts.admin')

@section('content')
<div class="container">
    <h2>Edit Supplier</h2>

    <form action="{{ route('suppliers.update', $supplier) }}" method="POST">
        @csrf @method('PUT')

        <div class="mb-3">
            <label>Nama Supplier</label>
            <input type="text" name="nama_supplier" class="form-control"
                value="{{ $supplier->nama_supplier }}" required>
        </div>

        <div class="mb-3">
            <label>Kontak</label>
            <input type="text" name="kontak" class="form-control"
                value="{{ $supplier->kontak }}" required>
        </div>

        <div class="mb-3">
            <label>Alamat</label>
            <textarea name="alamat" class="form-control" required>{{ $supplier->alamat }}</textarea>
        </div>

        <button class="btn btn-success">Update</button>
    </form>
</div>
@endsection
