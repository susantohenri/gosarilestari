<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Penukarans extends MY_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->table = 'penukaran';
        $this->thead = [
            (object) ['mData' => 'orders', 'sTitle' => 'No', 'visible' => false],
            (object) ['mData' => 'kode', 'sTitle' => 'KODE'],
            (object) ['mData' => 'fwaktu', 'sTitle' => 'TANGGAL'],
            (object) ['mData' => 'fwarga', 'sTitle' => 'WARGA'],
            (object) ['mData' => 'fkasir', 'sTitle' => 'KASIR'],
            (object) ['mData' => 'fproduk', 'sTitle' => 'PRODUK'],
            (object) ['mData' => 'fqty', 'sTitle' => 'QTY'],
            (object) ['mData' => 'ftotal', 'sTitle' => 'TOTAL'],
            (object) ['mData' => 'status', 'sTitle' => 'STATUS'],
            (object) ['mData' => 'aksi', 'sTitle' => 'AKSI'],
        ];

        $this->form = [
            array(
                'name' => 'warga',
                'label' => 'Warga',
                'options' => array(),
                'width' => 2,
                'attributes' => array(
                    array('data-autocomplete' => 'true'),
                    array('data-model' => 'Wargas'),
                    array('data-field' => 'nama'),
                    array('required' => true),
                )
            ),
            array(
                'name' => 'produktukar',
                'label' => 'Produk',
                'options' => array(),
                'width' => 2,
                'attributes' => array(
                    array('data-autocomplete' => 'true'),
                    array('data-model' => 'ProdukTukars'),
                    array('data-field' => 'nama'),
                    array('required' => true),
                )
            ),
            array(
                'name' => 'qty',
                'label' => 'Quantity',
                'width' => 2,
                'attributes' => array(
                    array('data-number' => 'true')
                )
            ),
            array(
                'name' => 'status',
                'label' => 'Status',
                'options' => [
                    ['text' => 'PENDING', 'value' => 'PENDING'],
                    ['text' => 'APPROVED', 'value' => 'APPROVED'],
                ]
            )
        ];
    }

    public function dt()
    {
        if ('Warga' === $this->session->userdata('role_name')) {
            $this->datatables->where('warga.uuid', $this->session->userdata('uuid'));
        }

        if ($customFilter = $this->input->post('customFilter')) {
            parse_str($customFilter, $params);
            if ('' !== $params['kode']) {
                $this->datatables->like("{$this->table}.kode", $params['kode']);
            }
        }

        $this->db->order_by("{$this->table}.createdAt", 'DESC');
        $this
            ->datatables
            ->select("{$this->table}.uuid")
            ->select("{$this->table}.orders")
            ->select("{$this->table}.kode")
            ->select("DATE_FORMAT({$this->table}.createdAt, '%d %b %Y %H:%i') AS fwaktu", false)
            ->select("warga.nama AS fwarga", false)
            ->select("kasir.nama AS fkasir", false)
            ->select("produk.nama AS fproduk", false)
            ->select("FORMAT({$this->table}.qty, 0) as fqty", false)
            ->select("CONCAT('Rp ', FORMAT({$this->table}.total, 0, 'id_ID')) as ftotal", false)
            ->select("{$this->table}.status")
            ->join('user AS warga', "warga.uuid = {$this->table}.warga", 'left')
            ->join('user AS kasir', "kasir.uuid = {$this->table}.kasir", 'left')
            ->join('produktukar AS produk', "produk.uuid = {$this->table}.produktukar", 'left')
        ;

        $controller = $this->router->class;
        $edit = site_url("{$controller}/Read/");
        $delete = site_url("{$controller}/Delete/");

        $this
            ->db
            ->select("if('PENDING' = {$this->table}.status,
                CONCAT(
                    '<div class=\"flex flex-wrap gap-2\">',
                    '<a class=\"px-2 py-1 text-xs text-white bg-yellow-500 rounded hover:bg-yellow-600\" href=\"{$edit}', {$this->table}.uuid, '\"><i class=\"fa fa-file-lines\"></i></a>'
                    '<a class=\"px-2 py-1 text-xs text-white bg-red-500 rounded hover:bg-red-600\" href=\"{$delete}', {$this->table}.uuid, '\"><i class=\"fa fa-trash\"></i></a>',
                    '</div>'
                ),
                CONCAT(
                    '<div class=\"flex flex-wrap gap-2\">',
                    '<a class=\"px-2 py-1 text-xs text-white bg-yellow-500 rounded hover:bg-yellow-600\" href=\"{$edit}', {$this->table}.uuid, '\"><i class=\"fa fa-file-lines\"></i></a>'
                    '</div>'
                )
            ) as aksi", false);

        return $this
            ->datatables
            ->from($this->table)
            ->where("{$this->table}.deletedAt", null)
            ->generate();
    }

    public function getForm($uuid = false, $isSubform = false)
    {
        $isWarga = 'Warga' === $this->session->userdata('role_name');
        $form = parent::getForm($uuid, $isSubform);
        $form = array_map(function ($field) use ($isWarga, $uuid) {
            switch ($field['name']) {
                case 'warga':
                    // warga can only create & update belongs to his own
                    if ($isWarga) {
                        $field['options'] = [
                            [
                                'text' => $this->session->userdata('nama'),
                                'value' => $this->session->userdata('uuid')
                            ]
                        ];
                        $field['attr'] = '';
                    }
                    break;
                case 'produktukar':
                    // on create penukaran from thumbnails: set default produk
                    if (!$uuid && $produktukar = $this->input->get('produktukar')) {
                        $this->load->model('ProdukTukars');
                        $produk = $this->ProdukTukars->findOne($produktukar);
                        $field['options'] = [
                            [
                                'text' => $produk['nama'],
                                'value' => $produk['uuid'],
                            ]
                        ];
                    }
                    break;
                case 'status':
                    // warga not allowed to change status
                    if ($isWarga) {
                        if (!$uuid) {
                            $field['options'] = [['text' => 'PENDING', 'value' => 'PENDING']];
                        } else {
                            $field['options'] = array_filter($field['options'], function ($option) use ($field) {
                                return $option['value'] === $field['value'];
                            });
                        }
                    }
                    if (isset($field['value']) && 'APPROVED' === $field['value']) {
                        $field['options'] = [['text' => 'APPROVED', 'value' => 'APPROVED']];
                    }
                    // kasir or admin create penukaran, first option: approved
                    if (!$isWarga && !$uuid) {
                        $field['options'] = [
                            ['text' => 'APPROVED', 'value' => 'APPROVED'],
                            ['text' => 'PENDING', 'value' => 'PENDING']
                        ];
                    }
                    break;
            }
            return $field;
        }, $form);
        return $form;
    }

    function create($record)
    {
        $this->load->model('ProdukTukars');
        $produk = $this->ProdukTukars->findOne($record['produktukar']);

        $record = $this->calculate($record, $produk);

        if ('APPROVED' === $record['status']) {
            $validation = $this->validateApproval($record, $produk);
            if (!!$validation['error']) return $validation;
            $uuid = parent::create($record);
            $created = $this->findOne($uuid);
            return $this->approve($created, $produk);
        } else return parent::create($record);
    }

    function update($next)
    {
        $this->load->model('ProdukTukars');
        $produk = $this->ProdukTukars->findOne($next['produktukar']);
        $next = $this->calculate($next, $produk);

        $prev = $this->findOne($next['uuid']);
        if ('APPROVED' === $prev['status']) return ['error' => 'Update tidak diizinkan'];
        if ('APPROVED' === $next['status']) {
            $validation = $this->validateApproval($next, $produk);
            if (!!$validation['error']) return $validation;
            return $this->approve(array_merge($prev, $next), $produk);
        }
        return parent::update($next);
    }

    protected function calculate($penukaran, $produk)
    {
        $penukaran['harga'] = $produk['harga'];
        $penukaran['total'] = $penukaran['qty'] * $produk['harga'];
        return $penukaran;
    }

    protected function validateApproval($penukaran, $produk)
    {
        $this->load->model(['Wargas', 'Ledgers']);
        if (!$produk) return ['error' => 'Produk tidak ditemukan'];
        if ($produk['stok'] < $penukaran['qty']) return ['error' => 'Stok tidak cukup'];

        $warga = $this->Wargas->findOne($penukaran['warga']);
        if (!$warga) return ['error' => 'Warga tidak ditemukan'];

        if ($penukaran['total'] > $warga['saldo']) return ['error' => 'Saldo tidak cukup'];
        return ['error' => false];
    }

    protected function approve($penukaran, $produk)
    {
        $penukaran['kasir'] = $this->session->userdata('uuid');
        $penukaran['approvedAt'] = date('Y-m-d H:i:s');
        $uuid = parent::update($penukaran);

        $this->ProdukTukars->sold($penukaran['produktukar'], $penukaran['qty']);
        $this->Ledgers->save([
            'kode' => $penukaran['kode'],
            'transaksi' => $penukaran['uuid'],
            'warga' => $penukaran['warga'],
            'petugas' => $penukaran['kasir'],
            'tipe' => 'TUKAR_PRODUK',
            'keterangan' => "Penukaran produk {$produk['nama']}",
            'nilai' => $penukaran['total'] * -1
        ]);

        return $uuid;
    }
}
