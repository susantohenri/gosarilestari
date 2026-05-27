<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Migration_hasilpemilahan extends CI_Migration
{

  function up()
  {

    $this->db->query("
      CREATE TABLE `hasilpemilahan` (
        `uuid` varchar(36) NOT NULL,
        `orders` INT(11) UNIQUE NOT NULL AUTO_INCREMENT,
        `createdAt` datetime DEFAULT NULL,
        `updatedAt` datetime DEFAULT NULL,
        `deletedAt` datetime DEFAULT NULL,
        `status` tinyint NOT NULL DEFAULT 1,
        `kode` varchar(6) NOT NULL,
        `transaksisampah` varchar(36) NOT NULL,
        `kategorisampah` varchar(36) NOT NULL,
        `berat` FLOAT NOT NULL DEFAULT 0,
        `harga` FLOAT NOT NULL DEFAULT 0,
        `total` FLOAT NOT NULL DEFAULT 0,
        PRIMARY KEY (`uuid`),
        KEY `transaksisampah` (`transaksisampah`),
        KEY `kategorisampah` (`kategorisampah`)
      ) ENGINE=InnoDB DEFAULT CHARSET=utf8
    ");
  }

  function down()
  {
    $this->db->query("DROP TABLE IF EXISTS `hasilpemilahan`");
  }
}
