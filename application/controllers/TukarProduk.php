<?php defined('BASEPATH') or exit('No direct script access allowed');

class TukarProduk extends MY_Controller
{

	function __construct()
	{
		$this->model = 'TukarProduks';
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

				try {
					$result = $this->$model->save($post);
				} catch (Exception $e) {
					$this->db->db_debug = $db_debug;
					$this->session->set_flashdata('model_error', $e->getMessage());
					redirect("$this->controller/create");
				}

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
		$vars['page_name'] = 'Warga' !== $this->session->userdata('role_name') ? 'table-tukar-produk-warga' : 'table';
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
		$vars['form'] = $this->$model->getForm();
		$vars['uuid'] = '';
		$vars['js'] = [
			'select2.full.min.js',
			'form.js'
		];

		if ('Warga' === $this->session->userdata('role_name')) {
			$vars['form'] = array_filter($vars['form'], function ($field) {
				return $field['name'] !== 'warga';
			});
		}

		$this->loadview('index', $vars);
	}
}
