@csrf

<div class="mb-3">
    <label>Supplier</label>
    <select name="id_supplier" class="form-control" required>
        <option value="">-- pilih supplier --</option>
        @foreach ($suppliers as $s)
            <option value="{{ $s->id_supplier }}">
                {{ $s->nama_supplier }}
            </option>
        @endforeach
    </select>
</div>

<div class="mb-3">
    <label>Tanggal</label>
    <input type="date" name="tanggal_kulakan" class="form-control" required>
</div>

<div class="mb-3">
    <label>Scan Nota (OCR)</label>
    <input type="file" id="ocr-input" class="form-control" accept="image/*" capture="environment">
</div>

<!-- 🔥 PREVIEW GAMBAR -->
<div class="mb-3 text-center">
    <img id="preview-img" style="max-width:200px; display:none;" class="mb-2"/>
</div>

<button type="button" id="btn-scan" class="btn btn-info mb-3">
    📷 Scan Nota
</button>

<!-- 🔥 LOADING -->
<div id="ocr-loading" style="display:none;" class="text-center my-3">
    <div class="spinner-border text-primary"></div>
    <p class="mt-2">Scanning nota... mohon tunggu</p>
</div>

<hr>

<h5 class="mb-3">Detail Barang</h5>

<div class="row fw-bold mb-2 text-center">

    <div class="col-md-4">
        Nama Barang
    </div>

    <div class="col-md-2">
        Tipe
    </div>

    <div class="col-md-2">
        Jumlah
    </div>

    <div class="col-md-3">
        Harga Satuan
    </div>

    <div class="col-md-1">
        Aksi
    </div>

</div>

<div id="detail-container"></div>

<button type="submit" class="btn btn-success mt-3">
    Simpan
</button>

<script>

// 🔥 COUNTER INDEX GLOBAL
let detailIndex = 0;

document.addEventListener('DOMContentLoaded', function () {

    // 🔥 FIELD AWAL
    addManualRow();
    addManualRow();
    addManualRow();

    const btn = document.getElementById('btn-scan');
    const loading = document.getElementById('ocr-loading');
    const preview = document.getElementById('preview-img');
    const fileInput = document.getElementById('ocr-input');

    btn.addEventListener('click', async () => {

        // 🔥 ANTI DOUBLE CLICK
        if (btn.disabled) return;

        if (!fileInput.files.length) {
            alert('Pilih foto dulu!');
            return;
        }

        const file = fileInput.files[0];

        // 🔥 PREVIEW GAMBAR
        preview.src = URL.createObjectURL(file);
        preview.style.display = "block";

        const formData = new FormData();
        formData.append('nota_image', file);

        try {

            // 🔥 START LOADING
            btn.disabled = true;
            btn.innerHTML = `<span class="spinner-border spinner-border-sm"></span> Scanning...`;

            loading.style.display = "block";

            const res = await fetch("{{ route('kulakan.ocr') }}", {
                method: "POST",
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: formData
            });

            const data = await res.json();

            console.log("OCR RESULT:", data);

            if (!data.items || !Array.isArray(data.items)) {
                alert("Response tidak valid");
                return;
            }

            if (data.items.length === 0) {
                alert("OCR berhasil, tapi data tidak terbaca");
                return;
            }

            // 🔥 MASUKKAN HASIL OCR KE FORM
            fillDetailTable(data.items);

            // 🔥 AUTO SELECT SUPPLIER
            if (data.supplier_id) {
                const select = document.querySelector('[name="id_supplier"]');
                select.value = data.supplier_id;
            }

            // 🔥 FEEDBACK SUKSES
            loading.innerHTML = "✅ OCR selesai!";

            setTimeout(() => {
                loading.style.display = "none";

                loading.innerHTML = `
                    <div class="spinner-border text-primary"></div>
                    <p class="mt-2">Scanning nota... mohon tunggu</p>
                `;
            }, 1000);

        } catch (err) {

            console.error(err);
            alert('OCR gagal (server error)');

        } finally {

            // 🔥 STOP LOADING
            btn.disabled = false;
            btn.innerHTML = "📷 Scan Nota";
        }
    });

});

// 🔥 TAMBAH BARIS MANUAL
function addManualRow(item = null) {

    const container = document.getElementById('detail-container');

    const namaBarang = item?.nama_barang ?? '';
    const tipeBarang = item?.id_tipe_barang ?? '';
    const banyak = item?.banyak ?? 1;
    const harga = item?.harga_satuan ?? 0;

    const row = `
    <div class="row mb-2 detail-row border rounded p-2">

        <div class="col-md-4">
            <input
                type="text"
                name="details[${detailIndex}][nama_barang]"
                class="form-control"
                placeholder="Nama Barang"
                value="${namaBarang}"
                required
            >
        </div>

        <div class="col-md-2">
            <select
                name="details[${detailIndex}][id_tipe_barang]"
                class="form-control"
                required
            >
                <option value="">Tipe</option>

                @foreach($tipeBarangs as $t)
                    <option value="{{ $t->id_tipe_barang }}">
                        {{ $t->nama_tipe }}
                    </option>
                @endforeach

            </select>
        </div>

        <div class="col-md-2">
            <input
                type="number"
                name="details[${detailIndex}][banyak]"
                class="form-control"
                placeholder="Jumlah"
                value="${banyak}"
                min="1"
                required
            >
        </div>

        <div class="col-md-3">
            <input
                type="number"
                name="details[${detailIndex}][harga_satuan]"
                class="form-control"
                placeholder="Harga Satuan"
                value="${harga}"
                min="0"
                required
            >
        </div>

        <div class="col-md-1">
            <button
                type="button"
                class="btn btn-danger w-100"
                onclick="this.closest('.detail-row').remove()"
            >
                X
            </button>
        </div>

    </div>
    `;

    container.insertAdjacentHTML('beforeend', row);

    detailIndex++;
}

// 🔥 OCR AUTO MASUK KE INPUT MANUAL
function fillDetailTable(items) {

    const container = document.getElementById('detail-container');

    // 🔥 HAPUS FIELD LAMA
    container.innerHTML = '';

    // 🔥 RESET INDEX
    detailIndex = 0;

    // 🔥 ISI DARI OCR
    items.forEach((item) => {
        addManualRow(item);
    });

}

</script>
