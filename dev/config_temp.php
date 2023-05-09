<?php
error_reporting(E_ALL ^ E_NOTICE ^ E_DEPRECATED ^ E_WARNING);

$db_obj = mysqli_connect("localhost","digit@digitaldar", ",e%.IHcO", "digitald4_shopify");
define('DOMAIN_NAME', 'www.digitaldarts.com.au/relatedposts');
define('SITE_URL', 'https://www.digitaldarts.com.au/relatedposts');
mysqli_set_charset($db_obj,"utf8");

if (!$db_obj) {
	echo "Failed to connect to MySQL: " . mysqli_connect_error();
}

$select_sql = "SELECT * FROM `app` WHERE `shop` = 'steele-henry.myshopify.com'";


$update_status = "UPDATE `app` SET shop_domain='www.steelehenry.com' WHERE `shop` = 'steele-henry.myshopify.com'";
mysqli_query($db_obj,$update_status);
echo "error :". mysqli_error();
//$select_sql = "SELECT `id`,`shop`,updated_date FROM `html_settings` WHERE updated_date >= NOW() - INTERVAL 3 DAY ORDER BY `id` DESC";
//$select_sql = "SELECT `id`,`shop`,updated_date FROM `related_settings` WHERE updated_date >= NOW() - INTERVAL 3 DAY ORDER BY `id` DESC";
//$select_sql = "SELECT `id`,`token`,`shop`,created_date FROM `app` WHERE `app_status`='installed' AND created_date >= NOW() - INTERVAL 3 DAY ORDER BY `id` DESC";
echo $select_sql;
$res = mysqli_query($db_obj,$select_sql);
if(!$res){
    echo "error :". mysqli_error();
}
echo mysqli_num_rows($db_obj,$res);
if (mysqli_num_rows($res) > 0) {

    while ($res_arr = mysqli_fetch_assoc($res)) {
        $shop = $res_arr['shop'];
        echo "<pre>";
        print_r($res_arr);
    }
}
exit;
/*$remain_store = mysqli_stmt_num_rows(mysqli_query("SELECT `id`,`token`,`shop` FROM `app` WHERE `snippet_status`='0' AND `app_status`='installed' ORDER BY `id` DESC"));
$comp_store = mysqli_stmt_num_rows(mysqli_query("SELECT `id`,`token`,`shop` FROM `app` WHERE `snippet_status`='1' AND `app_status`='installed' ORDER BY `id` DESC"));
$inprogress_store = mysqli_query("SELECT `id`,`token`,`shop` FROM `app` WHERE `snippet_status`='2' AND `app_status`='installed' ORDER BY `id` DESC");

$allstore = mysqli_stmt_num_rows(mysqli_query("SELECT `id`,`token`,`shop` FROM `app` WHERE `app_status`='installed' ORDER BY `id` DESC"));
echo "<pre>allstore = ".$allstore."</pre>";
echo "<pre>remain = ".$remain_store."</pre>";
echo "<pre>complete = ".$comp_store."</pre>";

while($res_arr = mysqli_fetch_assoc($inprogress_store)){
    echo "<pre>".$res_arr['shop']."</pre>";
}*/

//mysqli_query("UPDATE app set snippet_status='0'");
/*$query = mysqli_query("select * from app");
while ($res_arr = mysqli_fetch_assoc($query)){
    print_r(json_encode($res_arr));
}*/
//mysqli_query("ALTER TABLE  `app` ADD  `snippet_status` ENUM(  '0', '1', '2' ) NOT NULL AFTER  `sync_taken`");
//mysqli_query("ALTER TABLE  `related_settings` ADD  `enable_bimage` ENUM(  '0',  '1' ) NOT NULL AFTER  `install_status`");
/*mysqli_query("delete from `app` where shop='vikas-24.myshopify.com'");
mysqli_query("delete from `related_settings` where shop='vikas-24.myshopify.com'");
mysqli_query("delete from `html_settings` where shop='vikas-24.myshopify.com'");
mysqli_query("delete from `blog_list` where shop='vikas-24.myshopify.com'");*/

//mysqli_query("ALTER TABLE  `app` ADD  `sync_taken` ENUM(  '0',  '1' ) NOT NULL AFTER  `store_metafield_id`");
//mysqli_query("ALTER TABLE  `related_settings` CHANGE  `install_status`  `install_status` ENUM(  '0',  '1' ) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL");
//mysqli_query("ALTER TABLE  `related_settings` ADD  `install_status` ENUM(  '0',  '1',  '2' ) NOT NULL AFTER  `blog_status`");
//echo mysqli_stmt_num_rows($query);
/*mysqli_query("CREATE TABLE IF NOT EXISTS `app` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `shop` varchar(150) NOT NULL,
  `shop_domain` varchar(120) NOT NULL,
  `code` varchar(100) NOT NULL,
  `token` varchar(100) NOT NULL,
  `payment_status` varchar(50) NOT NULL DEFAULT 'free',
  `app_status` varchar(50) NOT NULL,
  `is_mail_sent` int(3) NOT NULL DEFAULT '0',
  `status_change_date` datetime NOT NULL,
  `created_date` datetime NOT NULL,
  `store_metafield_id` text NOT NULL,
  PRIMARY KEY (`id`)
)");

mysqli_query("CREATE TABLE IF NOT EXISTS `blog_list` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `b_id` bigint(20) NOT NULL,
  `shop` varchar(350) NOT NULL,
  `b_title` text NOT NULL,
  `b_handle` text NOT NULL,
  `b_tags` text NOT NULL,
  `b_json` text NOT NULL,
  PRIMARY KEY (`id`)
)");

mysqli_query("CREATE TABLE IF NOT EXISTS `html_settings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `shop` varchar(350) NOT NULL,
  `top_text` text NOT NULL,
  `html_before_list` text NOT NULL,
  `html_after_list` text NOT NULL,
  `html_before_post` text NOT NULL,
  `html_after_post` text NOT NULL,
  `no_char_post` bigint(11) NOT NULL,
  `custom_style` text NOT NULL,
  `created_date` datetime NOT NULL,
  `updated_date` datetime NOT NULL,
  PRIMARY KEY (`id`)
)");

mysqli_query("CREATE TABLE IF NOT EXISTS `related_settings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `shop` varchar(350) NOT NULL,
  `related_to` enum('0','1','2','3') NOT NULL,
  `no_post_display` int(11) NOT NULL,
  `handle_rel_post` enum('0','1','2') NOT NULL,
  `custom_msg` text NOT NULL,
  `auto_add_post` enum('0','1') NOT NULL,
  `blogs` text NOT NULL,
  `show_same_btype` enum('0','1') NOT NULL,
  `ex_tags` text NOT NULL,
  `ex_ids` text NOT NULL,
  `ex_frmpool_id` text NOT NULL,
  `blog_status` varchar(250) NOT NULL,
  `created_date` datetime NOT NULL,
  `updated_date` datetime NOT NULL,
  PRIMARY KEY (`id`)
)");
*/
?>
