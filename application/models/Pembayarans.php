<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Pembayarans extends MY_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->table = 'pembayaran';
        $this->thead = [
            (object) ['mData' => 'orders', 'sTitle' => 'No', 'visible' => false],
            (object) ['mData' => 'kode', 'sTitle' => 'KODE'],
            (object) ['mData' => 'fwaktu', 'sTitle' => 'TANGGAL'],
            (object) ['mData' => 'fwarga', 'sTitle' => 'WARGA'],
            (object) ['mData' => 'fpetugas', 'sTitle' => 'PETUGAS'],
            (object) ['mData' => 'fnominal', 'sTitle' => 'NOMINAL'],
            (object) ['mData' => 'catatan', 'sTitle' => 'CATATAN'],
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
                'name' => 'nominal',
                'label' => 'Nominal',
                'width' => 2,
                'attributes' => array(
                    array('data-number' => 'true')
                )
            ),
            array(
                'name' => 'catatan',
                'label' => 'Catatan'
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
            $this->db->where('warga.uuid', $this->session->userdata('uuid'));
        }

        if ($customFilter = $this->input->post('customFilter')) {
            parse_str($customFilter, $params);
            if ('' !== $params['kode']) {
                $this->db->like("{$this->table}.kode", $params['kode']);
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
            ->select("petugas.nama AS fpetugas", false)
            ->select("CONCAT('Rp ', FORMAT({$this->table}.nominal, 0, 'id_ID')) as fnominal", false)
            ->select("{$this->table}.catatan")
            ->select("{$this->table}.status")
            ->join('user AS warga', "warga.uuid = {$this->table}.warga", 'left')
            ->join('user AS petugas', "petugas.uuid = {$this->table}.petugas", 'left')
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

    function create($record)
    {
        if ('APPROVED' === $record['status']) {
            $uuid = parent::create($record);
            $created = $this->findOne($uuid);
            return $this->approve($created);
        } else return parent::create($record);
    }

    function update($next)
    {
        $prev = $this->findOne($next['uuid']);
        if ('APPROVED' === $prev['status']) return ['error' => 'Update tidak diizinkan'];
        if ('APPROVED' === $next['status']) {
            return $this->approve(array_merge($prev, $next));
        }
        return parent::update($next);
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
                    break;
            }
            return $field;
        }, $form);
        return $form;
    }

    protected function approve($pembayaran)
    {
        $pembayaran['petugas'] = $this->session->userdata('uuid');
        $pembayaran['approvedAt'] = date('Y-m-d H:i:s');
        $uuid = parent::update($pembayaran);

        $nominal = number_format($pembayaran['nominal'], 0, ',', '.');
        $this->load->model('Ledgers');
        $this->Ledgers->save([
            'kode' => $pembayaran['kode'],
            'transaksi' => $pembayaran['uuid'],
            'warga' => $pembayaran['warga'],
            'petugas' => $pembayaran['petugas'],
            'tipe' => 'SETOR_TUNAI',
            'keterangan' => "Setor tunai senilai Rp {$nominal}",
            'nilai' => $pembayaran['nominal']
        ]);

        return $uuid;
    }
}
