<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Barang Masuk</title>

    <style>

        body{
            font-family: sans-serif;
            font-size: 12px;
        }

        table{
            width:100%;
            border-collapse: collapse;
            margin-top:20px;
        }

        table, th, td{
            border:1px solid black;
        }

        th, td{
            padding:8px;
            text-align:left;
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
        <h2>Laporan Barang Masuk</h2>

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
                <th>Barang</th>
                <th>Supplier</th>
                <th>Jumlah</th>
                <th>Expired</th>
                <th>Harga</th>
                <th>Total</th>
            </tr>
        </thead>

        <tbody>

            @foreach($laporans as $item)

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
                    {{ $item->tanggal_expired
                        ? \Carbon\Carbon::parse($item->tanggal_expired)->format('d-m-Y')
                        : '-'
                    }}
                </td>

                <td>
                    Rp {{ number_format($item->harga_beli, 0, ',', '.') }}
                </td>

                <td>
                    Rp {{ number_format($item->jumlah * $item->harga_beli, 0, ',', '.') }}
                </td>

            </tr>

            @endforeach

        </tbody>

        <tfoot>

            <tr>
                <th colspan="4">
                    Total Barang
                </th>

                <th>
                    {{ $totalBarang }}
                </th>

                <th colspan="3"></th>
            </tr>

            <tr>
                <th colspan="7">
                    Total Nominal
                </th>

                <th>
                    Rp {{ number_format($totalNominal, 0, ',', '.') }}
                </th>
            </tr>

        </tfoot>

    </table>

</body>
</html>
