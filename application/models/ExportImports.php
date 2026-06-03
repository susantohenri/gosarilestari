<?php

defined('BASEPATH') or exit('No direct script access allowed');

class ExportImports extends MY_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->table = '';
        $this->form = [];
    }

    public function belumPilah()
    {
        return $this
            ->db
            ->select("COUNT(warga.uuid) jumlahKK", false)
            ->select("rtrw.nama as wilayah", false)
            ->select("SUM(CASE WHEN setorsampah.kategori = 'merah' THEN 1 ELSE 0 END) as belum_pilah", false)
            ->where('role.name', 'Warga')
            ->join('user as warga', 'warga.uuid = setorsampah.warga')
            ->join('role', 'role.uuid = warga.role')
            ->join('rtrw', 'rtrw.uuid = warga.rtrw', 'right')
            ->from('setorsampah')
            ->group_by('rtrw.uuid')
            ->get()
            ->result();
    }

    public function sampahTerkelola()
    {
        return $this
            ->db
            ->select("rtrw.nama as wilayah", false)
            ->select("SUM(setorsampah.berat) as berat", false)
            ->where('role.name', 'Warga')
            ->join('user as warga', 'warga.uuid = setorsampah.warga')
            ->join('role', 'role.uuid = warga.role')
            ->join('rtrw', 'rtrw.uuid = warga.rtrw', 'right')
            ->from('setorsampah')
            ->group_by('rtrw.uuid')
            ->get()
            ->result();
    }

    public function byKategoriSampah()
    {
        return $this
            ->db
            ->select("SUM(setorsampah.berat) as berat", false)
            ->select("kategorisampah.nama as kategori", false)
            ->join('kategorisampah', 'kategorisampah.uuid = setorsampah.kategorisampah')
            ->group_by('setorsampah.kategorisampah')
            ->get('setorsampah')
            ->result();
    }

    public function penghasilanWarga()
    {
        return $this
            ->db
            ->select("warga.nama")
            ->select("SUM(setorsampah.pendapatan) as penghasilan", false)
            ->where('role.name', 'Warga')
            ->join('user as warga', 'warga.uuid = setorsampah.warga')
            ->join('role', 'role.uuid = warga.role')
            ->group_by('warga.uuid')
            ->having("SUM(setorsampah.pendapatan) >", 0, false)
            ->get('setorsampah')
            ->result();
    }

    public function beratKategoriSampah()
    {
        return $this
            ->db
            ->select("kategorisampah.nama")
            ->select("SUM(setorsampah.berat) as berat", false)
            ->where("setorsampah.warga", $this->session->userdata('uuid'))
            ->join("kategorisampah", "kategorisampah.uuid = setorsampah.kategorisampah")
            ->group_by("kategorisampah.uuid")
            ->get("setorsampah")
            ->result();
    }

    public function pendapatanKategoriSampah()
    {
        return $this
            ->db
            ->select("kategorisampah.nama")
            ->select("SUM(setorsampah.pendapatan) as pendapatan", false)
            ->where("setorsampah.warga", $this->session->userdata('uuid'))
            ->join("kategorisampah", "kategorisampah.uuid = setorsampah.kategorisampah")
            ->group_by("kategorisampah.uuid")
            ->get("setorsampah")
            ->result();
    }

    public function iuranPerBulan () {
        return $this
            ->db
            ->select("CONCAT(bulan, ' ', tahun) as bulan", false)
            ->select("nominal")
            ->where("setortunai.warga", $this->session->userdata('uuid'))
            ->get('setortunai')
            ->result();
    }
}
