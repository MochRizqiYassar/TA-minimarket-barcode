
    @extends('layouts.admin')

@section('content')
<div class="container">
    <h2>Data Supplier</h2>

    <a href="{{ route('suppliers.create') }}" class="btn btn-primary mb-3">
        + Tambah Supplier
    </a>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>No</th>
                <th>Nama</th>
                <th>Kontak</th>
                <th>Alamat</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach($suppliers as $s)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $s->nama_supplier }}</td>
                <td>{{ $s->kontak }}</td>
                <td>{{ $s->alamat }}</td>
                <td>
                    <a href="{{ route('suppliers.show',$s) }}" class="btn btn-info btn-sm">Detail</a>
                    <a href="{{ route('suppliers.edit',$s) }}" class="btn btn-warning btn-sm">Edit</a>

                    <form action="{{ route('suppliers.destroy',$s) }}" method="POST" class="d-inline">
                        @csrf @method('DELETE')
                        <button class="btn btn-danger btn-sm">Hapus</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
