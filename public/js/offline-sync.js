/**
 * offline-sync.js
 *
 * Bertugas mengirim ulang (sync) semua transaksi penjualan yang tersimpan
 * offline di IndexedDB (lihat offline-db.js) ke server, begitu koneksi
 * internet tersedia lagi.
 *
 * PERBAIKAN dari versi sebelumnya:
 * 1. Sebelumnya ada DUA fungsi bernama sama "syncOfflinePenjualan" (satu di
 *    sini, satu lagi didefinisikan ulang di penjualan/create.blade.php yang
 *    pakai localStorage). Yang di blade menimpa yang di file ini sehingga
 *    listener 'online' yang dipasang di file ini memanggil definisi yang
 *    salah / tidak konsisten datanya. Sekarang logic offline di blade sudah
 *    dihapus, jadi file ini satu-satunya yang menangani sync.
 * 2. Penentuan sukses sebelumnya hanya mengandalkan try-catch tanpa memeriksa
 *    response.ok, jadi response error dari server (4xx/5xx) bisa salah
 *    dianggap berhasil dan datanya hilang padahal belum tersimpan di server.
 * 3. Sebelumnya HANYA mencoba sync saat event 'online' browser ditembak.
 *    Event ini tidak selalu reliable (tergantung browser/OS), jadi ditambah
 *    polling ringan tiap beberapa detik sebagai jaring pengaman, dan dicoba
 *    juga setiap kali halaman baru dimuat.
 * 4. Ditambahkan event kustom 'penjualan-sync-update' yang di-dispatch ke
 *    `window` setiap kali status sync berubah, supaya tampilan (badge jumlah
 *    transaksi pending, notifikasi sukses) bisa ikut update otomatis tanpa
 *    perlu refresh halaman.
 */

let isSyncing = false;

async function syncOfflinePenjualan() {
    if (!navigator.onLine) return;
    if (isSyncing) return; // cegah sync dobel kalau dipanggil bersamaan

    isSyncing = true;

    try {
        const penjualans = await getOfflinePenjualan();

        if (penjualans.length === 0) {
            notifySyncStatus({ pending: 0, justSynced: 0, failed: 0 });
            return;
        }

        let justSynced = 0;
        let failed = 0;

        for (const trx of penjualans) {
            try {
                const response = await fetch('/penjualan', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({
                        tanggal_penjualan: trx.tanggal_penjualan,
                        details_json: JSON.stringify(trx.details),
                    }),
                });

                // PENTING: cek response.ok, jangan asumsikan sukses hanya
                // karena fetch tidak melempar error. Request yang sampai ke
                // server tapi ditolak (422, 500, dst) HARUS tetap dianggap
                // gagal supaya data tidak hilang dari antrian offline.
                if (response.ok) {
                    const result = await response.json().catch(() => null);

                    if (result && result.success === false) {
                        failed++;
                        continue;
                    }

                    await deleteOfflinePenjualan(trx.id);
                    justSynced++;
                    console.log('[offline-sync] Transaksi', trx.id, 'berhasil disinkronkan');
                } else {
                    failed++;
                    console.warn('[offline-sync] Server menolak transaksi', trx.id, response.status);
                }

            } catch (error) {
                // Kemungkinan koneksi putus lagi di tengah proses sync.
                // Hentikan loop, sisanya akan dicoba lagi nanti.
                failed++;
                console.error('[offline-sync] Gagal mengirim transaksi', trx.id, error);
                break;
            }
        }

        const remaining = await countOfflinePenjualan();
        notifySyncStatus({ pending: remaining, justSynced, failed });

    } finally {
        isSyncing = false;
    }
}

function notifySyncStatus(detail) {
    window.dispatchEvent(new CustomEvent('penjualan-sync-update', { detail }));
}

// ===== Pemicu sync =====

// 1. Saat browser mendeteksi koneksi kembali online
window.addEventListener('online', () => {
    console.log('[offline-sync] Koneksi internet kembali, mencoba sinkronisasi...');
    syncOfflinePenjualan();
});

// 2. Saat halaman pertama kali dimuat (jaga-jaga ada transaksi
//    tertinggal dari sesi sebelumnya yang belum tersinkron).
//    Cek document.readyState langsung karena script ini dimuat di akhir
//    <body> tanpa 'defer', sehingga event DOMContentLoaded bisa saja sudah
//    lewat sebelum listener di bawah ini terpasang.
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => {
        syncOfflinePenjualan();
    });
} else {
    syncOfflinePenjualan();
}

// 3. Polling ringan tiap 15 detik sebagai jaring pengaman, karena event
//    'online' tidak selalu konsisten ditembak oleh semua browser/perangkat.
setInterval(() => {
    syncOfflinePenjualan();
}, 15000);
