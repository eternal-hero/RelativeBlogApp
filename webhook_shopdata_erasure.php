<?php
//session_start();
error_reporting(E_ALL);
//Configuration
require 'config.php';

//Webhook Start
 $webhookContent1 = "";
 $webhook = fopen('php://input' , 'rb'); 
  while (!feof($webhook))
{ 
 $webhookContent1 .= fread($webhook, 4096);  
} 
fclose($webhook);

$headers = apache_request_headers();

if(isset($_REQUEST['shop_domain']) && $_REQUEST['shop_domain'] != ''){
	$siteURL = $_REQUEST['shop_domain'];
}else{
	$siteURL = $headers['X-Shopify-Shop-Domain']; 
}

// Decode the JSON //
	$webhook_Details = json_encode(json_decode($webhookContent1, true));

	//mysqli_query($db_obj, "INSERT INTO webhook_det SET content = '$webhook_Details', siteURL = '$siteURL'"); 

	
	//$siteURL = $_REQUEST['shop_domain'];
	
	// blog_list //
	$shop_deleted_detail = "SELECT * FROM `shop_deleted` where status = 0";
	$shop_deleted_details = mysqli_query($db_obj,$shop_deleted_detail);
    if (mysqli_num_rows($shop_deleted_details) > 0) 
    {
        while($article_res = mysqli_fetch_assoc($shop_deleted_details))
            {
                $start = $article_res['created_at'];
                $start_val = strtotime($start);
                $end = date('Y-m-d h:i:s a', time());
                $end_val = strtotime($end);
                $days_between = $end_val - $start_val;
                $diff_date = $days_between/3600;
                if($diff_date > 48)
                {
                    $delete_article = mysqli_query($db_obj, "DELETE FROM artical_list WHERE `shop_name` = '".$article_res['shop_name']."'");
                    $update = mysqli_query($db_obj, "UPDATE shop_deleted SET status= 1 WHERE id='" . $article_res['id'] . "'");  
                }
            }
            
    }
	$delete1 = mysqli_query($db_obj, "DELETE FROM blog_list WHERE shop = '".$siteURL."'");
	
	// html_settings //
	$delete2 = mysqli_query($db_obj, "DELETE FROM html_settings WHERE shop = '".$siteURL."'");
	
	// related_settings //
	$delete3 = mysqli_query($db_obj, "DELETE FROM related_settings WHERE shop = '".$siteURL."'");
	
	// app //
	$delete4 = mysqli_query($db_obj, "DELETE FROM app WHERE shop = '".$siteURL."'");
	
	// file delete //
	$file_name = explode(".",$siteURL);
	
	 @unlink("article_html_files/".$file_name[0]."_relatedblogs.html");
	
	// End Get the site url and shops // 	
	
	http_response_code(200);
?>