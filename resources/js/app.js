import './bootstrap';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();

// Contoh JavaScript dasar Pertemuan 9 pada halaman demo RIZWEAR.
document.addEventListener('DOMContentLoaded', function () {
    const demo = document.getElementById('javascript-demo');

    // Demo hanya dijalankan jika tombol dan area hasil ada di halaman.
    if (!demo) return;

    const demoButton = demo.querySelector('#javascript-demo-button');
    const demoOutput = demo.querySelector('#javascript-demo-output');

    function jalankanDemoRizwear() {
        // var, let, dan const digunakan untuk menyimpan nilai.
        var namaTokoVar = 'RIZWEAR';
        const namaToko = namaTokoVar;
        let jumlahProduk = 3;

        // Contoh tipe data: String, Number, Boolean, Array, dan Object.
        const kategoriProduk = 'Pakaian';
        const hargaProduk = 150000;
        const tokoAktif = true;
        const kategoriDaftar = ['Baju', 'Celana', 'Aksesoris'];
        const toko = { nama: namaToko, kota: 'Indonesia' };

        // Operator penugasan dan aritmatika: 3 + 2 menjadi 5.
        jumlahProduk += 2;
        const hargaTotal = hargaProduk * jumlahProduk;

        // Operator perbandingan dan logika.
        const jumlahBertambah = jumlahProduk > 3;
        const tokoSiap = tokoAktif && jumlahBertambah;

        const informasi = {
            namaToko,
            jumlahProduk,
            kategoriProduk,
            hargaProduk,
            tokoAktif,
            kategoriDaftar,
            toko,
            hargaTotal,
            jumlahBertambah,
            tokoSiap,
        };

        console.log('Demo JavaScript Dasar RIZWEAR:', informasi);
        demoOutput.textContent =
            namaToko + ': jumlah produk sekarang ' + jumlahProduk +
            ', total harga ' + hargaTotal.toLocaleString('id-ID') +
            ', operator perbandingan/logika: ' + jumlahBertambah + '/' + tokoSiap;

        alert(namaToko + ' memiliki ' + jumlahProduk + ' jumlah produk.');
    }

    // Informasi dasar ditampilkan melalui Developer Tools saat halaman utama dibuka.
    var namaTokoVar = 'RIZWEAR';
    const namaTokoRizwear = namaTokoVar;
    let jumlahProdukRizwear = 3;
    jumlahProdukRizwear += 2;

    console.log('RIZWEAR:', {
        namaToko: namaTokoRizwear,
        jumlahProduk: jumlahProdukRizwear,
        tipeNamaToko: typeof namaTokoRizwear,
        tipeJumlahProduk: typeof jumlahProdukRizwear,
    });

    demoButton.addEventListener('click', jalankanDemoRizwear);
});
