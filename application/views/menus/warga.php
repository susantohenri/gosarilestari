<?php include __DIR__ . '/_helpers.php'; ?>

<?php sidebar_section('Menu Utama'); ?>
<?php sidebar_link('Ledger', 'Ledger', 'clock-rotate-left', 'Riwayat Transaksi', $current); ?>
<?php sidebar_link('TukarProduk', 'TukarProduk', 'cart-shopping', 'Tukar Produk', $current); ?>
<?php sidebar_section_end(); ?>

<?php sidebar_section('Sistem'); ?>
<?php sidebar_link('Notifikasi', 'Notifikasi', 'bell', 'Notifikasi', $current); ?>
<?php sidebar_section_end(); ?>
