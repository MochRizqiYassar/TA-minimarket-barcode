@extends('layouts.kasir')

@section('content')
<div class="container">
    <h4>Edit Penjualan</h4>

    <form action="{{ route('penjualan.update', $penjualan->id_penjualan) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label>Tanggal</label>
            <input type="date" name="tanggal_penjualan"
                   value="{{ $penjualan->tanggal_penjualan->format('Y-m-d') }}"
                   class="form-control" required>
        </div>

        <table class="table">
            <thead>
                <tr>
                    <th>Barang</th>
                    <th>Jumlah</th>
                    <th></th>
                </tr>
            </thead>
            <tbody id="cart-body">
                @foreach($penjualan->detailPenjualans as $d)
                <tr>
                    <td>
                        <select name="details[{{ $loop->index }}][id_barang]" class="form-control">
                            @foreach($barangs as $b)
                                <option value="{{ $b->id_barang }}"
                                    {{ $b->id_barang == $d->id_barang ? 'selected' : '' }}>
                                    {{ $b->nama_barang }}
                                </option>
                            @endforeach
                        </select>
                    </td>
                    <td>
                        <input type="number"
                               name="details[{{ $loop->index }}][jumlah]"
                               value="{{ $d->jumlah }}"
                               class="form-control">
                    </td>
                    <td>
                        <button type="button" onclick="this.closest('tr').remove()">x</button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <button type="button" onclick="addRow()" class="btn btn-secondary mb-3">
            + Tambah Barang
        </button>

        <br>
        <button class="btn btn-success">Update</button>
        <a href="{{ route('penjualan.index') }}" class="btn btn-secondary">Kembali</a>
    </form>
</div>

<script>
let index = {{ count($penjualan->detailPenjualans) }};

function addRow() {
    let html = `
    <tr>
        <td>
            <select name="details[${index}][id_barang]" class="form-control">
                @foreach($barangs as $b)
                    <option value="{{ $b->id_barang }}">{{ $b->nama_barang }}</option>
                @endforeach
            </select>
        </td>
        <td>
            <input type="number" name="details[${index}][jumlah]" class="form-control">
        </td>
        <td>
            <button type="button" onclick="this.closest('tr').remove()">x</button>
        </td>
    </tr>
    `;

    document.getElementById('cart-body').insertAdjacentHTML('beforeend', html);
    index++;
}
</script>
@endsection
