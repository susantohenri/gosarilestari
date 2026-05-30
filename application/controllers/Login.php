<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Login extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->library('session');
        $this->load->helper('url');
    }

    public function index()
    {
        if ($this->session->userdata('uuid')) {
            redirect(base_url());
        }

        $error = '';
        if ($post = $this->input->post()) {
            $this->load->model(['Users']);
            $login = $this->Users->findOne([
                'username' => $post['username'],
                'password' => md5($post['password'])
            ]);
            if (isset($login['uuid'])) {
                $this->load->model(['Notifikasis', 'Roles']);
                $role = $this->Roles->findOne(['uuid' => $login['role']]);
                $login['role_name'] = $role['name'];
                $this->session->set_userdata($login);
                $this->Notifikasis->updateUserdataUnreadNotification($login['uuid']);
                redirect(base_url());
            }
            $error = 'Username atau password salah.';
        }

        $this->load->view('login', ['error' => $error]);
    }

    public function register()
    {
        if ($this->session->userdata('uuid')) {
            redirect(base_url());
        }

        $this->load->model(['Wargas', 'Rtrws', 'Users']);
        $rtrws = $this->Rtrws->find();
        $error = '';
        $old = [
            'nama' => '',
            'alamat' => '',
            'rtrw' => '',
            'kontak' => '',
            'username' => '',
        ];

        if ($post = $this->input->post()) {
            $old = array_merge($old, array_intersect_key($post, $old));
            $error = $this->validateRegister($post);

            if ($error === '') {
                $this->Wargas->create([
                    'nama' => trim($post['nama']),
                    'alamat' => trim($post['alamat']),
                    'rtrw' => $post['rtrw'],
                    'kontak' => trim($post['kontak']),
                    'username' => trim($post['username']),
                    'password' => md5($post['password']),
                    'saldo' => 0,
                    'status' => 1,
                ]);
                $this->session->set_flashdata('register_success', 'Registrasi berhasil. Silakan masuk.');
                redirect(site_url('Login'));
            }
        }

        $this->load->view('register', [
            'rtrws' => $rtrws,
            'error' => $error,
            'old' => $old,
        ]);
    }

    private function validateRegister($post)
    {
        $required = ['nama', 'alamat', 'rtrw', 'kontak', 'username', 'password', 'confirm_password'];
        foreach ($required as $field) {
            if (empty(trim($post[$field] ?? ''))) {
                return 'Semua field wajib diisi.';
            }
        }

        if ($post['password'] !== $post['confirm_password']) {
            return 'Password dan konfirmasi password tidak sesuai.';
        }

        $existing = $this->Users->findOne(['username' => trim($post['username'])]);
        if (!empty($existing['uuid'])) {
            return 'Username sudah digunakan.';
        }

        $rtrw = $this->Rtrws->findOne(['uuid' => $post['rtrw']]);
        if (empty($rtrw['uuid'])) {
            return 'RT/RW tidak valid.';
        }

        return '';
    }

    public function Logout()
    {
        $this->session->sess_destroy();
        redirect(base_url());
    }
}
