<?php
header("Access-Control-Allow-Origin: *");
include 'db_login_detail.php';
require 'shopifyclient.php';
require 'vendor/autoload.php';

use sandeepshetty\shopify_api;

$date = date("Y-m-d H:i:s");
exit;
if(isset($_GET['shop']) && $_GET['shop'] != ""){
    global $db_obj;
    $shop = preg_replace('/^www\./', '', $_GET['shop']);

    $select_sql = "SELECT `id`,`token`,`store_metafield_id`,`shop` FROM `app` WHERE (`shop` = '" . $shop . "' OR `shop_domain` = '".$shop."') AND `app_status`='installed' ORDER BY `id` DESC LIMIT 1";
    $res = mysqli_query($db_obj, $select_sql);

    if (mysqli_num_rows($res) > 0) {
        $res_arr = mysqli_fetch_assoc($res);
        $token = $res_arr['token'];
        $shop = $res_arr['shop'];
        $sc = new ShopifyClient($shop, $token, SHOPIFY_API_KEY, SHOPIFY_SECRET);

        $active_theme_id = "";

        try {
            $themes = $sc->call('GET', '/admin/api/2020-01/themes.json');
            $active_theme_arr = loopAndFind($themes, 'role', 'main');
            $active_theme_id = $active_theme_arr[0]['id'];
        } catch (Exception $ex) {

        }

        if($active_theme_id != ""){
            $timestamp = "{% assign timestamp = '".strtotime(date('Y-m-d H:i:s'))."' %}";
            $assets_arr1 = array("asset" =>
                                    array(
                                         "key" => "snippets/timestamp.liquid",
                                         "value" => (string)$timestamp
                                    ));
            try {
                    $resp1 = $sc->call('PUT', "/admin/api/2020-01/themes/{$active_theme_id}/assets.json", $assets_arr1);
                    /*echo "<pre>";
                    print_R($resp1);*/
            } catch (Exception $e) {
                echo "You are not authorized to access this file.";
                    //echo $e;
            }
        }else{
            exit();
        }

    }else{
        echo "Not Ins =You are not authorized to access this file.";
    }
}else{
    echo "You are not authorized to access this file.";
}

?>
