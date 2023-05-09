<?php
session_start();
error_reporting(E_ALL);
//Configuration
require 'config.php';

require 'shopifyclient.php';
require 'vendor/autoload.php';

use sandeepshetty\shopify_api;

	$webhookContent = "";
	$webhook = fopen('php://input' , 'rb'); 
	  while (!feof($webhook))
	{ 
		$webhookContent .= fread($webhook, 4096);  
	} 
	fclose($webhook);
	error_log($webhookContent);
	//Webhook ends

	//$headers = apache_request_headers();
	$siteURL = $_REQUEST['myshop'];
	
	/*  mysqli_query($connect,"INSERT INTO webhook (`valuess`, `siteurl`)
			VALUES ('$webhookContent', '$siteURL')"); */
	//die();
	// blog_list //
	$delete1 = mysqli_query($db_obj, "DELETE FROM blog_list WHERE shop = '".$siteURL."'");
	
	// html_settings //
	$delete2 = mysqli_query($db_obj, "DELETE FROM html_settings WHERE shop = '".$siteURL."'");
	
	// related_settings //
	$delete3 = mysqli_query($db_obj, "DELETE FROM related_settings WHERE shop = '".$siteURL."'");
	
	// app //
	$delete4 = mysqli_query($db_obj, "DELETE FROM app WHERE shop = '".$siteURL."'");
	
	$file_name = explode(".",$siteURL);
	
	 @unlink("article_html_files/".$file_name[0]."_relatedblogs.html");
	
	// End Get the site url and shops // 		
?>