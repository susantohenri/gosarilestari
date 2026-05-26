<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Migration_role extends CI_Migration
{
    public function up()
    {

        $this->db->query("
      CREATE TABLE `role` (
        `uuid` varchar(36) NOT NULL,
        `orders` INT(11) UNIQUE NOT NULL AUTO_INCREMENT,
        `name` varchar(255) NOT NULL,
        `createdAt` datetime DEFAULT NULL,
        `deletedAt` datetime DEFAULT NULL,
        `updatedAt` datetime DEFAULT NULL,
        PRIMARY KEY (`uuid`)
      ) ENGINE=InnoDB DEFAULT CHARSET=utf8
    ");

    }

    public function down()
    {
        $this->db->query("DROP TABLE IF EXISTS `role`");
    }

}
