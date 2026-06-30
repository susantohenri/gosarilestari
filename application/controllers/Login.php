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
            ]);

            if (!isset($login['uuid'])) {
                $error = 'Pengguna tidak ditemukan.';
            } else if ($login['password'] !== md5($post['password'])) {
                $error = 'Kata sandi tidak tepat.';
            } else if (1 != $login['status']) {
                $error = 'Status pengguna tidak aktif.';
            } else if (null === $login['activatedAt']) {
                $error = 'Pengguna belum diaktivasi.';
            } else {
                $this->load->model('Roles');
                $role = $this->Roles->findOne(['uuid' => $login['role']]);
                $login['role_name'] = $role['name'];
                $this->session->set_userdata($login);
                redirect(base_url());
            }
        }

        $this->load->view('login', ['error' => $error]);
    }

    public function register()
    {
        if ($this->session->userdata('uuid')) {
            redirect(base_url());
        }

        $this->load->model(['Rtrws', 'Users']);
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
            $error = $this->validateRegister($post, $rtrws);

            if ($error === '') {
                $this->load->model(['Wargas', 'Notifikasis']);
                $nama = trim($post['nama']);
                $uuid = $this->Wargas->create([
                    'nama' => $nama,
                    'alamat' => trim($post['alamat']),
                    'rtrw' => $post['rtrw'],
                    'kontak' => trim($post['kontak']),
                    'username' => trim($post['username']),
                    'password' => md5($post['password']),
                    'saldo' => 0,
                    'status' => 1,
                    'activatedAt' => null
                ]);
                $admins = $this->Users->getAdmins();
                $wargaUrl = site_url("Warga/Read/{$uuid}");
                foreach ($admins as $admin) {
                    $this->Notifikasis->create([
                        'user' => $admin->uuid,
                        'period' => strtoupper(base_convert(time() + rand(), 10, 36)),
                        'judul' => 'Permohonan aktivasi warga baru - ' . $nama,
                        'informasi' => "Silakan klik link berikut untuk melihat detail permohonan warga baru: <u><a href='{$wargaUrl}'>{$nama}</a></u>"
                    ]);
                }
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

    private function validateRegister($post, $rtrws)
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

        $filtered = array_filter($rtrws, function ($item) use ($post) {
            return $item->uuid === $post['rtrw'];
        });
        $rtrw = reset($filtered);
        if (empty($rtrw->uuid)) {
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
