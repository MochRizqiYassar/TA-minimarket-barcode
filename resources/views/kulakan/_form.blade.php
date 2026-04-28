@csrf

<div class="mb-3">
    <label>Supplier</label>
    <select name="id_supplier" class="form-control" required>
        <option value="">-- pilih supplier --</option>
        @foreach ($suppliers as $s)
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

<div class="mb-3">
    <label>Scan Nota (OCR)</label>
    <input type="file" id="ocr-input" class="form-control" accept="image/*" capture="environment">
</div>

<button type="button" id="btn-scan" class="btn btn-info mb-3">
    Scan Nota
</button>
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
                @foreach ($barangs as $b)
                    <option value="{{ $b->id_barang }}">{{ $b->nama_barang }}</option>
                @endforeach
            </select>
        </div>

        <div class="col">
            <select name="details[0][id_tipe_barang]" class="form-control">
                @foreach ($tipeBarangs as $t)
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
<script>
    document.addEventListener('DOMContentLoaded', function() {

        document.getElementById('btn-scan').addEventListener('click', async () => {
            const fileInput = document.getElementById('ocr-input');

            if (!fileInput.files.length) {
                alert('Pilih foto dulu!');
                return;
            }

            const formData = new FormData();
            formData.append('nota_image', fileInput.files[0]);

            try {
                const res = await fetch("{{ route('kulakan.ocr') }}", {
    method: "POST",
    headers: {
        'X-CSRF-TOKEN': '{{ csrf_token() }}'
    },
    body: formData
});

const text = await res.text();
console.log("RAW:", text);

let data = JSON.parse(text);
console.log("PARSED:", data);

// 🔥 VALIDASI OBJECT
if (!data.items || !Array.isArray(data.items)) {
    alert("Response tidak valid");
    return;
}

if (data.items.length === 0) {
    alert("OCR berhasil, tapi data tidak terbaca");
    return;
}

// 🔥 ISI TABLE
fillDetailTable(data.items);

// 🔥 AUTO SELECT SUPPLIER
if (data.supplier_id) {
    const select = document.querySelector('[name="id_supplier"]');
    select.value = data.supplier_id;
}

            } catch (err) {
                console.error(err);
                alert('OCR gagal (server error)');
            }
        });

    });
</script>
<script>
    function fillDetailTable(items) {
        const container = document.getElementById('detail-container');
        container.innerHTML = '';

        items.forEach((item, index) => {
            const row = `
        <div class="row mb-2">
            <div class="col">
                <input type="hidden" name="details[${index}][id_barang]" value="${item.id_barang}">
                <input type="text" class="form-control" value="${item.nama_barang}" readonly>
            </div>

            <div class="col">
                <input type="hidden" name="details[${index}][id_tipe_barang]" value="${item.id_tipe_barang}">
                <input type="text" class="form-control" value="Tipe ${item.id_tipe_barang}" readonly>
            </div>

            <div class="col">
                <input type="number" name="details[${index}][banyak]" class="form-control" value="${item.banyak}">
            </div>

            <div class="col">
                <input type="number" name="details[${index}][harga_satuan]" class="form-control" value="${item.harga_satuan}">
            </div>
        </div>
        `;

            container.insertAdjacentHTML('beforeend', row);
        });
    }
</script>
<button type="submit" class="btn btn-success">Simpan</button>
