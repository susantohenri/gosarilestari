<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Permissions extends MY_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->table = 'permission';
        $this->thead = [];
        $this->form = [
            [
                'name' => 'entity',
                'label' => 'Entity',
                'options' => [
                    ['text' => 'User', 'value' => 'User'],
                    ['text' => 'Role', 'value' => 'Role'],
                    ['text' => 'Permission', 'value' => 'Permission'],
                    array('text' => 'Warga', 'value' => 'Warga'),
                    array('text' => 'Rtrw', 'value' => 'Rtrw'),
                    array('text' => 'KategoriSampah', 'value' => 'KategoriSampah'),
                    array('text' => 'ProdukTukar', 'value' => 'ProdukTukar'),
                    array('text' => 'TransaksiGlobal', 'value' => 'TransaksiGlobal'),
                    array('text' => 'TransaksiSampah', 'value' => 'TransaksiSampah'),
                    array('text' => 'HasilPemilahan', 'value' => 'HasilPemilahan'),
                    array('text' => 'TransaksiIuran', 'value' => 'TransaksiIuran'),
                    array('text' => 'TransaksiPembelian', 'value' => 'TransaksiPembelian'),
                    array('text' => 'Konfigurasi', 'value' => 'Konfigurasi'),
                ],
                'width' => 4
            ],
            [
                'name' => 'action',
                'label' => 'Action',
                'options' => [
                    ['text' => 'List', 'value' => 'index'],
                    ['text' => 'Create', 'value' => 'create'],
                    ['text' => 'Detail', 'value' => 'read'],
                    ['text' => 'Update', 'value' => 'update'],
                    ['text' => 'Delete', 'value' => 'delete']
                ],
                'width' => 4
            ],
        ];
    }

    public function getPermissions()
    {
        $permission = [];
        foreach ($this->find(['role' => $this->session->userdata('role')]) as $perm) {
            $permission[] = "{$perm->action}_{$perm->entity}";
        }
        return $permission;
    }

    public function getPermittedMenus() {}
}
