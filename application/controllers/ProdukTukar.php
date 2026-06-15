<?php defined('BASEPATH') or exit('No direct script access allowed');

class ProdukTukar extends MY_Controller
{

	function __construct()
	{
		$this->model = 'ProdukTukars';
		parent::__construct();
		$this->page_subtitle = 'Kelola produk tukar yang bisa ditukar warga menggunakan saldo Bank Sampah';
		$this->header_buttons = 'custom-header-buttons/produk-tukar-header-buttons';
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
		$vars['page_name'] = 'custom-tables/table-produk-tukar';
		$vars['js'] = [
			// 'jquery.dataTables.min.js',
			// 'table.js'
		];
		$vars['thead'] = $this->$model->thead;
		$vars['overview'] = $this->$model->getOverView();
		$vars['products'] = $this->$model->find();
		$vars['categories'] = $this->$model->getCategories();
		$this->loadview('index', $vars);
	}
}
