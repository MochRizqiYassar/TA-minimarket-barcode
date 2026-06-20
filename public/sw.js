const CACHE_VERSION = 'v5';
const CACHE_NAME = 'inventori-cache-' + CACHE_VERSION;
const OFFLINE_URL = '/offline.html';

const LOGIN_CACHE_NAME = 'inventori-login-cache-' + CACHE_VERSION;
const LOGIN_URL = '/login';

const PRECACHE_URLS = [
    '/offline.html',
    '/kasir/dashboard',
    '/penjualan',
    '/penjualan/create',
];

async function cleanResponse(response) {
    if (!response.redirected) {
        return response;
    }
    const body = await response.clone().blob();
    return new Response(body, {
        status: response.status,
        statusText: response.statusText,
        headers: response.headers,
    });
}

self.addEventListener('install', event => {
    event.waitUntil(
        caches.open(CACHE_NAME).then(cache => {
            return Promise.allSettled(
                PRECACHE_URLS.map(url =>
                    fetch(url, { credentials: 'same-origin' })
                        .then(async response => {

                            if (response.ok && !response.url.includes('/login')) {
                                return cache.put(url, await cleanResponse(response));
                            }
                        })
                        .catch(() => {

                        })
                )
            );
        }).then(() => self.skipWaiting())
    );
});

self.addEventListener('activate', event => {
    event.waitUntil(
        caches.keys().then(keys =>
            Promise.all(
                keys
                    .filter(key => key !== CACHE_NAME && key !== LOGIN_CACHE_NAME)
                    .map(key => caches.delete(key))
            )
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
        const url = new URL(req.url);

        // Halaman /login ditangani terpisah (lihat networkFirstForLoginPage):
        // disimpan ke cache sendiri supaya tidak bercampur dengan halaman
        // dashboard/penjualan, dan supaya tetap punya fallback tampilan form
        // login walau lagi offline saat sesi habis / mau login ulang.
        if (url.pathname === LOGIN_URL) {
            event.respondWith(networkFirstForLoginPage(req));
            return;
        }

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
        //
        // response.clone() di sini tetap membawa flag redirected (kalau request
        // ini sempat di-redirect server, mis. lewat route redirector). Itu masih
        // aman SELAMA key cache-nya = URL request yang sama dengan URL akhir
        // response (artinya tidak ada redirect, browser sudah mengarah ke URL
        // yang benar sebelum sampai sini). Tetap dibersihkan untuk jaga-jaga.
        if (response && response.status === 200 && !response.url.includes('/login')) {
            cache.put(request, await cleanResponse(response.clone()));
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

async function networkFirstForLoginPage(request) {
    const loginCache = await caches.open(LOGIN_CACHE_NAME);

    try {
        const response = await fetch(request);

        // Kalau user yang request ini SUDAH login, server akan me-redirect
        // /login ke dashboard (middleware 'guest'). response.url pada kasus
        // itu tidak lagi diakhiri '/login' -- jangan simpan itu sebagai
        // pengganti halaman login (supaya cache login tidak ketiban
        // halaman dashboard milik user lain/sesi lain).
        if (response && response.status === 200 && new URL(response.url).pathname === LOGIN_URL) {
            loginCache.put(request, await cleanResponse(response.clone()));
        }

        return response;

    } catch (err) {
        // Network gagal (offline) -> coba ambil tampilan form login dari cache.
        // CATATAN: ini HANYA mengembalikan HTML form-nya. Submit (POST) tetap
        // butuh internet, jadi ini bukan login offline -- cuma supaya kasir
        // tidak nyasar ke halaman offline.html generik saat yang sebenarnya
        // dia butuhkan cuma form login untuk login ulang.
        const cachedLogin = await loginCache.match(request);
        if (cachedLogin) {
            return cachedLogin;
        }

        // Belum pernah berhasil membuka /login saat online -> tidak ada apapun
        // yang bisa disuguhkan, jatuh ke offline.html seperti halaman lain.
        const mainCache = await caches.open(CACHE_NAME);
        const offlineFallback = await mainCache.match(OFFLINE_URL);
        if (offlineFallback) {
            return offlineFallback;
        }

        return new Response(
            '<h1>Offline</h1><p>Halaman login belum tersedia secara offline.</p>',
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
