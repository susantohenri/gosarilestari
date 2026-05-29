<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Migration_setortunai extends CI_Migration
{

  function up()
  {

    $this->db->query("
      CREATE TABLE `setortunai` (
        `uuid` varchar(36) NOT NULL,
        `orders` INT(11) UNIQUE NOT NULL AUTO_INCREMENT,
        `createdAt` datetime DEFAULT NULL,
        `updatedAt` datetime DEFAULT NULL,
        `deletedAt` datetime DEFAULT NULL,
        `status` tinyint NOT NULL DEFAULT 1,
        `kode` varchar(6) NOT NULL,
        `warga` varchar(36) NOT NULL,
        `petugas` varchar(36) NOT NULL,
        `bulan` varchar(16) NOT NULL,
        `tahun` INT(4) NOT NULL,
        `nominal` FLOAT NOT NULL DEFAULT 0,
        PRIMARY KEY (`uuid`),
        KEY `warga` (`warga`),
        KEY `petugas` (`petugas`)
      ) ENGINE=InnoDB DEFAULT CHARSET=utf8
    ");
  }

  function down()
  {
    $this->db->query("DROP TABLE IF EXISTS `setortunai`");
  }
}
