<?php defined('BASEPATH') or exit('No direct script access allowed');

class KategoriSampah extends MY_Controller
{

	function __construct()
	{
		$this->model = 'KategoriSampahs';
		$this->page_subtitle = 'Atur jenis sampah yang diterima dan harga per-kilogram-nya';
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
		$vars['page_name'] = 'custom-tables/table-kategori-sampah';
		$vars['js'] = [
			'jquery.dataTables.min.js',
			'table.js'
		];
		$vars['thead'] = $this->$model->thead;
		$vars['overview'] = $this->$model->getOverView();
		$vars['pratinjau'] = $this->$model->find();
		$this->loadview('index', $vars);
	}
}
