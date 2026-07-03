<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Migration_notifikasi extends CI_Migration
{

  function up()
  {

    $this->db->query("
      CREATE TABLE `notifikasi` (
        `uuid` varchar(36) NOT NULL,
        `orders` int NOT NULL AUTO_INCREMENT,
        `createdAt` datetime DEFAULT CURRENT_TIMESTAMP,
        `updatedAt` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        `deletedAt` datetime DEFAULT NULL,
        `status` tinyint NOT NULL DEFAULT '1',
        `kode` varchar(6) NOT NULL,
        `user` varchar(36) NOT NULL,
        `judul` varchar(255) NOT NULL,
        `informasi` text NOT NULL,
        `isRead` tinyint NOT NULL DEFAULT '0',
        PRIMARY KEY (`uuid`),
        UNIQUE KEY `orders` (`orders`),
        KEY `idx_notifikasi_user` (`user`)
      ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;
    ");
  }

  function down()
  {
    $this->db->query("DROP TABLE IF EXISTS `notifikasi`");
  }
}
