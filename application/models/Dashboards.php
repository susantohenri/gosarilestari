<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Dashboards extends MY_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->table = '';
        $this->form = [];
    }

    public function dt()
    {
        return $this->datatables
            ->select('u.nama')
            ->select("CONCAT('Rp ', FORMAT(IF(0 <= u.saldo, 0, u.saldo * -1), 0, 'id_ID'))", false)
			->select("CONCAT(FORMAT(ss.berat, 1), ' KG')", false)
            ->from('user u')
            ->join("
                (
                    SELECT warga, SUM(berat) AS berat
                    FROM setorsampah
                    WHERE kategori = 'merah'
                    AND YEAR(createdAt) = YEAR(NOW())
                    AND MONTH(createdAt) = MONTH(NOW())
                    GROUP BY warga
                ) ss
            ", 'u.uuid = ss.warga', 'left')
            ->where('u.saldo < 0')
            ->or_where('ss.warga IS NOT NULL')
            ->generate();
    }
}
