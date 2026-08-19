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
        $this->db->order_by('saldo', 'desc');
        return $this->datatables
            ->select('u.nama')
            ->select("CONCAT('Rp ', FORMAT(saldo, 0, 'id_ID'))", false)
            ->from('user u')
            ->generate();
    }
}
