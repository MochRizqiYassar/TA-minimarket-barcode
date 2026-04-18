
    @extends('layouts.admin')

@section('content')
<div class="container">
    <h2>Data Kulakan</h2>

    <a href="{{ route('kulakan.create') }}" class="btn btn-primary mb-3">+ Tambah Kulakan</a>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>No</th>
                <th>Supplier</th>
                <th>Tanggal</th>
                <th>Status</th>
                <th>Total Harga</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach($kulakans as $k)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $k->supplier->nama_supplier }}</td>
                <td>{{ $k->tanggal_kulakan }}</td>
                <td>
                    <span class="badge bg-{{ $k->status == 'approved' ? 'success' : 'warning' }}">
                        {{ $k->status }}
                    </span>
                </td>
                <td>Rp {{ number_format($k->total_harga,0,',','.') }}</td>
                <td>
                    <a href="{{ route('kulakan.show',$k) }}" class="btn btn-info btn-sm">Detail</a>

                    @if($k->status == 'pending')
                        <a href="{{ route('kulakan.edit',$k) }}" class="btn btn-warning btn-sm">Edit</a>

                        <form action="{{ route('kulakan.destroy',$k) }}" method="POST" class="d-inline">
                            @csrf @method('DELETE')
                            <button class="btn btn-danger btn-sm">Hapus</button>
                        </form>

                        <form action="{{ route('kulakan.approve',$k) }}" method="POST" class="d-inline">
                            @csrf
                            <button class="btn btn-success btn-sm">Approve</button>
                        </form>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
