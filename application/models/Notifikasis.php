<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Notifikasis extends MY_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Konfigurasis');

        $this->table = 'notifikasi';

        $this->thead = [
            (object) ['mData' => 'orders', 'sTitle' => 'No', 'visible' => false],
            (object) ['mData' => 'kode', 'sTitle' => 'KODE'],
        ];

        $this->form = [
            [
                'name' => 'kode',
                'label' => 'Kode Notifikasi',
            ],
        ];
    }

    public function dt()
    {
        $this
            ->datatables
            ->select("{$this->table}.uuid")
            ->select("{$this->table}.orders")
            ->select("{$this->table}.kode");
        return parent::dt();
    }
}
