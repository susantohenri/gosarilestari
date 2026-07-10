<?php include __DIR__ . '/_helpers.php'; ?>

<?php sidebar_section('Menu Utama'); ?>
<?php sidebar_link('Dashboard', 'Dashboard', 'house', 'Overview', $current); ?>
<?php sidebar_link('Warga', 'Warga', 'users', 'Manajemen Warga', $current); ?>
<?php sidebar_link('Rtrw', 'Rtrw', 'map-location-dot', 'RT/RW', $current); ?>
<?php sidebar_link('KategoriSampah', 'KategoriSampah', 'tags', 'Kategori Sampah', $current); ?>
<?php sidebar_link('ProdukTukar', 'ProdukTukar', 'box-open', 'Produk Tukar', $current); ?>
<?php sidebar_section_end(); ?>

<?php sidebar_section('Transaksi'); ?>
<?php sidebar_link('SetorSampah', 'SetorSampah', 'recycle', 'Setor Sampah', $current); ?>
<?php sidebar_link('Pembayaran', 'Pembayaran', 'money-bill-wave', 'Pembayaran', $current); ?>
<?php sidebar_link('Penukaran', 'Penukaran', 'cart-shopping', 'Penukaran Produk', $current); ?>
<?php sidebar_link('Ledger', 'Ledger', 'clock-rotate-left', 'Riwayat Transaksi', $current); ?>
<?php sidebar_section_end(); ?>

<?php sidebar_section('Sistem'); ?>
<?php sidebar_link('Informasi', 'Informasi', 'newspaper', 'Informasi', $current); ?>
<?php sidebar_link('Konfigurasi', 'Konfigurasi', 'gear', 'Konfigurasi', $current); ?>
<?php sidebar_link('Notifikasi', 'Notifikasi', 'bell', 'Notifikasi', $current); ?>
<?php sidebar_link('Profile', 'Profile', 'user', 'Profile', $current); ?>
<?php sidebar_section_end(); ?>
