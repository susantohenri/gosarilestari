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
            'ProdukTukars'
        ]);
        $fas = ['database', 'desktop', 'download', 'ethernet', 'hdd', 'hdd', 'headphones', 'keyboard', 'keyboard', 'laptop', 'memory', 'microchip', 'mobile', 'mobile-alt', 'plug', 'power-off', 'print', 'satellite', 'satellite-dish', 'save', 'save', 'sd-card', 'server', 'sim-card', 'stream', 'tablet', 'tablet-alt', 'tv', 'upload'];

        $admin = $this->Roles->create(['name' => 'Admin']);
        $petugas = $this->Roles->create(['name' => 'Petugas']);

        $baseEntities = ['User', 'Role', 'Permission', 'Menu'];
        $appEntities = ['Warga', 'Rtrw', 'KategoriSampah', 'ProdukTukar', 'TransaksiGlobal', 'TransaksiSampah', 'HasilPemilahan', 'TransaksiIuran', 'TransaksiPembelian', 'Konfigurasi'];
        $allPermissions = ['index', 'create', 'read', 'update', 'delete'];

        foreach (array_merge($baseEntities, $appEntities) as $entity) {
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

        foreach ($appEntities as $entity) {
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

        $this->Users->create([
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

        if ('development' === ENVIRONMENT) {
            $this->Users->create([
                'username' => 'Petugas Jaga',
                'password' => md5('123'),
                'role' => $petugas
            ]);

            $rtrw = $this->Rtrws->create([
                'rt' => '006',
                'rw' => '004'
            ]);

            $this->Wargas->create([
                'nama' => 'Hj. Warga Teladan',
                'rtrw' => $rtrw
            ]);

            $this->KategoriSampahs->create([
                'nama' => 'Plastik',
                'contoh' => 'ember, galon, botol',
                'harga' => 2500
            ]);

            $this->KategoriSampahs->create([
                'nama' => 'Kertas',
                'contoh' => 'koran, buku, tissue',
                'harga' => 1500
            ]);

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
