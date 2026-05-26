<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Users extends MY_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->table = 'user';
        $this->thead = [
            (object) ['mData' => 'orders', 'sTitle' => 'No', 'visible' => false],
            (object) ['mData' => 'username', 'sTitle' => 'Username'],
            (object) ['mData' => 'role_name', 'sTitle' => 'Role'],
        ];
        $this->form  = [];

        $this->form[] = [
            'name' => 'username',
            'label' => 'Username'
        ];

        $this->form[] = [
            'name' => 'role',
            'label' => 'Role',
            'options' => [],
            'attributes' => [
                ['data-autocomplete' => 'true'],
                ['data-model' => 'Roles'],
                ['data-field' => 'name']
            ],
        ];

        $this->form[] = [
            'type' => 'password',
            'name' => 'password',
            'label' => 'Password'
        ];

        $this->form[] = [
            'type' => 'password',
            'name' => 'confirm_password',
            'label' => 'Confirm Password'
        ];
    }

    public function delete($uuid)
    {
        $user = $this->findOne($uuid);
        if ('admin' !== $user['username']) {
            return parent::delete($uuid);
        }
    }

    public function save($data)
    {
        if (strlen($data['password']) > 0) {
            if ($data['password'] !== $data['confirm_password']) {
                return ['error' => ['message' => 'Password tidak sesuai']];
            } else {
                $data['password'] = md5($data['password']);
            }
        } else {
            unset($data['password']);
        }
        unset($data['confirm_password']);
        return parent::save($data);
    }

    public function findOne($param)
    {
        $record = parent::findOne($param);
        $record['confirm_password'] = '';
        return $record;
    }

    public function dt()
    {
        $this->datatables
            ->select("{$this->table}.uuid")
            ->select("{$this->table}.orders")
            ->select("{$this->table}.username")
            ->select('role.name as role_name', false)
            ->join('role', 'role.uuid = user.role', 'left');
        return parent::dt();
    }
}
