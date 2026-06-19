/**
 * offline-db.js
 *
 * Satu-satunya tempat penyimpanan data penjualan offline di sistem ini.
 * Sebelumnya ada DUA sistem offline yang berjalan bersamaan (localStorage di
 * penjualan/create.blade.php DAN IndexedDB di file ini) sehingga saling
 * tabrakan dan data offline tidak konsisten saat disinkronkan.
 *
 * Sekarang IndexedDB dijadikan satu-satunya sumber data offline karena lebih
 * reliable dan kapasitasnya jauh lebih besar dibanding localStorage.
 */

const DB_NAME = 'inventori_db';
const DB_VERSION = 1;
const STORE_NAME = 'pending_penjualan';

function openDB() {
    return new Promise((resolve, reject) => {
        const request = indexedDB.open(DB_NAME, DB_VERSION);

        request.onupgradeneeded = function (event) {
            const db = event.target.result;

            if (!db.objectStoreNames.contains(STORE_NAME)) {
                db.createObjectStore(STORE_NAME, {
                    keyPath: 'id',
                    autoIncrement: true,
                });
            }
        };

        request.onsuccess = function () {
            resolve(request.result);
        };

        request.onerror = function () {
            reject(request.error);
        };
    });
}

/**
 * Simpan satu transaksi penjualan ke penyimpanan offline.
 * data harus berbentuk: { tanggal_penjualan, details, total_harga }
 */
async function saveOfflinePenjualan(data) {
    const db = await openDB();

    return new Promise((resolve, reject) => {
        const tx = db.transaction(STORE_NAME, 'readwrite');

        const record = {
            ...data,
            created_at: new Date().toISOString(),
            synced: false,
        };

        const request = tx.objectStore(STORE_NAME).add(record);

        request.onsuccess = () => {
            console.log('[offline-db] Penjualan disimpan offline, id:', request.result);
            resolve(request.result);
        };

        request.onerror = () => reject(request.error);
    });
}

/**
 * Ambil semua transaksi penjualan yang masih tersimpan offline (belum sync).
 */
async function getOfflinePenjualan() {
    const db = await openDB();

    return new Promise((resolve, reject) => {
        const tx = db.transaction(STORE_NAME, 'readonly');
        const request = tx.objectStore(STORE_NAME).getAll();

        request.onsuccess = () => resolve(request.result);
        request.onerror = () => reject(request.error);
    });
}

/**
 * Hitung jumlah transaksi yang masih pending (belum tersinkron).
 * Dipakai untuk menampilkan badge "ada N transaksi belum tersinkron".
 */
async function countOfflinePenjualan() {
    const all = await getOfflinePenjualan();
    return all.length;
}

/**
 * Hapus satu transaksi offline berdasarkan id (dipanggil setelah sync sukses).
 */
async function deleteOfflinePenjualan(id) {
    const db = await openDB();

    return new Promise((resolve, reject) => {
        const tx = db.transaction(STORE_NAME, 'readwrite');
        const request = tx.objectStore(STORE_NAME).delete(id);

        request.onsuccess = () => resolve();
        request.onerror = () => reject(request.error);
    });
}
