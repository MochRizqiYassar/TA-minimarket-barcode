@csrf

<div class="card shadow-sm border-0">
    <div class="card-body">

        {{-- SUPPLIER --}}
        <div class="row mb-3">

            <div class="col-md-6">
                <label class="form-label fw-bold">
                    Supplier
                </label>

                <select name="id_supplier" class="form-select" required>
                    <option value="">
                        -- pilih supplier --
                    </option>

                    @foreach ($suppliers as $s)
                        <option value="{{ $s->id_supplier }}">
                            {{ $s->nama_supplier }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-3">
                <label class="form-label fw-bold">
                    Tanggal
                </label>

                <input
                    type="date"
                    name="tanggal_kulakan"
                    class="form-control"
                    required
                >
            </div>

        </div>

        {{-- OCR --}}
        <div class="card border-0 bg-light mb-4">
            <div class="card-body">

                <label class="form-label fw-bold">
                    Scan Nota (OCR)
                </label>

                <div class="row align-items-end">

                    <div class="col-md-8">
                        <input
                            type="file"
                            id="ocr-input"
                            class="form-control"
                            accept="image/*"
                            capture="environment"
                        >
                    </div>

                    <div class="col-md-4 d-grid">
                        <button
                            type="button"
                            id="btn-scan"
                            class="btn btn-primary"
                        >
                            📷 Scan Nota
                        </button>
                    </div>

                </div>

                {{-- PREVIEW --}}
                <div class="text-center mt-3">
                    <img
                        id="preview-img"
                        class="img-fluid rounded shadow-sm"
                        style="max-height:250px; display:none;"
                    >
                </div>

                {{-- LOADING --}}
                <div
                    id="ocr-loading"
                    style="display:none;"
                    class="text-center mt-3"
                >
                    <div class="spinner-border text-primary"></div>

                    <p class="mt-2 mb-0">
                        Scanning nota... mohon tunggu
                    </p>
                </div>

            </div>
        </div>

        {{-- DETAIL --}}
        <div class="d-flex justify-content-between align-items-center mb-3">

            <h5 class="mb-0 fw-bold">
                Detail Barang
            </h5>

            <button
                type="button"
                class="btn btn-success btn-sm"
                onclick="addManualRow()"
            >
                + Tambah Barang
            </button>

        </div>

        {{-- HEADER --}}
        <div class="row fw-bold text-center border-bottom pb-2 mb-2 d-none d-md-flex">

            <div class="col-md-3">
                Nama Barang
            </div>

            <div class="col-md-2">
                Tipe
            </div>

            <div class="col-md-2">
                Jumlah
            </div>

            <div class="col-md-2">
                Harga
            </div>

            <div class="col-md-1">
                Aksi
            </div>

        </div>

        {{-- DETAIL CONTAINER --}}
        <div id="detail-container"></div>

        {{-- SUBMIT --}}
        <div class="mt-4 text-end">
            <button type="submit" class="btn btn-primary px-4">
                💾 Simpan
            </button>
        </div>

    </div>
</div>

<script>

let detailIndex = 0;

document.addEventListener('DOMContentLoaded', function () {

    addManualRow();
    addManualRow();
    addManualRow();

    const btn = document.getElementById('btn-scan');
    const loading = document.getElementById('ocr-loading');
    const preview = document.getElementById('preview-img');
    const fileInput = document.getElementById('ocr-input');

    btn.addEventListener('click', async () => {

        if (btn.disabled) return;

        if (!fileInput.files.length) {
            alert('Pilih foto dulu!');
            return;
        }

        const file = fileInput.files[0];

        preview.src = URL.createObjectURL(file);
        preview.style.display = "block";

        const formData = new FormData();
        formData.append('nota_image', file);

        try {

            btn.disabled = true;

            btn.innerHTML = `
                <span class="spinner-border spinner-border-sm"></span>
                Scanning...
            `;

            loading.style.display = "block";

            const res = await fetch("{{ route('kulakan.ocr') }}", {
                method: "POST",
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: formData
            });

            const data = await res.json();

            if (!data.items || !Array.isArray(data.items)) {
                alert("Response tidak valid");
                return;
            }

            if (data.items.length === 0) {
                alert("OCR berhasil, tapi data tidak terbaca");
                return;
            }

            fillDetailTable(data.items);

            if (data.supplier_id) {
                document.querySelector('[name="id_supplier"]').value =
                    data.supplier_id;
            }

            loading.innerHTML = `
                <div class="alert alert-success py-2">
                    ✅ OCR selesai
                </div>
            `;

            setTimeout(() => {

                loading.style.display = "none";

                loading.innerHTML = `
                    <div class="spinner-border text-primary"></div>
                    <p class="mt-2 mb-0">
                        Scanning nota... mohon tunggu
                    </p>
                `;

            }, 1200);

        } catch (err) {

            console.error(err);
            alert('OCR gagal (server error)');

        } finally {

            btn.disabled = false;
            btn.innerHTML = '📷 Scan Nota';

        }

    });

});

function addManualRow(item = null) {

    const container = document.getElementById('detail-container');

    const namaBarang = item?.nama_barang ?? '';
    const banyak = item?.banyak ?? 1;
    const harga = item?.harga_satuan ?? 0;

    const row = `
    <div class="detail-row card border-0 shadow-sm mb-3">

        <div class="card-body">

            <div class="row g-2 align-items-center">

                <div class="col-md-3">
    <select
        name="details[${detailIndex}][id_barang]"
        class="form-select"
        required
    >
        <option value="">
            Pilih Barang
        </option>

        @foreach ($barangs as $barang)
            <option value="{{ $barang->id_barang }}">
                {{ $barang->nama_barang }}
            </option>
        @endforeach

    </select>
</div>

                <div class="col-md-2">
                    <select
                        name="details[${detailIndex}][id_tipe_barang]"
                        class="form-select"
                        required
                    >
                        <option value="">
                            Pilih Tipe
                        </option>

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

                <div class="col-md-2">
                    <input
                        type="number"
                        name="details[${detailIndex}][harga_satuan]"
                        class="form-control"
                        placeholder="Harga"
                        value="${harga}"
                        min="0"
                        required
                    >
                </div>

                <div class="col-md-1 d-grid">
                    <button
                        type="button"
                        class="btn btn-danger"
                        onclick="this.closest('.detail-row').remove()"
                    >
                        ✕
                    </button>
                </div>

            </div>

        </div>

    </div>
    `;

    container.insertAdjacentHTML('beforeend', row);

    detailIndex++;
}

function fillDetailTable(items) {

    const container = document.getElementById('detail-container');

    container.innerHTML = '';

    detailIndex = 0;

    items.forEach((item) => {
        addManualRow(item);
    });

}

</script>
