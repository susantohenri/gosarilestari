<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Migration_transaksisampah extends CI_Migration
{

  function up()
  {

    $this->db->query("
      CREATE TABLE `transaksisampah` (
        `uuid` varchar(36) NOT NULL,
        `orders` INT(11) UNIQUE NOT NULL AUTO_INCREMENT,
        `createdAt` datetime DEFAULT NULL,
        `updatedAt` datetime DEFAULT NULL,
        `deletedAt` datetime DEFAULT NULL,
        `status` ENUM('DISETOR', 'DIPILAH', 'DIBAYAR') DEFAULT 'DISETOR',
        `kode` varchar(6) NOT NULL,
        `warga` varchar(36) NOT NULL,
        `petugas` varchar(36) NOT NULL,
        `kategori` varchar(255) NOT NULL,
        `berat` FLOAT NOT NULL DEFAULT 0,
        `pendapatan` FLOAT NOT NULL DEFAULT 0,
        `tagihan`  FLOAT NOT NULL DEFAULT 0,
        PRIMARY KEY (`uuid`),
        KEY `warga` (`warga`),
        KEY `petugas` (`petugas`)
      ) ENGINE=InnoDB DEFAULT CHARSET=utf8
    ");
  }

  function down()
  {
    $this->db->query("DROP TABLE IF EXISTS `transaksisampah`");
  }
}
