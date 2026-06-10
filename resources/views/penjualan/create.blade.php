@extends('layouts.kasir')

@section('content')
    <div class="container-fluid">
        <h4>Penjualan</h4>

        <form id="form-penjualan">
            @csrf

            <input type="hidden" name="tanggal_penjualan" value="{{ date('Y-m-d') }}">

            <div class="row">
                <!-- LEFT: KERANJANG -->
                <div class="col-md-7">
                    <div class="card p-3">

                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Produk</th>
                                    <th>Harga</th>
                                    <th>Qty</th>
                                    <th>Subtotal</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody id="cart-body"></tbody>
                        </table>

                        <hr>

                        <h5 id="grand-total">Grand Total: Rp. 0</h5>

                        <input type="hidden" name="details_json" id="details-json">

                        <div class="mt-3">
                            <button type="button" id="reset" class="btn btn-danger">Reset</button>
                            <button class="btn btn-primary">Selesai</button>
                        </div>
                    </div>
                </div>

                <!-- RIGHT: PRODUK -->
                <div class="col-md-5">
                    <div class="card p-3">

                        <input type="text" id="search" class="form-control mb-3" placeholder="Cari produk...">

                        <div class="row product-list-scroll" id="product-list">
                            @foreach ($barangs as $b)
                                <div class="col-md-6 mb-3 product-item" data-id="{{ $b->id_barang }}"
                                    data-nama="{{ $b->nama_barang }}" data-harga="{{ $b->harga_jual }}"
                                    data-stok="{{ $b->stok }}" data-barcode="{{ $b->barcode }}">

                                    <div class="card p-2 text-center product-card h-100"
                                        style="cursor:pointer;
     @if ($b->stok == 0) opacity:0.5; pointer-events:none; @endif">
                                        <img src="{{ $b->foto ? asset('storage/' . $b->foto) : asset('assets/images/no-image.png') }}"
                                            class="img-fluid rounded mb-2"
                                            style="
        height:120px;
        width:100%;
        object-fit:cover;
    ">

                                        <small>{{ $b->nama_barang }}</small>

                                        <div class="badge bg-success mb-1">
                                            Rp {{ number_format($b->harga_jual) }}
                                        </div>

                                        <div class="badge bg-info">
                                            Stok: {{ $b->stok }}
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                    </div>
                </div>
            </div>
        </form>
    </div>

    <script>
        let cart = JSON.parse(localStorage.getItem('draft_cart')) || [];

        function saveCart() {

            localStorage.setItem(
                'draft_cart',
                JSON.stringify(cart)
            );
        }

        function renderCart() {
            let body = document.getElementById('cart-body');
            body.innerHTML = '';

            let total = 0;

            cart.forEach((item, index) => {
                let subtotal = item.harga * item.qty;
                total += subtotal;

                body.innerHTML += `
        <tr>
            <td>${item.nama}</td>
            <td>Rp ${item.harga.toLocaleString()}</td>
            <td>
                <button onclick="changeQty(${index}, -1)">-</button>
                ${item.qty}
                <button onclick="changeQty(${index}, 1)">+</button>
            </td>
            <td>Rp ${subtotal.toLocaleString()}</td>
            <td><button onclick="removeItem(${index})">x</button></td>
        </tr>`;
            });

            document.getElementById('grand-total').innerText =
                "Grand Total: Rp. " + total.toLocaleString();

            let details = cart.map(item => ({
                id_barang: item.id,
                jumlah: item.qty
            }));

            document.getElementById('details-json').value = JSON.stringify(details);
            saveCart();
        }

        function addToCart(id, nama, harga, stok) {
            if (stok <= 0) {
                showPopup('danger', 'Stok habis!');
                return;
            }

            let item = cart.find(i => i.id == id);

            if (item) {
                if (item.qty >= stok) {
                    showPopup('danger', 'Stok tidak mencukupi!');
                    return;
                }
                item.qty++;
            } else {
                cart.push({
                    id,
                    nama,
                    qty: 1,
                    stok,
                    harga
                });
            }

            renderCart();
        }

        function changeQty(index, val) {
            let item = cart[index];
            let newQty = item.qty + val;

            if (newQty > item.stok) {
                showPopup('danger', 'Stok tidak mencukupi!');
                return;
            }

            if (newQty <= 0) {
                cart.splice(index, 1);
            } else {
                item.qty = newQty;
            }

            renderCart();
        }

        function removeItem(index) {
            cart.splice(index, 1);
            renderCart();
        }

        // klik produk
        document.querySelectorAll('.product-item').forEach(el => {
            el.addEventListener('click', function() {
                addToCart(
                    this.dataset.id,
                    this.dataset.nama,
                    parseInt(this.dataset.harga),
                    parseInt(this.dataset.stok)
                );
            });
        });

        // reset
        document.getElementById('reset')
            .addEventListener('click', () => {

                cart = [];

                localStorage.removeItem('draft_cart');

                renderCart();
            });

        // search
        document.getElementById('search').addEventListener('keyup', function() {
            let val = this.value.toLowerCase();

            document.querySelectorAll('.product-item').forEach(el => {
                let nama = el.dataset.nama.toLowerCase();
                el.style.display = nama.includes(val) ? '' : 'none';
            });
        });
        let scanBuffer = '';
        let scanTimeout;

        document.addEventListener('keydown', function(e) {
            if (scanTimeout) clearTimeout(scanTimeout);

            // ENTER = selesai scan
            if (e.key === "Enter") {
                handleScan(scanBuffer.trim());
                scanBuffer = '';
                return;
            }

            scanBuffer += e.key;

            // reset kalau bukan scanner
            scanTimeout = setTimeout(() => {
                scanBuffer = '';
            }, 300);
        });

        function handleScan(barcode) {
            let items = [];

            document.querySelectorAll('.product-item').forEach(el => {
                if (el.dataset.barcode == barcode) {
                    items.push(el);
                }
            });

            if (items.length === 1) {
                let el = items[0];
                addToCart(
                    el.dataset.id,
                    el.dataset.nama,
                    parseInt(el.dataset.harga),
                    parseInt(el.dataset.stok)
                );

            } else if (items.length > 1) {
                let pilihan = items.map((el, i) => `${i+1}. ${el.dataset.nama}`).join('\n');
                let pilih = prompt("Pilih barang:\n" + pilihan);

                let index = parseInt(pilih) - 1;

                if (items[index]) {
                    let el = items[index];
                    addToCart(
                        el.dataset.id,
                        el.dataset.nama,
                        parseInt(el.dataset.harga),
                        parseInt(el.dataset.stok)
                    );
                }

            } else {
                showPopup('danger', 'Barcode tidak ditemukan!');
            }
        }
        document.getElementById('form-penjualan')
            .addEventListener('submit', async function(e) {

                e.preventDefault();

                if (cart.length === 0) {
                    showPopup('danger', 'Keranjang masih kosong!');
                    return;
                }

                const data = {

                    tanggal_penjualan: new Date().toISOString().split('T')[0],

                    details: cart.map(item => ({
                        id_barang: item.id,
                        jumlah: item.qty
                    }))
                };

                try {

                    console.log('COBA KIRIM ONLINE');

                    const response = await fetch('/penjualan', {

                        method: 'POST',

                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json'
                        },

                        body: JSON.stringify({
                            tanggal_penjualan: data.tanggal_penjualan,
                            details_json: JSON.stringify(data.details)
                        })
                    });

                    // kalau sukses server
                    if (response.ok) {

                        const result = await response.json();

                        if (result.success) {

                            showPopup('success', null, () => {
                                cart = [];
                                localStorage.removeItem('draft_cart');
                                renderCart();

                                window.location.href = "/penjualan";
                            });

                            return;
                        }
                    }

                    throw new Error('SERVER ERROR');

                } catch (error) {

                    console.log('MASUK MODE OFFLINE');

                    // ===== SAVE OFFLINE =====

                    let offlinePenjualans =
                        JSON.parse(localStorage.getItem('offline_penjualans')) || [];

                    let total = 0;

                    cart.forEach(item => {
                        total += item.harga * item.qty;
                    });

                    const transaksiBaru = {

                        offline_id: Date.now(),

                        tanggal_penjualan: data.tanggal_penjualan,

                        details: data.details,

                        total_harga: total,

                        offline: true,

                        synced: false,
                    };

                    offlinePenjualans.push(transaksiBaru);

                    localStorage.setItem(
                        'offline_penjualans',
                        JSON.stringify(offlinePenjualans)
                    );

                    console.log(
                        'HASIL LOCAL:',
                        localStorage.getItem('offline_penjualans')
                    );

                    showPopup('offline', null, () => {
                        cart = [];
                        localStorage.removeItem('draft_cart');
                        renderCart();

                        window.location.href = "/penjualan";
                    });
                }
            });
        renderCart();
        async function saveOfflinePenjualan(data) {

            try {

                let offlinePenjualans =
                    JSON.parse(localStorage.getItem('offline_penjualans')) || [];

                // hitung total
                let total = 0;

                cart.forEach(item => {
                    total += item.harga * item.qty;
                });

                const transaksiBaru = {
                    offline_id: Date.now(),
                    tanggal_penjualan: data.tanggal_penjualan,
                    details: data.details,
                    total_harga: total,
                    offline: true,
                    synced: false,
                };

                offlinePenjualans.push(transaksiBaru);

                localStorage.setItem(
                    'offline_penjualans',
                    JSON.stringify(offlinePenjualans)
                );

                console.log(
                    'HASIL SAVE:',
                    localStorage.getItem('offline_penjualans')
                );

                // reset cart
                cart = [];

                localStorage.removeItem('draft_cart');

                renderCart();

                showPopup('offline', null, () => {
                    window.location.href = "/penjualan";
                });

            } catch (e) {

                console.error('GAGAL SAVE OFFLINE:', e);
            }
        }
        window.addEventListener('online', syncOfflinePenjualan);

        async function syncOfflinePenjualan() {

            let offlinePenjualans =
                JSON.parse(localStorage.getItem('offline_penjualans')) || [];

            if (offlinePenjualans.length === 0) return;

            for (const trx of offlinePenjualans) {

                try {

                    const response = await fetch('/penjualan', {

                        method: 'POST',

                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json'
                        },

                        body: JSON.stringify({
                            tanggal_penjualan: trx.tanggal_penjualan,
                            details_json: JSON.stringify(trx.details)
                        })
                    });

                    if (response.ok) {

                        trx.synced = true;
                    }

                } catch (e) {

                    console.error(e);
                }
            }

            offlinePenjualans =
                offlinePenjualans.filter(t => !t.synced);

            localStorage.setItem(
                'offline_penjualans',
                JSON.stringify(offlinePenjualans)
            );
        }
        const popupConfigs = {
            success: {
                icon: '✓',
                iconBg: '#eaf3de',
                title: 'Penjualan Berhasil!',
                msg: 'Penjualan telah berhasil dicatat ke server.',
                badges: [{
                    label: 'Online',
                    color: '#3b6d11',
                    bg: '#eaf3de'
                }, {
                    label: 'Tersimpan',
                    color: '#3b6d11',
                    bg: '#eaf3de'
                }],
                btnBg: '#3b6d11',
                btnText: 'Oke, Lanjut'
            },
            offline: {
                icon: '⚡',
                iconBg: '#faeeda',
                title: 'Disimpan Offline',
                msg: 'Tidak ada koneksi. Penjualan tersimpan di perangkat dan akan otomatis tersinkron saat online.',
                badges: [{
                    label: 'Offline Mode',
                    color: '#854f0b',
                    bg: '#faeeda'
                }, {
                    label: 'Akan Disinkron',
                    color: '#854f0b',
                    bg: '#faeeda'
                }],
                btnBg: '#ba7517',
                btnText: 'Oke, Mengerti'
            },
            danger: {
                icon: '✕',
                iconBg: '#fcebeb',
                title: 'Penjualan Gagal',
                msg: 'Terjadi kesalahan. Coba ulangi beberapa saat lagi.',
                badges: [{
                    label: 'Gagal',
                    color: '#a32d2d',
                    bg: '#fcebeb'
                }],
                btnBg: '#a32d2d',
                btnText: 'Tutup'
            }
        };

        function showPopup(type, customMsg, callback) {
            const c = popupConfigs[type];
            const overlay = document.getElementById('popup-overlay');
            document.getElementById('popup-icon').style.background = c.iconBg;
            document.getElementById('popup-icon').textContent = c.icon;
            document.getElementById('popup-title').textContent = c.title;
            document.getElementById('popup-msg').textContent = customMsg || c.msg;
            document.getElementById('popup-btn').style.background = c.btnBg;
            document.getElementById('popup-btn').textContent = c.btnText;
            document.getElementById('popup-badges').innerHTML = c.badges
                .map(b =>
                    `<span style="font-size:11px;font-weight:600;padding:4px 10px;border-radius:20px;background:${b.bg};color:${b.color};">${b.label}</span>`
                ).join('');

            overlay.style.opacity = '1';
            overlay.style.pointerEvents = 'auto';
            document.getElementById('popup-box').style.transform = 'scale(1)';

            const close = () => {
                overlay.style.opacity = '0';
                overlay.style.pointerEvents = 'none';
                document.getElementById('popup-box').style.transform = 'scale(.85)';
                if (callback) callback();
            };
            document.getElementById('popup-close').onclick = close;
            document.getElementById('popup-btn').onclick = close;
            overlay.onclick = e => {
                if (e.target === overlay) close();
            };
        }
    </script>
    <!-- POPUP COMPONENT -->
    <div id="popup-overlay" role="dialog" aria-modal="true"
        style="position:fixed;inset:0;background:rgba(0,0,0,0.45);display:flex;align-items:center;justify-content:center;z-index:9999;opacity:0;transition:opacity .25s;pointer-events:none;">
        <div id="popup-box"
            style="background:#fff;border-radius:20px;padding:2rem 2rem 1.5rem;max-width:360px;width:90%;text-align:center;position:relative;transform:scale(.85);transition:transform .25s;">
            <button id="popup-close"
                style="position:absolute;top:14px;right:16px;background:none;border:none;font-size:20px;cursor:pointer;color:#888;">✕</button>
            <div id="popup-icon"
                style="width:90px;height:90px;border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 1rem;font-size:42px;background:#eaf3de;">
                ✓</div>
            <p id="popup-title" style="font-size:18px;font-weight:600;margin:0 0 .4rem;color:#1a1a1a;"></p>
            <p id="popup-msg" style="font-size:14px;color:#666;margin:0 0 1.4rem;line-height:1.5;"></p>
            <div id="popup-badges" style="display:flex;gap:8px;justify-content:center;flex-wrap:wrap;margin-bottom:1rem;">
            </div>
            <button id="popup-btn"
                style="padding:.6rem 2rem;border-radius:10px;font-size:14px;font-weight:600;border:none;cursor:pointer;color:#fff;background:#3b6d11;">Oke,
                Lanjut</button>
        </div>
    </div>
@endsection
