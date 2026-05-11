@extends('layouts.admin')

@section('content')
    <div class="container-fluid">
        <h4>Ambil Barang ke Etalase</h4>

        <form action="{{ route('barang-masuk.store') }}" method="POST">
            @csrf

            <div class="row">
                <!-- LEFT: KERANJANG -->
                <div class="col-md-8">
                    <div class="card p-3">

                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Barang</th>
                                    <th>Stok Kulakan</th>
                                    <th>Qty</th>
                                    <th>Expired</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody id="cart-body"></tbody>
                        </table>
                        <hr>
                        <div class="mt-2 text-end">
                            <h5 id="grand-total">Total Item: 0</h5>
                        </div>

                        <input type="hidden" name="details_json" id="details-json">

                        <div class="mt-3">
                            <button type="button" id="reset" class="btn btn-danger">Reset</button>
                            <button class="btn btn-success">Simpan</button>
                        </div>
                    </div>
                </div>
                        <!-- RIGHT: LIST BARANG -->
                        <div class="col-md-4">
                            <div class="card p-3">

                                <input type="text" id="search" class="form-control mb-3" placeholder="Cari barang...">

                                <div class="product-list-scroll" id="product-list">
                                    @foreach ($details as $item)
                                        <div class="product-item mb-2"
    data-id="{{ $item['id_barang'] }}"
    data-nama="{{ $item['nama_barang'] }}"
    data-stok="{{ $item['stok'] }}"
    data-barcode="{{ $item['barcode'] }}">

    <div class="card p-2 product-card" style="cursor:pointer;">

        <div class="d-flex align-items-center gap-2">

            <img
                src="{{ $item['foto']
                    ? asset('storage/' . $item['foto'])
                    : asset('assets/images/no-image.png') }}"
                style="
                    width:70px;
                    height:70px;
                    object-fit:cover;
                    border-radius:10px;
                ">

            <div class="text-start flex-grow-1">

                <div class="fw-bold small">
                    {{ $item['nama_barang'] }}
                </div>

                <div class="badge bg-info mt-1">
                    Stok: {{ $item['stok'] }}
                </div>

            </div>

        </div>

    </div>
</div>
                                    @endforeach
                                </div>

                            </div>
                        </div>
                    </div>

                </div>
            </div>
    </div>
    </form>
    </div>

    <script>
        let cart = [];

        function renderCart() {
            let body = document.getElementById('cart-body');
            body.innerHTML = '';

            cart.forEach((item, index) => {
                body.innerHTML += `
        <tr>
            <td>${item.nama}</td>
            <td>${item.stok}</td>
            <td>
                <button onclick="changeQty(${index}, -1)">-</button>
                ${item.qty}
                <button onclick="changeQty(${index}, 1)">+</button>
            </td>
            <td>
                <input type="date"
                       value="${item.tanggal_expired ?? ''}"
                       onchange="updateExpired(${index}, this.value)">
            </td>
            <td>
                <button onclick="removeItem(${index})">x</button>
            </td>
        </tr>`;
            });

            // ✅ update JSON
            document.getElementById('details-json').value = JSON.stringify(cart);

            // ✅ HITUNG TOTAL ITEM
            let total = 0;
            cart.forEach(item => total += item.qty);

            document.getElementById('grand-total').innerText =
                "Total Item: " + total;
        }

        function addToCart(id, nama, stok) {
            let item = cart.find(i => i.id == id);

            if (item) {
                if (item.qty < item.stok) {
                    item.qty++;
                } else {
                    alert('Qty melebihi stok kulakan!');
                }
            } else {
                cart.push({
                    id,
                    nama,
                    qty: 1,
                    stok,
                    harga: null,
                    tanggal_expired: null
                });
            }

            renderCart();
        }

        function changeQty(index, val) {
            let item = cart[index];
            let newQty = item.qty + val;

            if (newQty > item.stok) {
                alert('Qty melebihi stok kulakan!');
                newQty = item.stok;
            }

            if (newQty <= 0) {
                cart.splice(index, 1);
            } else {
                item.qty = newQty;
            }

            renderCart();
        }

        function updateExpired(index, val) {
            cart[index].tanggal_expired = val;

            // 🔥 UPDATE JSON juga!
            document.getElementById('details-json').value = JSON.stringify(cart);

            console.log(cart);
        }

        function removeItem(index) {
            cart.splice(index, 1);
            renderCart();
        }

        // klik barang
        document.querySelectorAll('.product-item').forEach(el => {
            el.addEventListener('click', function() {
                addToCart(
                    this.dataset.id,
                    this.dataset.nama,
                    parseInt(this.dataset.stok),

                );
            });
        });

        document.querySelector('form').addEventListener('submit', function(e) {
            for (let item of cart) {
                if (!item.tanggal_expired) {
                    alert('Tanggal expired harus diisi!');
                    e.preventDefault();
                    return;
                }
            }
        });

        // reset
        document.getElementById('reset').addEventListener('click', () => {
            cart = [];
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

            // ENTER = scan selesai
            if (e.key === "Enter") {
                handleScan(scanBuffer.trim());
                scanBuffer = '';
                return;
            }

            // Tambah karakter
            scanBuffer += e.key;

            // Reset kalau lama (bukan scanner)
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
                addToCart(el.dataset.id, el.dataset.nama, parseInt(el.dataset.stok));
            } else if (items.length > 1) {
                // 🔥 tampilkan pilihan
                let pilihan = items.map((el, i) => `${i+1}. ${el.dataset.nama}`).join('\n');
                let pilih = prompt("Pilih barang:\n" + pilihan);

                let index = parseInt(pilih) - 1;

                if (items[index]) {
                    let el = items[index];
                    addToCart(el.dataset.id, el.dataset.nama, parseInt(el.dataset.stok));
                }
            } else {
                alert('Barcode tidak ditemukan!');
            }
        }
    </script>
@endsection
