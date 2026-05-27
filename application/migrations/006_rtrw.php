<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Migration_rtrw extends CI_Migration
{

  function up()
  {

    $this->db->query("
      CREATE TABLE `rtrw` (
        `uuid` varchar(36) NOT NULL,
        `orders` INT(11) UNIQUE NOT NULL AUTO_INCREMENT,
        `createdAt` datetime DEFAULT NULL,
        `updatedAt` datetime DEFAULT NULL,
        `deletedAt` datetime DEFAULT NULL,
        `status` tinyint NOT NULL DEFAULT 1,
        `kode` varchar(6) NOT NULL,
        `rt` varchar(16) NOT NULL,
        `rw` varchar(16) NOT NULL,
        `latitude` varchar(16) NOT NULL,
        `longitude` varchar(16) NOT NULL,
        PRIMARY KEY (`uuid`)
      ) ENGINE=InnoDB DEFAULT CHARSET=utf8
    ");
  }

  function down()
  {
    $this->db->query("DROP TABLE IF EXISTS `rtrw`");
  }
}
