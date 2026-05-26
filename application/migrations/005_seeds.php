<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Migration_seeds extends CI_Migration
{
    public function up()
    {
        $this->load->model(['Users', 'Roles', 'Permissions', 'Menus']);
        $fas = ['database', 'desktop', 'download', 'ethernet', 'hdd', 'hdd', 'headphones', 'keyboard', 'keyboard', 'laptop', 'memory', 'microchip', 'mobile', 'mobile-alt', 'plug', 'power-off', 'print', 'satellite', 'satellite-dish', 'save', 'save', 'sd-card', 'server', 'sim-card', 'stream', 'tablet', 'tablet-alt', 'tv', 'upload'];
        $admin = $this->Roles->create(['name' => 'Admin']);
        $this->Roles->create(['name' => 'Content Creator']);
        foreach (['User', 'Role', 'Permission', 'Menu', 'Creator', 'Content', 'Interaction', 'GlobalScore', 'RelationshipScore'/*additionalEntity*/] as $entity) {
            foreach (['index', 'create', 'read', 'update', 'delete'] as $action) {
                $this->Permissions->create([
                    'role' => $admin,
                    'action' => $action,
                    'entity' => $entity
                ]);
            }
            if (!in_array($entity, ['Menu', 'Permission', 'Role'])) {
                $this->Menus->create([
                    'role' => $admin,
                    'name' => $entity,
                    'url' => $entity,
                    'icon' => $fas[rand(0, count($fas) - 1)]
                ]);
            }
        }

        $this->Users->create([
            'username' => 'admin',
            'password' => md5('admin'),
            'role' => $admin
        ]);
    }

    public function down()
    {

    }

}
