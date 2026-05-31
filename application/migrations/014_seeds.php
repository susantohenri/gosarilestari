<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Migration_seeds extends CI_Migration
{
    public function up()
    {
        $this->load->model([
            'Users',
            'Roles',
            'Permissions',
            'Menus',
            'Konfigurasis',
            'Wargas',
            'Rtrws',
            'KategoriSampahs',
            'ProdukTukars',
            'Notifikasis'
        ]);
        $fas = ['database', 'desktop', 'download', 'ethernet', 'hdd', 'hdd', 'headphones', 'keyboard', 'keyboard', 'laptop', 'memory', 'microchip', 'mobile', 'mobile-alt', 'plug', 'power-off', 'print', 'satellite', 'satellite-dish', 'save', 'save', 'sd-card', 'server', 'sim-card', 'stream', 'tablet', 'tablet-alt', 'tv', 'upload'];

        $admin = $this->Roles->create(['name' => 'Admin']);
        $petugas = $this->Roles->create(['name' => 'Petugas']);
        $warga = $this->Roles->create(['name' => 'Warga']);

        $baseEntities = ['User', 'Role', 'Permission', 'Menu'];
        $petugasEntities = ['Warga', 'Rtrw', 'KategoriSampah', 'ProdukTukar'];
        $transaksiEntities = ['SetorSampah', 'SetorTunai', 'TukarProduk'];
        $wargaEntities = ['TukarProduk', 'Notifikasi'];
        $allPermissions = ['index', 'create', 'read', 'update', 'delete'];
        $wargaPermissions = ['index', 'read'];

        // permission superadmin
        foreach (array_merge($baseEntities, $petugasEntities) as $entity) {
            foreach ($allPermissions as $action) {
                $this->Permissions->create([
                    'role' => $admin,
                    'action' => $action,
                    'entity' => $entity
                ]);
            }
            $this->Menus->create([
                'role' => $admin,
                'name' => $entity,
                'url' => $entity,
                'icon' => $fas[rand(0, count($fas) - 1)]
            ]);
        }

        // permission petugas
        foreach ($petugasEntities as $entity) {
            foreach ($allPermissions as $action) {
                $this->Permissions->create([
                    'role' => $petugas,
                    'action' => $action,
                    'entity' => $entity
                ]);
            }
            $this->Menus->create([
                'role' => $petugas,
                'name' => $entity,
                'url' => $entity,
                'icon' => $fas[rand(0, count($fas) - 1)]
            ]);
        }
        foreach ($transaksiEntities as $entity) {
            foreach (['index', 'create', 'read'] as $action) {
                $this->Permissions->create([
                    'role' => $petugas,
                    'action' => $action,
                    'entity' => $entity
                ]);
            }
            $this->Menus->create([
                'role' => $petugas,
                'name' => $entity,
                'url' => $entity,
                'icon' => $fas[rand(0, count($fas) - 1)]
            ]);
        }
        foreach (['index', 'read', 'update'] as $action) {
            $this->Permissions->create([
                'role' => $petugas,
                'action' => $action,
                'entity' => 'Konfigurasi'
            ]);
        }
        foreach (['Ledger', 'Notifikasi'] as $entity) {
            foreach (['index', 'read'] as $action) {
                $this->Permissions->create([
                    'role' => $petugas,
                    'action' => $action,
                    'entity' => $entity
                ]);
            }
        }

        // permission warga
        foreach ($wargaEntities as $entity) {
            foreach ($wargaPermissions as $action) {
                $this->Permissions->create([
                    'role' => $warga,
                    'action' => $action,
                    'entity' => $entity
                ]);
            }
            $this->Menus->create([
                'role' => $warga,
                'name' => $entity,
                'url' => $entity,
                'icon' => $fas[rand(0, count($fas) - 1)]
            ]);
        }
        $this->Permissions->create([
            'role' => $warga,
            'action' => 'create',
            'entity' => 'TukarProduk'
        ]);

        $this->Users->create([
            'nama' => 'Administrator',
            'username' => 'admin',
            'password' => md5('admin'),
            'role' => $admin
        ]);

        $this->Konfigurasis->create([
            'nama' => 'TARGET_SAMPAH_BULAN_INI',
            'nilai' => '2000'
        ]);

        $this->Konfigurasis->create([
            'nama' => 'BATAS_MINIMUM_STOK_RENDAH',
            'nilai' => '10'
        ]);

        $this->Konfigurasis->create([
            'nama' => 'TANGGAL_PENGIRIMAN_NOTIFIKASI_WARGA',
            'nilai' => '1'
        ]);

        $this->Konfigurasis->create([
            'nama' => 'TANGGAL_PENGIRIMAN_NOTIFIKASI_PETUGAS',
            'nilai' => '5'
        ]);

        $this->KategoriSampahs->create([
            'nama' => 'Plastik',
            'contoh' => 'PET, botol, kresek',
            'harga' => 3500
        ]);

        $this->KategoriSampahs->create([
            'nama' => 'Kertas',
            'contoh' => 'koran, kardus, HVS',
            'harga' => 2000
        ]);

        $this->KategoriSampahs->create([
            'nama' => 'Logam',
            'contoh' => 'Kaleng, besi, alumunium',
            'harga' => 7000
        ]);

        $this->KategoriSampahs->create([
            'nama' => 'Kaca',
            'contoh' => 'Botol kaca bening',
            'harga' => 1500
        ]);

        $this->KategoriSampahs->create([
            'nama' => 'Minyak Jelantah',
            'contoh' => 'Per liter, bersih',
            'harga' => 5500
        ]);

        $rtrws = [["nama" => "Kembangputihan RT 001", "latitude" => "-7.8732", "longitude" => "110.2998"], ["nama" => "Kembangputihan RT 002", "latitude" => "-7.8732", "longitude" => "110.2998"], ["nama" => "Kembangputihan RT 003", "latitude" => "-7.8732", "longitude" => "110.2998"], ["nama" => "Kembangputihan RT 004", "latitude" => "-7.8732", "longitude" => "110.2998"], ["nama" => "Kembangputihan RT 005", "latitude" => "-7.8732", "longitude" => "110.2998"], ["nama" => "Kembangputihan RT 006", "latitude" => "-7.8732", "longitude" => "110.2998"], ["nama" => "Kentolan Lor RT 001", "latitude" => "-7.8754", "longitude" => "110.3014"], ["nama" => "Kentolan Lor RT 002", "latitude" => "-7.8754", "longitude" => "110.3014"], ["nama" => "Kentolan Lor RT 003", "latitude" => "-7.8754", "longitude" => "110.3014"], ["nama" => "Kentolan Lor RT 004", "latitude" => "-7.8754", "longitude" => "110.3014"], ["nama" => "Kentolan Lor RT 005", "latitude" => "-7.8754", "longitude" => "110.3014"], ["nama" => "Kentolan Lor RT 006", "latitude" => "-7.8754", "longitude" => "110.3014"], ["nama" => "Kentolan Kidul RT 001", "latitude" => "-7.8781", "longitude" => "110.3017"], ["nama" => "Kentolan Kidul RT 002", "latitude" => "-7.8781", "longitude" => "110.3017"], ["nama" => "Kentolan Kidul RT 003", "latitude" => "-7.8781", "longitude" => "110.3017"], ["nama" => "Kentolan Kidul RT 004", "latitude" => "-7.8781", "longitude" => "110.3017"], ["nama" => "Gandekan RT 001", "latitude" => "-7.8794", "longitude" => "110.3042"], ["nama" => "Gandekan RT 002", "latitude" => "-7.8794", "longitude" => "110.3042"], ["nama" => "Gandekan RT 003", "latitude" => "-7.8794", "longitude" => "110.3042"], ["nama" => "Gandekan RT 004", "latitude" => "-7.8794", "longitude" => "110.3042"], ["nama" => "Gandekan RT 005", "latitude" => "-7.8794", "longitude" => "110.3042"], ["nama" => "Dukuh RT 001", "latitude" => "-7.8817", "longitude" => "110.3065"], ["nama" => "Dukuh RT 002", "latitude" => "-7.8817", "longitude" => "110.3065"], ["nama" => "Dukuh RT 003", "latitude" => "-7.8817", "longitude" => "110.3065"], ["nama" => "Dukuh RT 004", "latitude" => "-7.8817", "longitude" => "110.3065"], ["nama" => "Dukuh RT 005", "latitude" => "-7.8817", "longitude" => "110.3065"], ["nama" => "Dukuh RT 006", "latitude" => "-7.8817", "longitude" => "110.3065"], ["nama" => "Iroyudan RT 001", "latitude" => "-7.8836", "longitude" => "110.3083"], ["nama" => "Iroyudan RT 002", "latitude" => "-7.8836", "longitude" => "110.3083"], ["nama" => "Iroyudan RT 003", "latitude" => "-7.8836", "longitude" => "110.3083"], ["nama" => "Iroyudan RT 004", "latitude" => "-7.8836", "longitude" => "110.3083"], ["nama" => "Iroyudan RT 005", "latitude" => "-7.8836", "longitude" => "110.3083"], ["nama" => "Iroyudan RT 006", "latitude" => "-7.8836", "longitude" => "110.3083"], ["nama" => "Kadisono RT 001", "latitude" => "-7.8851", "longitude" => "110.3111"], ["nama" => "Kadisono RT 002", "latitude" => "-7.8851", "longitude" => "110.3111"], ["nama" => "Kadisono RT 003", "latitude" => "-7.8851", "longitude" => "110.3111"], ["nama" => "Kadisono RT 004", "latitude" => "-7.8851", "longitude" => "110.3111"], ["nama" => "Kembanggede RT 001", "latitude" => "-7.8874", "longitude" => "110.3137"], ["nama" => "Kembanggede RT 002", "latitude" => "-7.8874", "longitude" => "110.3137"], ["nama" => "Kembanggede RT 003", "latitude" => "-7.8874", "longitude" => "110.3137"], ["nama" => "Kembanggede RT 004", "latitude" => "-7.8874", "longitude" => "110.3137"], ["nama" => "Karangber RT 001", "latitude" => "-7.8898", "longitude" => "110.3155"], ["nama" => "Karangber RT 002", "latitude" => "-7.8898", "longitude" => "110.3155"], ["nama" => "Karangber RT 003", "latitude" => "-7.8898", "longitude" => "110.3155"], ["nama" => "Karangber RT 004", "latitude" => "-7.8898", "longitude" => "110.3155"], ["nama" => "Santan RT 001", "latitude" => "-7.8912", "longitude" => "110.3178"], ["nama" => "Santan RT 002", "latitude" => "-7.8912", "longitude" => "110.3178"], ["nama" => "Santan RT 003", "latitude" => "-7.8912", "longitude" => "110.3178"], ["nama" => "Santan RT 004", "latitude" => "-7.8912", "longitude" => "110.3178"], ["nama" => "Kalakijo RT 001", "latitude" => "-7.8934", "longitude" => "110.3202"], ["nama" => "Kalakijo RT 002", "latitude" => "-7.8934", "longitude" => "110.3202"], ["nama" => "Kalakijo RT 003", "latitude" => "-7.8934", "longitude" => "110.3202"], ["nama" => "Kalakijo RT 004", "latitude" => "-7.8934", "longitude" => "110.3202"], ["nama" => "Kalakijo RT 005", "latitude" => "-7.8934", "longitude" => "110.3202"], ["nama" => "Kalakijo RT 006", "latitude" => "-7.8934", "longitude" => "110.3202"], ["nama" => "Kedung RT 001", "latitude" => "-7.8961", "longitude" => "110.3225"], ["nama" => "Kedung RT 002", "latitude" => "-7.8961", "longitude" => "110.3225"], ["nama" => "Kedung RT 003", "latitude" => "-7.8961", "longitude" => "110.3225"], ["nama" => "Kedung RT 004", "latitude" => "-7.8961", "longitude" => "110.3225"], ["nama" => "Bungsing RT 001", "latitude" => "-7.8995", "longitude" => "110.3257"], ["nama" => "Bungsing RT 002", "latitude" => "-7.8995", "longitude" => "110.3257"], ["nama" => "Bungsing RT 003", "latitude" => "-7.8995", "longitude" => "110.3257"], ["nama" => "Bungsing RT 004", "latitude" => "-7.8995", "longitude" => "110.3257"], ["nama" => "Watugedug RT 001", "latitude" => "-7.9032", "longitude" => "110.3291"], ["nama" => "Watugedug RT 002", "latitude" => "-7.9032", "longitude" => "110.3291"], ["nama" => "Watugedug RT 003", "latitude" => "-7.9032", "longitude" => "110.3291"], ["nama" => "Watugedug RT 004", "latitude" => "-7.9032", "longitude" => "110.3291"], ["nama" => "Watugedug RT 005", "latitude" => "-7.9032", "longitude" => "110.3291"], ["nama" => "Watugedug RT 006", "latitude" => "-7.9032", "longitude" => "110.3291"], ["nama" => "Pringgading RT 001", "latitude" => "-7.9076", "longitude" => "110.3338"], ["nama" => "Pringgading RT 002", "latitude" => "-7.9076", "longitude" => "110.3338"], ["nama" => "Pringgading RT 003", "latitude" => "-7.9076", "longitude" => "110.3338"], ["nama" => "Pringgading RT 004", "latitude" => "-7.9076", "longitude" => "110.3338"], ["nama" => "Pringgading RT 005", "latitude" => "-7.9076", "longitude" => "110.3338"], ["nama" => "Pringgading RT 006", "latitude" => "-7.9076", "longitude" => "110.3338"], ["nama" => "Pringgading RT 007", "latitude" => "-7.9076", "longitude" => "110.3338"], ["nama" => "Pringgading RT 008", "latitude" => "-7.9076", "longitude" => "110.3338"], ["nama" => "Pringgading RT 009", "latitude" => "-7.9076", "longitude" => "110.3338"], ["nama" => "Pringgading RT 010", "latitude" => "-7.9076", "longitude" => "110.3338"]];
        $rtrwids = [];
        foreach ($rtrws as $rtrw) {
            $rtrwids[] = $this->Rtrws->create($rtrw);
        }

        if ('development' === ENVIRONMENT) {
            $this->Users->create([
                'username' => '081901088918',
                'password' => md5('123'),
                'nama' => 'Ahmad Rizki',
                'role' => $petugas
            ]);

            $dummyWargas=[['username'=>'087832600001','password'=>md5('123'),'nama'=>'Budi Santoso','alamat'=>'Jl. Mawar No. 1','kontak'=>'087832600001'],['username'=>'087832600002','password'=>md5('123'),'nama'=>'Siti Aminah','alamat'=>'Jl. Melati No. 2','kontak'=>'087832600002'],['username'=>'087832600003','password'=>md5('123'),'nama'=>'Andi Pratama','alamat'=>'Jl. Kenanga No. 3','kontak'=>'087832600003'],['username'=>'087832600004','password'=>md5('123'),'nama'=>'Rina Oktaviani','alamat'=>'Jl. Anggrek No. 4','kontak'=>'087832600004'],['username'=>'087832600005','password'=>md5('123'),'nama'=>'Dedi Kurniawan','alamat'=>'Jl. Dahlia No. 5','kontak'=>'087832600005'],['username'=>'087832600006','password'=>md5('123'),'nama'=>'Agus Saputra','alamat'=>'Jl. Flamboyan No. 6','kontak'=>'087832600006'],['username'=>'087832600007','password'=>md5('123'),'nama'=>'Lina Marlina','alamat'=>'Jl. Teratai No. 7','kontak'=>'087832600007'],['username'=>'087832600008','password'=>md5('123'),'nama'=>'Eko Prasetyo','alamat'=>'Jl. Cempaka No. 8','kontak'=>'087832600008'],['username'=>'087832600009','password'=>md5('123'),'nama'=>'Fitri Handayani','alamat'=>'Jl. Wijaya No. 9','kontak'=>'087832600009'],['username'=>'087832600010','password'=>md5('123'),'nama'=>'Joko Susilo','alamat'=>'Jl. Sakura No. 10','kontak'=>'087832600010'],['username'=>'087832600011','password'=>md5('123'),'nama'=>'Rahmat Hidayat','alamat'=>'Jl. Kamboja No. 11','kontak'=>'087832600011'],['username'=>'087832600012','password'=>md5('123'),'nama'=>'Nur Aisyah','alamat'=>'Jl. Cemara No. 12','kontak'=>'087832600012'],['username'=>'087832600013','password'=>md5('123'),'nama'=>'Yusuf Maulana','alamat'=>'Jl. Mangga No. 13','kontak'=>'087832600013'],['username'=>'087832600014','password'=>md5('123'),'nama'=>'Putri Lestari','alamat'=>'Jl. Nangka No. 14','kontak'=>'087832600014'],['username'=>'087832600015','password'=>md5('123'),'nama'=>'Rudi Hartono','alamat'=>'Jl. Durian No. 15','kontak'=>'087832600015'],['username'=>'087832600016','password'=>md5('123'),'nama'=>'Sri Wahyuni','alamat'=>'Jl. Rambutan No. 16','kontak'=>'087832600016'],['username'=>'087832600017','password'=>md5('123'),'nama'=>'Hendra Gunawan','alamat'=>'Jl. Pepaya No. 17','kontak'=>'087832600017'],['username'=>'087832600018','password'=>md5('123'),'nama'=>'Tika Ramadhani','alamat'=>'Jl. Apel No. 18','kontak'=>'087832600018'],['username'=>'087832600019','password'=>md5('123'),'nama'=>'Bayu Nugroho','alamat'=>'Jl. Salak No. 19','kontak'=>'087832600019'],['username'=>'087832600020','password'=>md5('123'),'nama'=>'Dewi Sartika','alamat'=>'Jl. Jeruk No. 20','kontak'=>'087832600020'],['username'=>'087832600021','password'=>md5('123'),'nama'=>'Ilham Fauzi','alamat'=>'Jl. Pinus No. 21','kontak'=>'087832600021'],['username'=>'087832600022','password'=>md5('123'),'nama'=>'Nina Karlina','alamat'=>'Jl. Jati No. 22','kontak'=>'087832600022'],['username'=>'087832600023','password'=>md5('123'),'nama'=>'Arif Setiawan','alamat'=>'Jl. Merpati No. 23','kontak'=>'087832600023'],['username'=>'087832600024','password'=>md5('123'),'nama'=>'Maya Sari','alamat'=>'Jl. Elang No. 24','kontak'=>'087832600024'],['username'=>'087832600025','password'=>md5('123'),'nama'=>'Rizky Firmansyah','alamat'=>'Jl. Rajawali No. 25','kontak'=>'087832600025'],['username'=>'087832600026','password'=>md5('123'),'nama'=>'Aulia Rahma','alamat'=>'Jl. Garuda No. 26','kontak'=>'087832600026'],['username'=>'087832600027','password'=>md5('123'),'nama'=>'Fajar Nugraha','alamat'=>'Jl. Kenari No. 27','kontak'=>'087832600027'],['username'=>'087832600028','password'=>md5('123'),'nama'=>'Wulan Pertiwi','alamat'=>'Jl. Cendrawasih No. 28','kontak'=>'087832600028'],['username'=>'087832600029','password'=>md5('123'),'nama'=>'Reza Maulana','alamat'=>'Jl. Elok No. 29','kontak'=>'087832600029'],['username'=>'087832600030','password'=>md5('123'),'nama'=>'Indah Permata','alamat'=>'Jl. Harmoni No. 30','kontak'=>'087832600030'],['username'=>'087832600031','password'=>md5('123'),'nama'=>'Galih Prakoso','alamat'=>'Jl. Damai No. 31','kontak'=>'087832600031'],['username'=>'087832600032','password'=>md5('123'),'nama'=>'Citra Ayu','alamat'=>'Jl. Sejahtera No. 32','kontak'=>'087832600032'],['username'=>'087832600033','password'=>md5('123'),'nama'=>'Yoga Pratama','alamat'=>'Jl. Bhakti No. 33','kontak'=>'087832600033'],['username'=>'087832600034','password'=>md5('123'),'nama'=>'Rani Kusuma','alamat'=>'Jl. Pahlawan No. 34','kontak'=>'087832600034'],['username'=>'087832600035','password'=>md5('123'),'nama'=>'Ferdiansyah','alamat'=>'Jl. Veteran No. 35','kontak'=>'087832600035'],['username'=>'087832600036','password'=>md5('123'),'nama'=>'Mega Utami','alamat'=>'Jl. Kartini No. 36','kontak'=>'087832600036'],['username'=>'087832600037','password'=>md5('123'),'nama'=>'Asep Supriatna','alamat'=>'Jl. Sudirman No. 37','kontak'=>'087832600037'],['username'=>'087832600038','password'=>md5('123'),'nama'=>'Nadia Zahra','alamat'=>'Jl. Ahmad Yani No. 38','kontak'=>'087832600038'],['username'=>'087832600039','password'=>md5('123'),'nama'=>'Ridwan Hakim','alamat'=>'Jl. Diponegoro No. 39','kontak'=>'087832600039'],['username'=>'087832600040','password'=>md5('123'),'nama'=>'Tiara Maharani','alamat'=>'Jl. Gatot Subroto No. 40','kontak'=>'087832600040'],['username'=>'087832600041','password'=>md5('123'),'nama'=>'Rifki Alamsyah','alamat'=>'Jl. Hasanudin No. 41','kontak'=>'087832600041'],['username'=>'087832600042','password'=>md5('123'),'nama'=>'Novi Anggraini','alamat'=>'Jl. Sawo No. 42','kontak'=>'087832600042'],['username'=>'087832600043','password'=>md5('123'),'nama'=>'Fauzan Akbar','alamat'=>'Jl. Manggis No. 43','kontak'=>'087832600043'],['username'=>'087832600044','password'=>md5('123'),'nama'=>'Lestari Wulandari','alamat'=>'Jl. Belimbing No. 44','kontak'=>'087832600044'],['username'=>'087832600045','password'=>md5('123'),'nama'=>'Dian Prameswari','alamat'=>'Jl. Kemuning No. 45','kontak'=>'087832600045'],['username'=>'087832600046','password'=>md5('123'),'nama'=>'Haris Ramadhan','alamat'=>'Jl. Puspa No. 46','kontak'=>'087832600046'],['username'=>'087832600047','password'=>md5('123'),'nama'=>'Anisa Putri','alamat'=>'Jl. Anyelir No. 47','kontak'=>'087832600047'],['username'=>'087832600048','password'=>md5('123'),'nama'=>'Bagus Saputro','alamat'=>'Jl. Bougenville No. 48','kontak'=>'087832600048'],['username'=>'087832600049','password'=>md5('123'),'nama'=>'Shinta Dewi','alamat'=>'Jl. Tulip No. 49','kontak'=>'087832600049'],['username'=>'087832600050','password'=>md5('123'),'nama'=>'Iqbal Ramadhan','alamat'=>'Jl. Lavender No. 50','kontak'=>'087832600050']];
            foreach ($dummyWargas as $dummyWarga) {
                $dummyWarga['rtrw'] = $rtrwids[rand(0, count($rtrwids) - 1)];
                $this->Wargas->create($dummyWarga);
            }

            $this->ProdukTukars->create([
                'nama' => 'Gas LPG 3KG',
                'kategori' => 'Gas Subsidi',
                'harga' => 16000,
                'stok' => 22,
                'terjual' => 10
            ]);

            $this->ProdukTukars->create([
                'nama' => 'Gas Bright 12KG',
                'kategori' => 'Gas Non Subsidi',
                'harga' => 125000,
                'stok' => 11,
                'terjual' => 6
            ]);
        }
    }

    public function down() {}
}
