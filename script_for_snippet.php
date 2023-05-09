<?php
ini_set('max_execution_time', 0);
// date_default_timezone_set('Asia/Kolkata');

include 'common_includes.php';
include 'generate_theme_snippet.php';

if (isset($_GET['shop']) && $_GET['shop'] != ""){
    $shop = $_GET['shop'];
    $select_sql = "SELECT `id`,`token`,`shop` FROM `app` WHERE `app_status`='installed' AND shop='".$shop."' ORDER BY `id` DESC LIMIT 1";
}else{
    $select_sql = "SELECT `id`,`token`,`shop` FROM `app` WHERE `snippet_status`='0' AND `app_status`='installed' ORDER BY `id` DESC";
}
$res = mysqli_query($db_obj, $select_sql);
if (mysqli_num_rows($res) > 0) {
    while ($res_arr = mysqli_fetch_assoc($res)){
        $token = $res_arr['token'];
        $shop = $res_arr['shop'];
        $sc = new ShopifyClient($shop, $token, SHOPIFY_API_KEY, SHOPIFY_SECRET);
        genereate_article_html($sc,$db_obj,$shop);
    }
}

?>
