<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Login extends CI_Controller
{
    public function index()
    {
        if ($post = $this->input->post()) {
            $this->load->model('Users');
            $login = $this->Users->findOne([
                'username' => $post['username'],
                'password' => md5($post['password'])
            ]);
            if (isset($login['uuid'])) {
                $this->load->library('session');
                $this->session->set_userdata($login);
                redirect(base_url());
            }
        }
        $this->load->view('login');
    }

    public function Logout()
    {
        $this->load->library('session');
        $this->session->sess_destroy();
        redirect(base_url());
    }
}
