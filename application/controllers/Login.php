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

    public function Migrate($version = null)
    {
        if ('development' !== ENVIRONMENT) show_404();
        $this->load->library('migration');
        $success = !is_null($version) ? $this->migration->version($version) : $this->migration->latest();
        if (!$success) {
            show_error($this->migration->error_string());
        }
    }

    public function Dummy()
    {
        if ('development' !== ENVIRONMENT) show_404();

        $this->load->model(['Users', 'Creators', 'Contents']);

        $creators = ['Henri', 'Susanto'];
        foreach ($creators as $name) {
            $userId = $this->Users->create(['username' => "user{$name}", 'password' => md5('123')]);
            $creatorId = $this->Creators->create([
                'name' => "Creator {$name}",
                'user' => $userId
            ]);
            $this->Contents->create([
                'title' => "Konten {$name}",
                'creator' => $creatorId
            ]);
        }
    }

    public function Logout()
    {
        $this->load->library('session');
        $this->session->sess_destroy();
        redirect(base_url());
    }
}
