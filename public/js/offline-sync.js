async function syncOfflinePenjualan() {

    if (!navigator.onLine) return;

    const penjualans = await getOfflinePenjualan();

    for (const trx of penjualans) {

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

                console.log('Sync berhasil');

                await deleteOfflinePenjualan(trx.id);
            }

        } catch (error) {

            console.error('Sync gagal', error);
        }
    }
}

window.addEventListener('online', () => {

    console.log('Internet kembali');

    syncOfflinePenjualan();
});

document.addEventListener('DOMContentLoaded', () => {

    syncOfflinePenjualan();
});
