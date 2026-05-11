@extends('layouts.admin')

@section('title', 'Verifikasi Akun')

@section('content')

    <div class="card">
        <div class="card-header">
            <h4>Daftar Akun Kasir</h4>
        </div>

        <div class="card-body">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Nama</th>
                        <th>Email</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach ($users as $user)
                        <tr>
                            <td>{{ $user->name }}</td>
                            <td>{{ $user->email }}</td>

                            <td>
                                @if ($user->status == 'active')
                                    <span class="badge bg-success">Aktif</span>
                                @elseif ($user->status == 'pending')
                                    <span class="badge bg-warning">Pending</span>
                                @else
                                    <span class="badge bg-danger">Ditolak</span>
                                @endif
                            </td>

                            <td>
    <div class="d-flex gap-1">

        {{-- APPROVE --}}
        @if ($user->status != 'active')
            <form method="POST" action="{{ route('admin.users.approve', $user->id) }}">
                @csrf

                <button class="btn btn-success btn-sm">
                    Approve
                </button>
            </form>
        @endif

        {{-- TOLAK / HAPUS --}}
        <form method="POST" action="{{ route('admin.users.reject', $user->id) }}">
            @csrf
            @method('DELETE')

            <button class="btn btn-danger btn-sm"
                onclick="return confirm('Yakin ingin menghapus / menolak akun ini?')">
                Delete
            </button>
        </form>

    </div>
</td>
                        </tr>
                    @endforeach
                </tbody>

            </table>
        </div>
    </div>

@endsection
