@extends('layouts.admin')

@section('content')
    <div class="container-fluid">

        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4>Laporan Barang Masuk</h4>

            <a href="{{ route('laporan.barang-masuk.pdf', [
                'tanggal_awal' => request('tanggal_awal'),
                'tanggal_akhir' => request('tanggal_akhir'),
            ]) }}"
                class="btn btn-danger">

                Export PDF
            </a>
        </div>

        {{-- FILTER --}}
        <div class="card p-3 mb-3">
            <form method="GET">

                <div class="row">

                    <div class="col-md-4">
                        <label>Tanggal Awal</label>

                        <input type="date" name="tanggal_awal" value="{{ request('tanggal_awal') }}" class="form-control">
                    </div>

                    <div class="col-md-4">
                        <label>Tanggal Akhir</label>

                        <input type="date" name="tanggal_akhir" value="{{ request('tanggal_akhir') }}"
                            class="form-control">
                    </div>

                    <div class="col-md-4 d-flex align-items-end">
                        <button class="btn btn-success w-100">
                            Filter
                        </button>
                    </div>

                </div>

            </form>
        </div>

        {{-- TABLE --}}
        <div class="card p-3">

            <div class="table-responsive">

                <table class="table table-bordered table-striped">

                    <thead class="table-dark">
                        <tr>
                            <th>No</th>
                            <th>Tanggal</th>
                            <th>Nama Barang</th>
                            <th>Supplier</th>
                            <th>Jumlah</th>
                            <th>Expired</th>
                            <th>Harga Beli</th>
                            <th>Total</th>
                        </tr>
                    </thead>

                    <tbody>

                        @forelse ($laporans as $item)
                            <tr>
                                <td>{{ $loop->iteration }}</td>

                                <td>
                                    {{ \Carbon\Carbon::parse($item->tanggal_masuk)->format('d-m-Y') }}
                                </td>

                                <td>
                                    {{ $item->nama_barang }}
                                </td>

                                <td>
                                    {{ $item->kulakan->supplier->nama_supplier ?? '-' }}
                                </td>

                                <td>
                                    {{ $item->jumlah }}
                                </td>

                                <td>
                                    {{ $item->tanggal_expired ? \Carbon\Carbon::parse($item->tanggal_expired)->format('d-m-Y') : '-' }}
                                </td>

                                <td>
                                    Rp {{ number_format($item->harga_beli, 0, ',', '.') }}
                                </td>

                                <td>
                                    Rp {{ number_format($item->harga_beli * $item->jumlah, 0, ',', '.') }}
                                </td>
                            </tr>

                        @empty

                            <tr>
                                <td colspan="8" class="text-center">
                                    Tidak ada data
                                </td>
                            </tr>
                        @endforelse

                    </tbody>

                    <tfoot>
                        <tr>
                            <th colspan="4" class="text-end">
                                Total Barang
                            </th>

                            <th>
                                {{ $totalBarang }}
                            </th>

                            <th colspan="3"></th>
                        </tr>
                    </tfoot>

                </table>

            </div>

        </div>

    </div>
@endsection
