<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Migration_informasi extends CI_Migration
{
  public function up()
  {

    $this->db->query("
      CREATE TABLE `informasi` (
        `uuid` varchar(36) NOT NULL,
        `orders` INT(11) UNIQUE NOT NULL AUTO_INCREMENT,
        `createdAt` datetime DEFAULT NULL,
        `updatedAt` datetime DEFAULT NULL,
        `deletedAt` datetime DEFAULT NULL,
        `status` tinyint NOT NULL DEFAULT 1,
        `kode` varchar(6) NOT NULL,
        `title` varchar(255) NOT NULL,
        `content` TEXT,
        PRIMARY KEY (`uuid`)
      ) ENGINE=InnoDB DEFAULT CHARSET=utf8
    ");
  }

  public function down()
  {
    $this->db->query("DROP TABLE IF EXISTS `informasi`");
  }
}
