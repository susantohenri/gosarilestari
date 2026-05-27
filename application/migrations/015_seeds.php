<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Migration_seeds extends CI_Migration
{
    public function up()
    {
        $this->load->model(['Users', 'Roles', 'Permissions', 'Menus']);
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

        if ('development' === ENVIRONMENT) {
            $this->Users->create([
                'username' => 'Petugas',
                'password' => md5('123'),
                'role' => $petugas
            ]);

            $this->load->model([
                'Wargas',
                'Rtrws',
                'KategoriSampahs',
            ]);

            $rtrw = $this->Rtrws->create([
                'rt' => '006',
                'rw' => '004'
            ]);

            $this->Wargas->create([
                'nama' => 'Henri Susanto',
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
        }
    }

    public function down() {}
}
