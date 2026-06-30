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
        `activatedAt` datetime DEFAULT CURRENT_TIMESTAMP,
        `deletedAt` datetime DEFAULT NULL,
        `status` tinyint NOT NULL DEFAULT 1,
        `kode` varchar(6) NOT NULL,
        `nama` varchar(255) NOT NULL,
        `kontak` varchar(255) NOT NULL,
        `alamat` varchar(255) NOT NULL,
        `rtrw` varchar(36) NOT NULL,
        `saldo` float NOT NULL DEFAULT 0,
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
