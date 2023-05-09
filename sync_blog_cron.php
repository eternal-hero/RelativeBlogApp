<?php
error_reporting(E_ALL ^ E_NOTICE ^ E_DEPRECATED ^ E_WARNING);

$db_obj = mysqli_connect("localhost","digit@digitaldar", ",e%.IHcO", "digitald4_shopify");
define('DOMAIN_NAME', 'www.digitaldarts.com.au/relatedposts');
define('SITE_URL', 'https://www.digitaldarts.com.au/relatedposts');
mysqli_set_charset($db_obj,"utf8");

if (!$db_obj) {
    echo "Failed to connect to MySQL: " . mysqli_connect_error();
    exit;
}

ini_set('max_execution_time', 0);
date_default_timezone_set('Asia/Kolkata');
require_once 'config.php';
require 'shopifyclient.php';
require 'vendor/autoload.php';

use sandeepshetty\shopify_api;

$select_sql = "SELECT `id`,`token`,`shop` FROM `app` WHERE shop='vikas-24.myshopify.com' AND `sync_taken`='0' AND `app_status`='installed' ORDER BY `id` DESC limit 1";
$res = mysqli_query($db_obj, $select_sql);
if (mysqli_num_rows($res) > 0) {
    while ($res_arr = mysqli_fetch_assoc($res)) {
        $token = $res_arr['token'];
        $shop = $res_arr['shop'];

        mysqli_query($db_obj, "UPDATE `app` SET `sync_taken`='1' where `shop`='".$shop."'");

        $sc = new ShopifyClient($shop, $token, SHOPIFY_API_KEY, SHOPIFY_SECRET);
        try {
            $total_blogs = $sc->call("GET", "/admin/api/".getYear()."/blogs/count.json");

            if ($total_blogs > 0) {
                $get_new_blogs = $sc->call("GET", "admin/api/".getYear()."/blogs.json");
                $new_blog_ids = array();
                foreach ($get_new_blogs as $b) {
                    $new_blog_ids[] = $b['id'];
                }

                $old_blog_ids = array();
                $get_old_blogs = mysqli_query($db_obj, "select b_id from blog_list where shop='".$shop."'");
                if (mysqli_num_rows($get_old_blogs) > 0) {
                    while ($res = mysqli_fetch_assoc($get_old_blogs)) {
                        $old_blog_ids[] = $res['b_id'];
                    }
                }

                $delete_bid = array_diff($old_blog_ids,$new_blog_ids);

                $delete_bcount = count($delete_bid);

                $d_count = 0;
                if($delete_bid != null){
                    foreach ($delete_bid as $id){
                        $d_count++;
                        mysqli_query($db_obj, "DELETE FROM `blog_list` WHERE `shop` = '" . $shop . "' AND b_id='" . $id . "'");
                    }
                }

                $loop_count = 0;
                if($delete_bcount == $d_count){
                    foreach ($get_new_blogs as $blog) {
                        $loop_count++;
                        $b_id = $blog['id'];
                        $b_title = mysqli_real_escape_string($db_obj, $blog['title']);
                        $b_handle = mysqli_real_escape_string($db_obj, $blog['handle']);
                        $b_tags = mysqli_real_escape_string($db_obj, $blog['tags']);
                        $b_json = json_encode($blog);
                        $select_sql_1 = "SELECT `id`,`b_id` FROM `blog_list` WHERE `shop` = '" . $shop . "' AND b_id='" . $b_id . "'";
                        $res1 = mysqli_query($db_obj, $select_sql_1);
                        if (mysqli_num_rows($res1) > 0) {
                            mysqli_query($db_obj, "UPDATE blog_list SET b_title='" . $b_title . "',b_handle='" . $b_handle . "',b_tags='" . $b_tags . "',b_json='" . mysqli_real_escape_string($db_obj, $b_json) . "' WHERE `shop` = '" . $shop . "' AND b_id='" . $b_id . "'");
                        } else {
                            mysqli_query($db_obj, "INSERT into blog_list(`b_id`,`shop`,`b_title`,`b_handle`,`b_tags`,`b_json`) "
                                    . "VALUES('" . $b_id . "','" . $shop . "','" . $b_title . "','" . $b_handle . "','" . $b_tags . "','" . mysqli_real_escape_string($db_obj, $b_json) . "')");
                        }

                        if ($loop_count == $total_blogs) {
                             mysqli_query($db_obj, "UPDATE `app` SET `sync_taken`='0' where `shop`='".$shop."'");
                        }
                    }
                }
            }else{
                mysqli_query($db_obj, "DELETE FROM `blog_list` WHERE `shop` = '" . $shop . "'");
                mysqli_query($db_obj, "UPDATE `app` SET `sync_taken`='0' where `shop`='".$shop."'");
            }
        } catch (Exception $ex) {
        }
    }
}
?>
