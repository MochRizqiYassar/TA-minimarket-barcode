/**
 * Service Worker - Inventori Kasir
 *
 * Strategi:
 * 1. Halaman (navigasi: dashboard, /penjualan, /penjualan/create, dll)
 *    -> Network First, fallback ke Cache, fallback terakhir ke halaman /offline.html
 *    Setiap halaman yang berhasil dibuka saat online otomatis disimpan ke cache,
 *    jadi kalau nanti koneksi putus, halaman terakhir yang pernah dibuka tetap bisa diakses.
 *
 * 2. Asset statis (css, js, gambar, font)
 *    -> Cache First, fallback ke network. Dipakai supaya tampilan (layout, css, js)
 *      tidak hilang saat offline.
 *
 * 3. Request API/AJAX (misalnya fetch ke /penjualan dengan method POST)
 *    -> TIDAK disentuh sama sekali oleh service worker ini (dibiarkan gagal natural),
 *      supaya logic offline-sync di offline-db.js / offline-sync.js yang menangani.
 */

const CACHE_VERSION = 'v3';
const CACHE_NAME = 'inventori-cache-' + CACHE_VERSION;
const OFFLINE_URL = '/offline.html';

// Halaman penting yang langsung di-precache saat service worker pertama kali aktif.
// Jadi sejak awal install, kasir bisa pindah-pindah menu walau internet tiba-tiba mati.
const PRECACHE_URLS = [
    '/offline.html',
    '/dashboard',
    '/penjualan',
    '/penjualan/create',
];

self.addEventListener('install', event => {
    event.waitUntil(
        caches.open(CACHE_NAME).then(cache => {
            return Promise.allSettled(
                PRECACHE_URLS.map(url =>
                    fetch(url, { credentials: 'same-origin' })
                        .then(response => {
                            // Hanya simpan kalau benar-benar berhasil (200) DAN
                            // bukan halaman login (kasir/admin belum login akan
                            // diarahkan ke /login, jangan sampai itu yang ke-cache
                            // sebagai pengganti halaman dashboard/penjualan asli).
                            if (response.ok && !response.url.includes('/login')) {
                                return cache.put(url, response);
                            }
                        })
                        .catch(() => {
                            // Kalau salah satu URL gagal di-precache (misal belum
                            // login atau memang offline saat install), jangan
                            // sampai bikin instalasi service worker gagal total.
                        })
                )
            );
        }).then(() => self.skipWaiting())
    );
});

self.addEventListener('activate', event => {
    event.waitUntil(
        caches.keys().then(keys =>
            Promise.all(keys.filter(key => key !== CACHE_NAME).map(key => caches.delete(key)))
        ).then(() => self.clients.claim())
    );
});

self.addEventListener('fetch', event => {
    const req = event.request;

    // Hanya tangani request GET. POST/PUT/DELETE (termasuk submit penjualan)
    // dibiarkan lewat begitu saja supaya logic offline-sync yang menangani.
    if (req.method !== 'GET') {
        return;
    }

    // ===== 1. NAVIGASI HALAMAN (klik link sidebar, refresh, dll) =====
    if (req.mode === 'navigate') {
        event.respondWith(networkFirstForPages(req));
        return;
    }

    // ===== 2. ASSET STATIS (css, js, gambar, font) =====
    const url = new URL(req.url);
    const isStaticAsset =
        url.origin === self.location.origin &&
        (/\.(css|js|png|jpg|jpeg|gif|svg|webp|woff|woff2|ttf|ico)$/i.test(url.pathname));

    if (isStaticAsset) {
        event.respondWith(cacheFirstForAssets(req));
        return;
    }

    // Request lain (API, XHR/fetch ke endpoint json, dll) -> biarkan jalan normal,
    // tidak dicampuri service worker.
});

async function networkFirstForPages(request) {
    const cache = await caches.open(CACHE_NAME);

    try {
        const response = await fetch(request);

        // Halaman berhasil diambil dari server -> simpan salinan terbaru ke cache.
        // Jangan cache kalau ternyata di-redirect ke /login (sesi habis), supaya
        // cache halaman dashboard/penjualan tidak ketiban halaman login.
        if (response && response.status === 200 && !response.url.includes('/login')) {
            cache.put(request, response.clone());
        }

        return response;

    } catch (err) {
        // Network gagal (offline) -> coba ambil dari cache halaman yang sama
        const cached = await cache.match(request);
        if (cached) {
            return cached;
        }

        // Belum pernah di-cache -> tampilkan halaman fallback offline
        const offlineFallback = await cache.match(OFFLINE_URL);
        if (offlineFallback) {
            return offlineFallback;
        }

        // Last resort kalau offline.html pun belum ke-cache
        return new Response(
            '<h1>Offline</h1><p>Halaman ini belum tersedia secara offline.</p>',
            { headers: { 'Content-Type': 'text/html' } }
        );
    }
}

async function cacheFirstForAssets(request) {
    const cache = await caches.open(CACHE_NAME);
    const cached = await cache.match(request);

    if (cached) {
        // Tetap update cache di background biar asset tidak basi
        fetch(request).then(response => {
            if (response && response.status === 200) {
                cache.put(request, response.clone());
            }
        }).catch(() => {});

        return cached;
    }

    try {
        const response = await fetch(request);
        if (response && response.status === 200) {
            cache.put(request, response.clone());
        }
        return response;
    } catch (err) {
        // Asset tidak ada di cache dan network gagal -> biarkan error,
        // browser akan menampilkan gambar/elemen kosong untuk asset ini saja.
        throw err;
    }
}
