<?php
error_reporting(E_ALL ^ E_NOTICE ^ E_DEPRECATED ^ E_WARNING);
include 'db_login_detail.php';

    $select_sql = "SELECT * FROM `app` where shop='vw-product-customizer.myshopify.com'";
    $res = mysqli_query($db_obj, $select_sql);
    while($res_arr = mysqli_fetch_assoc($res)){
      // $shops[]=$res_arr['shop'];
      echo "<pre>";
      print_r($res_arr);
      echo "</pre>";
    }
    // $installed = implode(',',$shops);
    // echo $installed;
?>
