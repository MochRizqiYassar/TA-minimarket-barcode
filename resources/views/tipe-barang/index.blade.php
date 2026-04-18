    @extends('layouts.admin')

@section('content')
<div class="container">
    <h2>Tipe Barang</h2>

    <a href="{{ route('tipe-barang.create') }}" class="btn btn-primary mb-3">+ Tambah</a>

    <table class="table">
        @foreach($tipeBarangs as $t)
        <tr>
            <td>{{ $t->nama_tipe }}</td>
            <td>
                <a href="{{ route('tipe-barang.edit',$t) }}" class="btn btn-warning btn-sm">Edit</a>

                <form action="{{ route('tipe-barang.destroy',$t) }}" method="POST" class="d-inline">
                    @csrf @method('DELETE')
                    <button class="btn btn-danger btn-sm">Hapus</button>
                </form>
            </td>
        </tr>
        @endforeach
    </table>
</div>
@endsection
