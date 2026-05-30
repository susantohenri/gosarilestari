<?php defined('BASEPATH') or exit('No direct script access allowed');

class Ledger extends MY_Controller
{

	function __construct()
	{
		$this->model = 'Ledgers';
		$this->page_title = 'Riwayat Transaksi';
		parent::__construct();
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
			'table-ledger.js'
		];
		$vars['thead'] = $this->$model->thead;
		$vars['page_title'] = 'Riwayat Transaksi';
		$this->loadview('index', $vars);
	}
}
