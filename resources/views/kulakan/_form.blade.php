
    @csrf

<div class="mb-3">
    <label>Supplier</label>
    <select name="id_supplier" class="form-control" required>
        <option value="">-- pilih supplier --</option>
        @foreach($suppliers as $s)
            <option value="{{ $s->id_supplier }}"
                {{ old('id_supplier', $kulakan->id_supplier ?? '') == $s->id_supplier ? 'selected' : '' }}>
                {{ $s->nama_supplier }}
            </option>
        @endforeach
    </select>
</div>

<div class="mb-3">
    <label>Tanggal</label>
    <input type="date" name="tanggal_kulakan" class="form-control"
        value="{{ old('tanggal_kulakan', $kulakan->tanggal_kulakan ?? '') }}" required>
</div>

<hr>
<h5>Detail Barang</h5>

<div id="detail-container">
    <div class="row mb-2">
        <div class="col">
            @if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
            <select name="details[0][id_barang]" class="form-control" required>
    <option value="">-- pilih barang --</option>
    @foreach($barangs as $b)
        <option value="{{ $b->id_barang }}">{{ $b->nama_barang }}</option>
    @endforeach
</select>
        </div>

        <div class="col">
            <select name="details[0][id_tipe_barang]" class="form-control">
                @foreach($tipeBarangs as $t)
                    <option value="{{ $t->id_tipe_barang }}">{{ $t->nama_tipe }}</option>
                @endforeach
            </select>
        </div>

        <div class="col">
            <input type="number" name="details[0][banyak]" placeholder="Qty" class="form-control">
        </div>

        <div class="col">
            <input type="number" name="details[0][harga_satuan]" placeholder="Harga" class="form-control">
        </div>
    </div>
</div>

<button type="submit" class="btn btn-success">Simpan</button>

