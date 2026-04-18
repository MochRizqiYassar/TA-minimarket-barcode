<!DOCTYPE html>
<html>

<head>
    <style>
        body {
            font-family: Arial;
        }

        table {
            width: 100%;
        }

        td {
            width: 25%;
            text-align: center;
            padding: 10px;
        }
    </style>
</head>

<body>

    <table>
        <tr>
            @for ($i = 0; $i < $jumlah; $i++)
                <td>
                    <div>
                        {!! $barcode !!}
                    </div>
                    <div style="font-size:12px;">
                        {{ $kode }}
                    </div>
                </td>

                @if (($i + 1) % 4 == 0)
        </tr>
        <tr>
            @endif
            @endfor
        </tr>
    </table>

</body>

</html>
