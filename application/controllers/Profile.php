<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Profile extends MY_Controller
{
    public function __construct()
    {
        $this->model = 'Users';
        parent::__construct();
    }

    public function index()
    {
        $id = $this->session->userdata('uuid');
        switch ($this->session->userdata('role_name')) {
            case 'Admin':
                $this->model = 'Users';
                break;
            case 'Petugas':
                $this->model = 'Petugass';
                break;
            case 'Warga':
                $this->model = 'Wargas';
                break;
        }
        $this->load->model($this->model);
        $model = $this->model;
        $vars = [];
        $vars['page_name'] = 'form';
        if ($post = $this->$model->lastSubmit($this->input->post())) {
            $result = $this->$model->save($post);
            if (isset($result['error'])) {
                $this->session->set_flashdata('model_error', $result['error']);
                redirect($this->controller);
            }
        }
        $vars['form'] = $this->$model->getForm($id);
        $vars['uuid'] = $id;
        $vars['js'] = [
            'select2.full.min.js',
            'form.js'
        ];

        $this->load->model('Permissions');
        $vars['permission'] = $this->Permissions->getPermissions();
        $vars['permission'][] = 'update_Profile';

        $this->loadview('index', $vars);
    }
}
