<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Migration_tukarproduk extends CI_Migration
{

  function up()
  {

    $this->db->query("
      CREATE TABLE `tukarproduk` (
        `uuid` varchar(36) NOT NULL,
        `orders` INT(11) UNIQUE NOT NULL AUTO_INCREMENT,
        `createdAt` datetime DEFAULT NULL,
        `updatedAt` datetime DEFAULT NULL,
        `deletedAt` datetime DEFAULT NULL,
        `status` ENUM('DIBAYAR', 'DIAMBIL'),
        `kode` varchar(6) NOT NULL,
        `warga` varchar(36) NOT NULL,
        `petugas` varchar(36) NOT NULL,
        `produktukar` varchar(36) NOT NULL,
        `harga` FLOAT NOT NULL DEFAULT 0,
        `qty` INT(11) NOT NULL,
        `total` FLOAT NOT NULL DEFAULT 0,
        PRIMARY KEY (`uuid`),
        KEY `warga` (`warga`),
        KEY `petugas` (`petugas`),
        KEY `produktukar` (`produktukar`)
      ) ENGINE=InnoDB DEFAULT CHARSET=utf8
    ");
  }

  function down()
  {
    $this->db->query("DROP TABLE IF EXISTS `tukarproduk`");
  }
}
