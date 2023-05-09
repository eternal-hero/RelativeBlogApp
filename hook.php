<?php
// require_once("config.php");
$rootDir = $_SERVER["DOCUMENT_ROOT"];
include  $rootDir."/relatedposts/config.php";
global $db_obj;
$to      = 'josh@digitaldarts.com.au';
$subject = APP_NAME.' : Webbook Called';
$message = APP_NAME.' : Webbook Called';
$headers = 'From: josh@digitaldarts.com.au' . "\r\n" .
    'X-Mailer: PHP/' . phpversion();

$entityBody = file_get_contents('php://input');
$arr = json_decode($entityBody);
$store_domain = $arr->myshopify_domain;

// $update_sql = "UPDATE `app` SET `app_status` = 'uninstalled', status_change_date = '".date('Y-m-d H:i:s')."' WHERE `shop` = '".$store_domain."'";
// mysqli_query($db_obj, $update_sql);
// mysqli_query($db_obj, "UPDATE `related_settings` SET `install_status` = '0' where `shop` = '".$store_domain."'");
//

// $delete1 = mysqli_query($db_obj, "DELETE FROM blog_list WHERE shop = '".$store_domain."'");
//
// // html_settings //
// $delete2 = mysqli_query($db_obj, "DELETE FROM html_settings WHERE shop = '".$store_domain."'");
//
// // related_settings //
// $delete3 = mysqli_query($db_obj, "DELETE FROM related_settings WHERE shop = '".$store_domain."'");
//
// // app //
$shop_detail = "SELECT `id`,`shop` FROM `app` WHERE shop = '".$store_domain."'";
$shop_details = mysqli_query($db_obj,$shop_detail);
$result = mysqli_fetch_assoc($shop_details);
if ($result > 0)
{
	mysqli_query($db_obj, "INSERT INTO shop_deleted SET shop_name = '".$result['shop']."', shop_id = '" . $result['id'] . "'");
}
$delete4 = mysqli_query($db_obj, "DELETE FROM app WHERE shop = '".$store_domain."'");

$file_name = explode(".",$store_domain);

 @unlink("article_html_files/".$file_name[0]."_relatedblogs.html");




#mail($to, $subject, $entityBody, $headers);
exit;

?>
