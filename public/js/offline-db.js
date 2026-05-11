const DB_NAME = 'inventori_db';
const STORE_NAME = 'pending_penjualan';

function openDB() {

    return new Promise((resolve, reject) => {

        const request = indexedDB.open(DB_NAME, 1);

        request.onupgradeneeded = function(event) {

            const db = event.target.result;

            if (!db.objectStoreNames.contains(STORE_NAME)) {

                db.createObjectStore(STORE_NAME, {
                    keyPath: 'id',
                    autoIncrement: true
                });
            }
        };

        request.onsuccess = function() {
            resolve(request.result);
        };

        request.onerror = function() {
            reject(request.error);
        };
    });
}

async function saveOfflinePenjualan(data) {

    const db = await openDB();

    const tx = db.transaction(STORE_NAME, 'readwrite');

    tx.objectStore(STORE_NAME).add({
        ...data,
        created_at: new Date().toISOString(),
        synced: false
    });

    console.log('Penjualan disimpan offline');
}

async function getOfflinePenjualan() {

    const db = await openDB();

    return new Promise((resolve, reject) => {

        const tx = db.transaction(STORE_NAME, 'readonly');

        const request = tx.objectStore(STORE_NAME).getAll();

        request.onsuccess = () => resolve(request.result);

        request.onerror = () => reject(request.error);
    });
}

async function deleteOfflinePenjualan(id) {

    const db = await openDB();

    const tx = db.transaction(STORE_NAME, 'readwrite');

    tx.objectStore(STORE_NAME).delete(id);
}
