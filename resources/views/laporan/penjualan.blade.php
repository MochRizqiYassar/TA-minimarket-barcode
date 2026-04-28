@extends('layouts.admin')

@section('content')
<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4>Laporan Penjualan</h4>

        <a href="{{ route('laporan.penjualan.pdf', [
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

                    <input type="date"
                           name="tanggal_awal"
                           value="{{ request('tanggal_awal') }}"
                           class="form-control">
                </div>

                <div class="col-md-4">
                    <label>Tanggal Akhir</label>

                    <input type="date"
                           name="tanggal_akhir"
                           value="{{ request('tanggal_akhir') }}"
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
                        <th>Kasir</th>
                        <th>Detail Barang</th>
                        <th>Total Harga</th>
                        <th>Total Laba</th>
                    </tr>
                </thead>

                <tbody>

                    @forelse($laporans as $item)

                    <tr>

                        <td>{{ $loop->iteration }}</td>

                        <td>
                            {{ \Carbon\Carbon::parse($item->tanggal_penjualan)->format('d-m-Y') }}
                        </td>

                        <td>
                            {{ $item->user->name ?? '-' }}
                        </td>

                        <td>

                            <ul class="mb-0">

                                @foreach($item->detailPenjualans as $detail)

                                    <li>
                                        {{ $detail->nama_barang }}
                                        -
                                        {{ $detail->jumlah }} x
                                        Rp {{ number_format($detail->harga,0,',','.') }}
                                    </li>

                                @endforeach

                            </ul>

                        </td>

                        <td>
                            Rp {{ number_format($item->total_harga,0,',','.') }}
                        </td>

                        <td>
                            Rp {{
                                number_format(
                                    $item->detailPenjualans->sum('total_laba'),
                                    0,
                                    ',',
                                    '.'
                                )
                            }}
                        </td>

                    </tr>

                    @empty

                    <tr>
                        <td colspan="6" class="text-center">
                            Tidak ada data
                        </td>
                    </tr>

                    @endforelse

                </tbody>

                <tfoot>

                    <tr>

                        <th colspan="4" class="text-end">
                            Total Omzet
                        </th>

                        <th>
                            Rp {{ number_format($totalOmzet,0,',','.') }}
                        </th>

                        <th></th>

                    </tr>

                    <tr>

                        <th colspan="5" class="text-end">
                            Total Laba
                        </th>

                        <th>
                            Rp {{ number_format($totalLaba,0,',','.') }}
                        </th>

                    </tr>

                </tfoot>

            </table>

        </div>

    </div>

</div>
@endsection
