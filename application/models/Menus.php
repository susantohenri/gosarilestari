<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Menus extends MY_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->table = 'menu';
        $this->form = [
            [
                'name' => 'name',
                'width' => 3,
                'label' => 'Name',
            ],
            [
                'name' => 'url',
                'width' => 2,
                'label' => 'URL',
            ],
            [
                'name' => 'icon',
                'width' => 2,
                'label' => 'Icon',
            ],
        ];
    }
}
