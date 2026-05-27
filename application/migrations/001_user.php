<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Migration_user extends CI_Migration
{
  public function up()
  {

    $this->db->query("
      CREATE TABLE `user` (
        `uuid` varchar(36) NOT NULL,
        `orders` INT(11) UNIQUE NOT NULL AUTO_INCREMENT,
        `username` varchar(255) NOT NULL,
        `password` varchar(255) NOT NULL,
        `role` varchar(36) NOT NULL,
        `createdAt` datetime DEFAULT NULL,
        `updatedAt` datetime DEFAULT NULL,
        `deletedAt` datetime DEFAULT NULL,
        PRIMARY KEY (`uuid`),
        KEY `role` (`role`)
      ) ENGINE=InnoDB DEFAULT CHARSET=utf8
    ");
  }

  public function down()
  {
    $this->db->query("DROP TABLE IF EXISTS `user`");
  }
}
