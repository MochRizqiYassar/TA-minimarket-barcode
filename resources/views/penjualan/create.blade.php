@extends('layouts.kasir')

@section('content')
    <div class="container-fluid">
        <h4>Penjualan</h4>

        <form action="{{ route('penjualan.store') }}" method="POST">
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

                        <div class="row" id="product-list">
                            @foreach ($barangs as $b)
                                <div class="col-md-6 mb-3 product-item" data-id="{{ $b->id_barang }}"
                                    data-nama="{{ $b->nama_barang }}" data-harga="{{ $b->harga_jual }}"
                                    data-stok="{{ $b->stok }}">

                                    <div class="card p-2 text-center product-card"
                                        style="cursor:pointer;
     @if ($b->stok == 0) opacity:0.5; pointer-events:none; @endif">
                                        <img src="{{ $b->foto ? asset('storage/' . $b->foto) : asset('assets/images/no-image.png') }}"
                                            height="80">

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
        let cart = [];

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
        }

        function addToCart(id, nama, harga, stok) {
            if (stok <= 0) {
                alert('Stok habis!');
                return;
            }

            let item = cart.find(i => i.id == id);

            if (item) {
                if (item.qty >= stok) {
                    alert('Stok tidak cukup!');
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
                alert('Stok tidak cukup!');
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
    </script>
@endsection
