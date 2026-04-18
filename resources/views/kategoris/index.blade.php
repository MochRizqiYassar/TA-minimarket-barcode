
    @extends('layouts.admin')

@section('content')
<div class="container">
    <h2>Kategori</h2>

    <a href="{{ route('kategoris.create') }}" class="btn btn-primary mb-3">+ Tambah</a>

    <table class="table">
        @foreach($kategoris as $k)
        <tr>
            <td>{{ $k->nama_kategori }}</td>
            <td>
                <a href="{{ route('kategoris.edit',$k) }}" class="btn btn-warning btn-sm">Edit</a>

                <form action="{{ route('kategoris.destroy',$k) }}" method="POST" class="d-inline">
                    @csrf @method('DELETE')
                    <button class="btn btn-danger btn-sm">Hapus</button>
                </form>
            </td>
        </tr>
        @endforeach
    </table>
</div>
@endsection
