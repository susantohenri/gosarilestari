<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Roles extends MY_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->table = 'role';
        $this->thead = [
            (object) ['mData' => 'orders', 'sTitle' => 'No', 'visible' => false],
            (object) ['mData' => 'name', 'sTitle' => 'Role'],
        ];
        $this->form = [
            [
                'name' => 'name',
                'label' => 'Role Name',
            ],
        ];

        $this->childs[] = ['label' => 'Role Menu', 'controller' => 'Menu', 'model' => 'Menus'];
        $this->childs[] = ['label' => 'Role Permission', 'controller' => 'Permission', 'model' => 'Permissions'];
    }

    public function dt()
    {
        $this->datatables
            ->select("{$this->table}.uuid")
            ->select("{$this->table}.orders")
            ->select("{$this->table}.name");
        return parent::dt();
    }
}
