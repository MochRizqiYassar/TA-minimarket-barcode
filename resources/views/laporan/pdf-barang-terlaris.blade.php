<!DOCTYPE html>
<html>
<head>

    <meta charset="utf-8">

    <title>Laporan Barang Terlaris</title>

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

        <h2>Laporan Barang Terlaris</h2>

        <p>
            Bulan {{ $bulan }}
            Tahun {{ $tahun }}
        </p>

    </div>

    <table>

        <thead>

            <tr>
                <th>Ranking</th>
                <th>Barang</th>
                <th>Total Terjual</th>
                <th>Omzet</th>
                <th>Laba</th>
            </tr>

        </thead>

        <tbody>

            @foreach($laporans as $item)

            <tr>

                <td>
                    #{{ $loop->iteration }}
                </td>

                <td>
                    {{ $item->nama_barang }}
                </td>

                <td>
                    {{ $item->total_terjual }}
                </td>

                <td>
                    Rp {{ number_format($item->total_omzet,0,',','.') }}
                </td>

                <td>
                    Rp {{ number_format($item->total_laba,0,',','.') }}
                </td>

            </tr>

            @endforeach

        </tbody>

    </table>

</body>
</html>
