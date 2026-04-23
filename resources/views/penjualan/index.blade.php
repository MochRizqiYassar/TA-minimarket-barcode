@extends('layouts.kasir')

@section('content')
    <div class="container">
        <h4>Data Penjualan</h4>

        <a href="{{ route('penjualan.create') }}" class="btn btn-primary mb-3">
            + Transaksi Baru
        </a>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Tanggal</th>
                    <th>Kasir</th>
                    <th>Total</th>
                    <th>Status</th>
                    <th>Aksi</th>

                </tr>
            </thead>
            <tbody>
                @foreach ($penjualans as $p)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $p->tanggal_penjualan }}</td>
                        <td>{{ $p->user->name ?? '-' }}</td>
                        <td>Rp {{ number_format($p->total_harga, 0, ',', '.') }}</td>
                        <td>
                            @if ($p->status == 'pending')
                                <span class="badge bg-warning">Pending</span>
                            @else
                                <span class="badge bg-success">Approved</span>
                            @endif
                        </td>

                        <td>
                            @if ($p->status == 'pending')
                                <form action="{{ route('penjualan.approve', $p->id_penjualan) }}" method="POST"
                                    class="d-inline">
                                    @csrf
                                    <button class="btn btn-success btn-sm">Approve</button>
                                </form>

                                <a href="{{ route('penjualan.edit', $p->id_penjualan) }}"
                                    class="btn btn-warning btn-sm">Edit</a>
                            @endif

                            <a href="{{ route('penjualan.show', $p->id_penjualan) }}" class="btn btn-info btn-sm">Detail</a>

                            <form action="{{ route('penjualan.destroy', $p->id_penjualan) }}" method="POST"
                                class="d-inline">
                                @csrf @method('DELETE')
                                <button onclick="return confirm('Yakin?')" class="btn btn-danger btn-sm">Hapus</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection
