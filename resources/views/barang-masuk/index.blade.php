
    @extends('layouts.admin')

@section('content')
<div class="container">
    <h2>Barang Masuk</h2>

    <a href="{{ route('barang-masuk.create') }}" class="btn btn-primary mb-3">+ Tambah</a>

    <table class="table">
        <thead>
            <tr>
                <th>Barang</th>
                <th>Harga Beli</th>
                <th>Supplier</th>
                <th>Jumlah</th>
                <th>Tanggal</th>
                <th>Status</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach($barangMasuks as $bm)
            <tr>
                <td>{{ $bm->nama_barang }}</td>

<td>Rp {{ number_format($bm->harga_beli) }}</td>
                <td>{{ $bm->kulakan->supplier->nama_supplier }}</td>
                <td>{{ $bm->jumlah }}</td>
                <td>{{ $bm->tanggal_masuk }}</td>
                <td>
    @if($bm->status == 'pending')
        <span class="badge bg-warning">Pending</span>
    @else
        <span class="badge bg-success">Approved</span>
    @endif
</td>

<td>
    @if($bm->status == 'pending')
        <a href="{{ route('barang-masuk.edit',$bm) }}" class="btn btn-warning btn-sm">Edit</a>

        <form action="{{ route('barang-masuk.approve', $bm) }}" method="POST" class="d-inline">
            @csrf
            <button class="btn btn-success btn-sm">Approve</button>
        </form>
    @endif

    <form action="{{ route('barang-masuk.destroy',$bm) }}" method="POST" class="d-inline">
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
