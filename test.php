<?php
error_reporting(E_ALL ^ E_NOTICE ^ E_DEPRECATED ^ E_WARNING);
include 'db_login_detail.php';

// require_once("config.php");
// require 'shopifyclient.php';
// require 'vendor/autoload.php';
//
// use sandeepshetty\shopify_api;
//
// $tabele = "show tables";
// $tbl_query = mysqli_query($db_obj, $tabele);
// while ($tbl_result = mysqli_fetch_assoc($tbl_query)){
//   echo "<pre>";
//   print_r($tbl_result);
//   echo "</pre>";
// }

  $select_sql = "SELECT count(*) as records, shop from app group by shop having records>1";
  // $select_sql = "SELECT * FROM `app` order by id desc limit 100";
  $res = mysqli_query($db_obj, $select_sql);
  while ($res_arr = mysqli_fetch_assoc($res)){
    echo "<pre>";
    print_r($res_arr);
    echo "</pre>";
  }
  exit;

?>
