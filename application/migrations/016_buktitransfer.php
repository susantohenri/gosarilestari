<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Migration_buktitransfer extends CI_Migration
{

  function up()
  {
    $this->db->query("
      ALTER TABLE `pembayaran` 
      ADD COLUMN `buktitransfer` varchar(255) NOT NULL DEFAULT ''
    ");
  }

  function down()
  {
    $this->db->query("
      ALTER TABLE `pembayaran` 
      DROP COLUMN `buktitransfer`
    ");
  }
}
