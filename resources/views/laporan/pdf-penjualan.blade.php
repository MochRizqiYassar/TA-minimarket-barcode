<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">

    <title>Laporan Penjualan</title>

    <style>

        body{
            font-family:sans-serif;
            font-size:12px;
        }

        table{
            width:100%;
            border-collapse:collapse;
            margin-top:20px;
        }

        table, th, td{
            border:1px solid black;
        }

        th, td{
            padding:8px;
            vertical-align:top;
        }

        th{
            background:#f2f2f2;
        }

        .title{
            text-align:center;
            margin-bottom:20px;
        }

    </style>
</head>
<body>

    <div class="title">

        <h2>Laporan Penjualan</h2>

        @if(request('tanggal_awal') && request('tanggal_akhir'))

            <p>
                Periode:
                {{ request('tanggal_awal') }}
                s/d
                {{ request('tanggal_akhir') }}
            </p>

        @endif

    </div>

    <table>

        <thead>

            <tr>
                <th>No</th>
                <th>Tanggal</th>
                <th>Kasir</th>
                <th>Detail Barang</th>
                <th>Total</th>
                <th>Laba</th>
            </tr>

        </thead>

        <tbody>

            @foreach($laporans as $item)

            <tr>

                <td>{{ $loop->iteration }}</td>

                <td>
                    {{ \Carbon\Carbon::parse($item->tanggal_penjualan)->format('d-m-Y') }}
                </td>

                <td>
                    {{ $item->user->name ?? '-' }}
                </td>

                <td>

                    <ul>

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

            @endforeach

        </tbody>

        <tfoot>

            <tr>

                <th colspan="4">
                    Total Omzet
                </th>

                <th>
                    Rp {{ number_format($totalOmzet,0,',','.') }}
                </th>

                <th></th>

            </tr>

            <tr>

                <th colspan="5">
                    Total Laba
                </th>

                <th>
                    Rp {{ number_format($totalLaba,0,',','.') }}
                </th>

            </tr>

        </tfoot>

    </table>

</body>
</html>
