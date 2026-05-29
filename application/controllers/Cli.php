<?php

defined('BASEPATH') or exit('No direct script access allowed');

class CLI extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();

        if (!is_cli()) {
            show_404();
        }
    }

    public function Migrate($version = null)
    {
        $this->load->library('migration');
        $success = !is_null($version) ? $this->migration->version($version) : $this->migration->latest();
        if (!$success) {
            show_error($this->migration->error_string());
        }
    }
}
