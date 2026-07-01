<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Informasis extends MY_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->table = 'informasi';
        $this->thead = [
            (object) ['mData' => 'orders', 'sTitle' => 'No', 'visible' => false],
            (object) ['mData' => 'ftanggal', 'sTitle' => 'tanggal'],
            (object) ['mData' => 'title', 'sTitle' => 'judul'],
            (object) ['mData' => 'aksi', 'sTitle' => 'baca'],
        ];
        $this->form  = [];

        $this->form[] = [
            'name' => 'title',
            'label' => 'Judul'
        ];

        $this->form[] = [
            'name' => 'content',
            'label' => 'Konten',
            'type' => 'textarea',
            'attributes' => [
                ['rows' => 23]
            ]
        ];
    }

    public function dt()
    {
        $this->db->order_by('createdAt', 'DESC');

        $controller = $this->router->class;
        $edit = site_url("{$controller}/Read/");
        $this
            ->db
            ->select("CONCAT(
                '<a class=\"mr-1 border p-1 rounded-sm\" href=\"{$edit}', {$this->table}.uuid, '\"><i class=\"fa fa-book-open text-yellow-500\"></i></a>'
            ) as aksi", false);

        $this->datatables
            ->select("{$this->table}.uuid")
            ->select("{$this->table}.orders")
            ->select("{$this->table}.title")
            ->select("DATE_FORMAT({$this->table}.createdAt, '%d %b %Y %H:%i') AS ftanggal", false);

        return $this
            ->datatables
            ->from($this->table)
            ->where("{$this->table}.deletedAt", null)
            ->generate();
    }

    function create($data)
    {
        $uuid = parent::create($data);
        $href = site_url("Informasi/Read/{$uuid}");
        $this->load->model(['Wargas', 'Notifikasis']);
        $wargas = $this->Wargas->find();
        foreach ($wargas as $warga) {
            $this->Notifikasis->create([
                'user' => $warga->uuid,
                'period' => strtoupper(base_convert(time() + rand(), 10, 36)),
                'judul' => "Informasi Baru: {$data['title']}",
                'informasi' => "Informasi terbaru, silakan klik link berikut: <u><a href='{$href}'>{$data['title']}</a></u>"
            ]);
        }
        return $uuid;
    }
}
