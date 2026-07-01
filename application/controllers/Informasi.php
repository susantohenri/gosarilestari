<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Informasi extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    function read($id)
    {
        if ('Warga' === $this->session->userdata('role_name')) {
            $vars = [
                'page_name' => 'informasi',
                'informasi' => $this->Informasis->findOne($id)
            ];
            $this->loadview('index', $vars);
        } else return parent::read($id);
    }
}
