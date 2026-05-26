<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Dashboard extends MY_Controller
{
    public function __construct()
    {
        $this->page_title = '';
        parent::__construct();
    }

    public function index()
    {
        $vars = [];
        $this->load->model('Menus');
        $vars['menu'] = $this->Menus->find(['role' => $this->session->userdata('role')]);
        $vars['page_name'] = 'dashboard';
        $this->loadview('index', $vars);
    }
}
