<?php defined('BASEPATH') or exit('No direct script access allowed');

class Pembayaran extends MY_Controller
{

	function __construct()
	{
		$this->model = 'Pembayarans';
		parent::__construct();
	}

	public function index()
	{
		$model = $this->model;
		if ($post = $this->$model->lastSubmit($this->input->post())) {
			if (isset($post['delete'])) {
				$this->$model->delete($post['delete']);
			} else {
				$result = $this->$model->save($post);
				if (isset($result['error'])) {
					$this->session->set_flashdata('model_error', $result['error']);
					redirect($this->controller);
				}
			}
		}
		$vars = [];
		$vars['page_name'] = 'custom-tables/table-pembayaran';
		$vars['js'] = [
			'jquery.dataTables.min.js',
			'table-pembayaran.js'
		];
		$vars['thead'] = $this->$model->thead;
		$vars['overview'] = $this->$model->getOverView();
		$this->loadview('index', $vars);
	}

	public function read($id)
	{
		$model = $this->model;
		$vars = [];

		// approved Pembayaran shouldn't be updated or deleted
		$found = $this->$model->findOne($id);
		if ('KASIR' === $found['status']) {
			$this->load->model('Permissions');
			$vars['permission'] = array_filter($this->Permissions->getPermissions(), function ($perm) {
				return !in_array($perm, ['update_Pembayaran', 'delete_Pembayaran']);
			});
		}

		// warga shouldn't be able to update pembayaran which is already in AGEN
		if ('AGEN' === $found['status'] && 'Warga' === $this->session->userdata('role_name')) {
			$this->load->model('Permissions');
			$vars['permission'] = array_filter($this->Permissions->getPermissions(), function ($perm) {
				return !in_array($perm, ['update_Pembayaran', 'delete_Pembayaran']);
			});
		}

		$vars['page_name'] = 'form';
		if ('KASIR' !== $found['status'] && 'Kasir' === $this->session->userdata('role_name')) {
			$vars['page_name'] = 'form-approval';
		}

		$vars['form'] = $this->$model->getForm($id);
		$vars['uuid'] = $id;
		$vars['js'] = [
			'select2.full.min.js',
			'form.js'
		];
		$this->loadview('index', $vars);
	}
}
