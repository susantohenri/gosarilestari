<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Migration_produktukar extends CI_Migration
{

  function up()
  {

    $this->db->query("
      CREATE TABLE `produktukar` (
        `uuid` varchar(36) NOT NULL,
        `orders` INT(11) UNIQUE NOT NULL AUTO_INCREMENT,
        `createdAt` datetime DEFAULT NULL,
        `updatedAt` datetime DEFAULT NULL,
        `deletedAt` datetime DEFAULT NULL,
        `status` tinyint NOT NULL DEFAULT 1,
        `kode` varchar(6) NOT NULL,
        `nama` varchar(255) NOT NULL,
        `kategori` varchar(255) NOT NULL,
        `harga` FLOAT NOT NULL DEFAULT 0,
        `stok` INT(11) NOT NULL,
        `terjual` INT(11) NOT NULL,
        PRIMARY KEY (`uuid`)
      ) ENGINE=InnoDB DEFAULT CHARSET=utf8
    ");
  }

  function down()
  {
    $this->db->query("DROP TABLE IF EXISTS `produktukar`");
  }
}
