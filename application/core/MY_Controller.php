<?php

defined('BASEPATH') or exit('No direct script access allowed');

class MY_Controller extends CI_Controller
{
    public $controller;
    public $model;
    public $page_title;
    public $page_subtitle;
    public $header_buttons;

    public function __construct()
    {
        parent::__construct();
        $this->load->helper('url');
        $this->load->library('session');
        if (empty($this->session->userdata['uuid'])) {
            redirect(site_url('Login'), 'refresh');
        }
        $this->controller = $this->router->class;

        if (!isset($this->model)) {
            $this->model = $this->controller . 's';
        }
        $this->load->model($this->model);
        $this->header_buttons = 'header-buttons';
    }

    public function loadview($view, $vars = [])
    {
        $vars['error'] = $this->session->flashdata('model_error');
        $vars['account_type'] = $this->session->userdata('role');

        $page_title = preg_split('#([A-Z][^A-Z]*)#', $this->controller, -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);
        $page_title = implode(' ', $page_title);
        $vars['page_title'] = isset($this->page_title) ? $this->page_title : $page_title;
        $vars['page_subtitle'] = isset($this->page_subtitle) ? $this->page_subtitle : '';

        if (!isset($vars['form_action'])) {
            $vars['form_action'] = site_url($this->controller);
        }
        $vars['current'] = [
            'model' => $this->model,
            'controller' => $this->controller
        ];

        $this->load->model(['Permissions', 'Notifikasis', 'Konfigurasis']);
        if (!isset($vars['permission'])) {
            $vars['permission'] = $this->Permissions->getPermissions();
        }
        $vars['unread'] = $this->Notifikasis->getUnreadCountByUserId($this->session->userdata('uuid'));
        $vars['sampah_terkumpul'] = $this->Konfigurasis->getSampahTerkumpul();
        $vars['header_buttons'] = $this->header_buttons;

        $this->load->view($view, $vars);
    }

    public function index()
    {
        $model = $this->model;
        if ($post = $this->$model->lastSubmit($this->input->post())) {
            if (isset($post['delete'])) {
                $this->$model->delete($post['delete']);
            } else {
                $db_debug = $this->db->db_debug;
                $this->db->db_debug = false;

                $result = $this->$model->save($post);

                $error = $this->db->error();
                $this->db->db_debug = $db_debug;
                if (isset($result['error'])) {
                    $error = $result['error'];
                }
                if (count($error)) {
                    $this->session->set_flashdata('model_error', $error['message']);
                    redirect($this->controller);
                }
            }
        }
        $vars = [];
        $vars['page_name'] = 'table';
        $vars['js'] = [
            'jquery.dataTables.min.js',
            'table.js'
        ];
        $vars['thead'] = $this->$model->thead;
        $vars['overview'] = $this->$model->getOverView();
        $this->loadview('index', $vars);
    }

    public function create()
    {
        $model = $this->model;
        $vars = [];
        $vars['page_name'] = 'form';
        $vars['form']     = $this->$model->getForm();
        $vars['uuid'] = '';
        $vars['js'] = [
            'select2.full.min.js',
            'form.js'
        ];
        $this->loadview('index', $vars);
    }

    public function read($id)
    {
        $vars = [];
        $vars['page_name'] = 'form';
        $model = $this->model;
        $vars['form'] = $this->$model->getForm($id);
        $vars['uuid'] = $id;
        $vars['js'] = [
            'select2.full.min.js',
            'form.js'
        ];
        $this->loadview('index', $vars);
    }

    public function delete($uuid)
    {
        $vars = [];
        $vars['page_name'] = 'confirm';
        $vars['uuid'] = $uuid;
        $this->loadview('index', $vars);
    }

    public function select2($model, $field)
    {
        $this->load->model($model);
        echo '{"results":' . json_encode($this->$model->select2($field, $this->input->post('term'))) . '}';
    }

    public function dt()
    {
        echo $this->{$this->model}->dt();
    }
}
