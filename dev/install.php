<?php

require_once("config.php");
require 'shopifyclient.php';
require 'vendor/autoload.php';

use sandeepshetty\shopify_api;

if(isset($_REQUEST['shop'])){

$shop = $_GET['shop'];
// $shop = ($_REQUEST['shop'] != '') ? $_REQUEST['shop'] : $_SESSION['shop'];
$date = date("Y-m-d H:i:s");
$select_sql = "SELECT `id`,`token` FROM `app` WHERE `shop` = '".$shop."' ORDER BY `id` DESC LIMIT 1";
$res = mysqli_query($db_obj, $select_sql);
if (mysqli_num_rows($res) > 0) {
    $res_arr = mysqli_fetch_assoc($res);
    $token = $res_arr['token'];
}

$sc = new ShopifyClient($shop, $token, SHOPIFY_API_KEY, SHOPIFY_SECRET);
session_unset();


try {
    $flag = "false";
    $get_blogs = $sc->call("GET", "admin/api/".getYear()."/blogs.json");
    $total_blogs = $sc->call("GET", "/admin/api/".getYear()."/blogs/count.json");

    $blog_ids = "";
    if ($total_blogs > 0) {
        $loop_count = 0;
        foreach ($get_blogs as $blog) {
            $loop_count++;
            $b_id = $blog['id'];
            $blog_ids .= $b_id . ",";
            $b_title = mysqli_real_escape_string($db_obj, $blog['title']);
            $b_handle = mysqli_real_escape_string($db_obj, $blog['handle']);
            $b_tags = mysqli_real_escape_string($db_obj, $blog['tags']);
            $b_json = mysqli_real_escape_string($db_obj, json_encode($blog));
            $select_sql_1 = "SELECT `id`,`b_id` FROM `blog_list` WHERE `shop` = '" . $shop . "' AND b_id='" . $b_id . "'";
            $res1 = mysqli_query($db_obj, $select_sql_1);
            if (mysqli_num_rows($res1) > 0) {
                mysqli_query($db_obj, "UPDATE blog_list SET b_title='" . $b_title . "',b_handle='" . $b_handle . "',b_tags='" . $b_tags . "',b_json='" .$b_json . "' WHERE `shop` = '" . $shop . "' AND b_id='" . $b_id . "'");
            } else {
                mysqli_query($db_obj, "INSERT into blog_list(`b_id`,`shop`,`b_title`,`b_handle`,`b_tags`,`b_json`) "
                        . "VALUES('" . $b_id . "','" . $shop . "','" . $b_title . "','" . $b_handle . "','" . $b_tags . "','" . $b_json . "')");
            }
 
            if ($loop_count == $total_blogs) {
                $flag = "true";
            }
        }
    } else {
        $flag = "true";
    }

    $select_sql_2 = "SELECT `id` FROM `html_settings` WHERE `shop` = '" . $shop . "' ORDER BY `id` DESC LIMIT 1";
    $res2 = mysqli_query($db_obj, $select_sql_2);
    if (mysqli_num_rows($res2) <= 0) {
        $top_text = '<h3>Related Posts</h3>';
        $html_before_list = '<ul>';
        $html_after_list = '</ul>';
        $html_before_post = '<li>';
        $html_after_post = '</li>';
        $no_char_post = 120;
		mysqli_query($db_obj, "INSERT into html_settings(`shop`,`top_text`,`html_before_list`,`html_after_list`,`html_before_post`,`html_after_post`,`no_char_post`,`created_date`,`updated_date`,`select_theme`,`read_more_theme_2`,`custom_style`)"
                . "VALUES('" . $shop . "','" . $top_text . "','" . $html_before_list . "','" . $html_after_list . "','" . $html_before_post . "','" . $html_after_post . "','$no_char_post','" . $date . "','" . $date . "','','','')");
    }

    $select_sql_3 = "SELECT `id` FROM `related_settings` WHERE `shop` = '" . $shop . "' ORDER BY `id` DESC LIMIT 1";
    $res3 = mysqli_query($db_obj, $select_sql_3);
    if (mysqli_num_rows($res3) <= 0) {
        $blogs = rtrim($blog_ids, ",");
        /* mysqli_query($db_obj, "INSERT into related_settings(`shop`,`related_to`,`no_post_display`,`handle_rel_post`,`blogs`,`show_same_btype`,`created_date`,`updated_date`,`blog_status`,`install_status`)"
                . "VALUES('" . $shop . "','0','3','0','" . $blogs . "','1','" . $date . "','" . $date . "','" . $flag . "','0')"); */

		mysqli_query($db_obj, "INSERT into related_settings(`shop`,`related_to`,`no_post_display`,`handle_rel_post`,`blogs`,`show_same_btype`,`created_date`,`updated_date`,`blog_status`,`install_status`,`custom_msg`,`ex_tags`,`ex_ids`,`ex_frmpool_id`,`ex_frmpool_tags`,`image_size`,`display_desc`)"
                . "VALUES('" . $shop . "','0','3','0','" . $blogs . "','1','" . $date . "','" . $date . "','" . $flag . "','0','','','','','','','')");
    }
} catch (Exception $ex) {
    $flag = "true";
}

try {
    $update_sql = "UPDATE `app` SET `payment_status` = 'accepted',`sync_taken`='0' WHERE `shop` = '" . $shop . "'";
    mysqli_query($db_obj, $update_sql);

    /* Webhook for App Uninstall Track */
    $themes = $sc->call('GET', '/admin/api/'.getYear().'/themes.json');
    $active_theme_arr = loopAndFind($themes, 'role', 'main');
    $active_theme_id = $active_theme_arr[0]['id'];

    try {
        $webhooks_arr = array("webhook" =>
            array(
                "topic" => "app/uninstalled",
                "address" => SITE_URL . "/hook.php",
                "format" => "json"
        ));
        $resp = $sc->call('POST', "/admin/api/".getYear()."/webhooks.json", $webhooks_arr);

    } catch (exception $e) {

    }

} catch (exception $e) {
    echo $e;
    exit;
}
$_SESSION['shop'] = $shop;
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
    <head>
        <title>Installing..</title>
        <meta http-equiv="content-type" content="text/html; charset=utf-8" />
        <meta http-equiv="content-language" content="en" />
        <link href="style.css" rel="stylesheet" type="text/css" />
    </head>
    <body>

        <div class="span24">
            <div id="app-install">
                <p style="font-size: 15px; margin-top: 21px;" class="app_status">Please wait. The app is being installed...</p>
            </div>
        </div>
        <script>
            var flag = "<?= $flag; ?>";
            if (flag == "true") {
                setTimeout('redirectToAdmin()', 3000);
            }
            function redirectToAdmin() {
                window.location.href = "<?= SITE_URL ?>/admin/home.php?shop=<?= $shop; ?>";
            }
        </script>

    </body>
</html>
<?php }else{
    echo "Empty Shop Unauthorised Access";
} ?>
