<?php defined('BASEPATH') or exit('No direct script access allowed');

class Penukaran extends MY_Controller
{

	function __construct()
	{
		$this->model = 'Penukarans';
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
		$vars['page_name'] = 'custom-tables/table-penukaran';
		$vars['js'] = [
			'jquery.dataTables.min.js',
			'table-penukaran.js'
		];
		$vars['thead'] = $this->$model->thead;
		$vars['overview'] = $this->$model->getOverView();
		$this->load->model('ProdukTukars');
		$vars['products'] = $this->ProdukTukars->find();
		$vars['categories'] = $this->ProdukTukars->getCategories();
		$this->loadview('index', $vars);
	}

	public function read($id)
	{
		$model = $this->model;
		$vars = [];

		// approved penukaran shouldn't be updated
		// still can be deleted only by admin & kasir
		$found = $this->$model->findOne($id);
		if ('APPROVED' === $found['status']) {
			$this->load->model('Permissions');
			$permissionToTakenOut = in_array($this->session->userdata('role_name'), ['Admin', 'Kasir']) ? ['update_Pembayaran'] : ['update_Pembayaran', 'delete_Pembayaran'];
			$vars['permission'] = array_filter($this->Permissions->getPermissions(), function ($perm) use ($permissionToTakenOut) {
				return !in_array($perm, $permissionToTakenOut);
			});
		}

		$vars['page_name'] = 'form';
		$vars['form'] = $this->$model->getForm($id);
		$vars['uuid'] = $id;
		$vars['js'] = [
			'select2.full.min.js',
			'form.js'
		];
		$this->loadview('index', $vars);
	}
}
