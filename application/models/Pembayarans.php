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
            (object) ['mData' => 'fagen', 'sTitle' => 'AGEN'],
            (object) ['mData' => 'fkasir', 'sTitle' => 'KASIR'],
            (object) ['mData' => 'fnominal', 'sTitle' => 'NOMINAL'],
            (object) ['mData' => 'catatan', 'sTitle' => 'CATATAN'],
            (object) ['mData' => 'status', 'sTitle' => 'POSISI'],
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
                    ['text' => 'WARGA', 'value' => 'WARGA'],
                    ['text' => 'AGEN', 'value' => 'AGEN'],
                    ['text' => 'KASIR', 'value' => 'KASIR'],
                ]
            )
        ];
    }

    public function dt()
    {
        switch ($this->session->userdata('role_name')) {
            case 'Warga':
                $this->datatables->where('warga.uuid', $this->session->userdata('uuid'));
                break;
            case 'Agen':
                $this->datatables->where('warga.agen', $this->session->userdata('uuid'));
                break;
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
            ->select("agen.nama AS fagen", false)
            ->select("kasir.nama AS fkasir", false)
            ->select("CONCAT('Rp ', FORMAT({$this->table}.nominal, 0, 'id_ID')) as fnominal", false)
            ->select("{$this->table}.catatan")
            ->select("{$this->table}.status")
            ->join('user AS warga', "warga.uuid = {$this->table}.warga", 'left')
            ->join('user AS agen', "agen.uuid = {$this->table}.agen", 'left')
            ->join('user AS kasir', "kasir.uuid = {$this->table}.kasir", 'left')
        ;

        $controller = $this->router->class;
        $edit = site_url("{$controller}/Read/");
        $delete = site_url("{$controller}/Delete/");

        $this
            ->db
            ->select("CONCAT(
                '<div class=\"flex flex-wrap gap-2\">',
                '<a class=\"px-2 py-1 text-xs text-white bg-yellow-500 rounded hover:bg-yellow-600\" href=\"{$edit}', {$this->table}.uuid, '\"><i class=\"fa fa-file-lines\"></i></a>'
                '</div>'
            ) as aksi", false);

        return $this
            ->datatables
            ->from($this->table)
            ->where("{$this->table}.deletedAt", null)
            ->generate();
    }

    function save($record)
    {
        if ('AGEN' === $record['status'] && 'Agen' === $this->session->userdata('role_name')) {
            $record['agen'] = $this->session->userdata('uuid');
        }
        return parent::save($record);
    }

    function create($record)
    {
        if ('KASIR' === $record['status']) {
            $uuid = parent::create($record);
            $created = $this->findOne($uuid);
            $this->approve($created);
        } else {
            $created = parent::create($record);
        }

        if ('WARGA' === $record['status']) {
            $pymt = $this->findOne($created);
            $this->load->model('Notifikasis');
            $this->Notifikasis->pembayaranBaru($pymt);
        }

        return $created;
    }

    function update($next)
    {
        $prev = $this->findOne($next['uuid']);
        if ('KASIR' === $prev['status']) return ['error' => 'Update tidak diizinkan'];

        if ('AGEN' === $prev['status']) {
            switch ($next['status']) {
                case 'WARGA':
                    return ['error' => 'Update tidak diizinkan'];
                case 'AGEN':
                    break;
                case 'KASIR':
                    break;
            }
        }

        if ('KASIR' === $next['status']) {
            return $this->approve(array_merge($prev, $next));
        }
        return parent::update($next);
    }

    /*
        skenario pembayaran
        1. warga bayar tunai ke kasir
            - kasir buat pembayaran baru, statusnya KASIR
        2. warga bayar transfer ke kasir
            - warga buat pembayaran baru, statusnya WARGA
            - kasir cek mutasi,
                - jika sesuai, update status ke KASIR
                - jika tidak sesuai, minta warga edit/hapus
        3. warga bayar tunai ke agen
            - agen buat pembayaran baru, statusnya AGEN
            - warga bisa validasi melalui akunnya
                - jika tidak sesuai, warga bisa menghapus & meminta agen input ulang
            - agen setor ke kasir
                - kasir hitung uang tunai
                    - jika sesuai, update status ke KASIR
                    - jika tidak, status tetap di AGEN
        4. warga bayar transfer ke agen
            - warga buat pembayaran baru, statusnya WARGA
            - agen cek mutasi
                - jika sesuai, agen update status ke AGEN
                - jika tidak sesuai, agen minta warga edit/hapus
            - agen setor ke kasir
                - kasir hitung uang tunai
                    - jika sesuai, update status ke KASIR
                    - jika tidak, status tetap di AGEN
    */
    public function getForm($uuid = false, $isSubform = false)
    {
        $roleName = $this->session->userdata('role_name');
        // $current = !!$uuid ? $this->findOne($uuid) : ['status' => null];

        $form = parent::getForm($uuid, $isSubform);
        $form = array_map(function ($field) use ($roleName, $uuid) {
            switch ($field['name']) {
                case 'warga':
                    switch ($roleName) {
                        case 'Warga':
                            // warga can only create & update belongs to his own
                            $field['options'] = [
                                [
                                    'text' => $this->session->userdata('nama'),
                                    'value' => $this->session->userdata('uuid')
                                ]
                            ];
                            $field['attr'] = '';
                            break;
                        case 'Agen':
                        case 'Kasir':
                            // agen & kasir aren't allowed to change warga
                            if (!!$uuid) {
                                $field['attr'] .= ' disabled="disabled"';
                            }
                            break;
                        case 'Admin':
                            break;
                    }
                    break;
                case 'status':
                    switch ($roleName) {
                        case 'Warga':
                            if (!$uuid) {
                                // warga can only create WARGA payment
                                $field['options'] = [['text' => 'WARGA', 'value' => 'WARGA']];
                            }
                            break;
                        case 'Agen':
                            if (!$uuid) {
                                // agen can only create AGEN payment
                                $field['options'] = [['text' => 'AGEN', 'value' => 'AGEN']];
                            } else {
                                // agen only allowed to change status from current status to AGEN
                                $field['options'] = array_filter($field['options'], function ($option) use ($field) {
                                    return $option['value'] === $field['value'];
                                });
                                if ('AGEN' !== $field['value']) $field['options'][] = ['text' => 'AGEN', 'value' => 'AGEN'];
                            }
                            break;
                        case 'Kasir':
                            if (!$uuid) {
                                // kasir can only create KASIR payment
                                $field['options'] = [['text' => 'KASIR', 'value' => 'KASIR']];
                            } else {
                                // kasir only allowed to change status from current status to KASIR
                                $field['options'] = array_filter($field['options'], function ($option) use ($field) {
                                    return $option['value'] === $field['value'];
                                });
                                if ('KASIR' !== $field['value']) $field['options'][] = ['text' => 'KASIR', 'value' => 'KASIR'];
                            }
                            break;
                        case 'Admin':
                            break;
                    }
                    if ('Warga' === $roleName) {
                    }
                    break;
                case 'nominal':
                case 'catatan':
                    switch ($roleName) {
                        case 'Warga':
                            break;
                        case 'Agen':
                        case 'Kasir':
                            // agen & kasir aren't allowed to change nominal & catatan
                            if (!!$uuid) {
                                $field['attr'] .= ' readonly="true"';
                            }
                            break;
                        case 'Admin':
                            break;
                    }

                    break;
            }
            return $field;
        }, $form);
        return $form;
    }

    protected function approve($pembayaran)
    {
        $pembayaran['kasir'] = $this->session->userdata('uuid');
        $pembayaran['approvedAt'] = date('Y-m-d H:i:s');
        $uuid = parent::update($pembayaran);

        $nominal = number_format($pembayaran['nominal'], 0, ',', '.');
        $this->load->model('Ledgers');
        $this->Ledgers->save([
            'kode' => $pembayaran['kode'],
            'transaksi' => $pembayaran['uuid'],
            'warga' => $pembayaran['warga'],
            'petugas' => $pembayaran['kasir'],
            'tipe' => 'SETOR_TUNAI',
            'keterangan' => "Setor tunai senilai Rp {$nominal}",
            'nilai' => $pembayaran['nominal']
        ]);

        return $uuid;
    }
}
