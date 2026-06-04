<?php defined('BASEPATH') or exit('No direct script access allowed');

class Warga extends MY_Controller
{

	function __construct()
	{
		$this->model = 'Wargas';
		parent::__construct();
		$this->page_subtitle = 'Hanya admin yang dapat menambahkan warga baru';
		$this->header_buttons = 'custom-header-buttons/warga-header-buttons';
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
		$vars['page_name'] = 'custom-tables/table-warga';
		$vars['js'] = [
			'jquery.dataTables.min.js',
			'select2.full.min.js',
			'table-warga.js'
		];
		$vars['thead'] = $this->$model->thead;
		$vars['overview'] = $this->$model->getOverView();
		$this->loadview('index', $vars);
	}
}
