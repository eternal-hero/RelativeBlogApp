<?php
require_once("config.php");

require 'shopifyclient.php';
require 'vendor/autoload.php';
use sandeepshetty\shopify_api;

if (isset($_GET['logout']) && $_GET['logout'] == 'true') {
	session_unset();
}
// print_r($_GET);
// exit;

if((isset($_POST['addShop']) && $_POST['shop'] != "") || isset($_GET['code']) || (isset($_SESSION['token']) && $_SESSION['token'] != '') || isset($_GET['mode'])  || (isset($_GET['shop']) && $_GET['shop'] != '' ) ) {

	// $shop = ($_REQUEST['shop'] != '')?$_REQUEST['shop']:$_SESSION['shop'];

	if(isset($_POST['shop'])){
		$shop = $_POST['shop'];
	}else{
		$shop = $_GET['shop'];
	}
	// echo $shop;
	// exit;
	$select_sql = "SELECT `id`,`token`,`payment_status`, `app_status` FROM `app` WHERE `shop` = '".$shop."' AND (`app_status` = 'installed') AND (`payment_status` = 'free' OR `payment_status` = 'accepted') ORDER BY `id` DESC LIMIT 1";
	$res = mysqli_query($db_obj, $select_sql);
	if (mysqli_num_rows($res) > 0) {
		$result = mysqli_fetch_assoc($res);

		try {
			$sc = new ShopifyClient($shop, $result['token'], SHOPIFY_API_KEY, SHOPIFY_SECRET);
			$shopdetails = $sc->call('GET', "/admin/api/".getYear()."/shop.json");
			header("Location: ".SITE_URL."/admin/home.php?shop=".$shop);
			exit;
		} catch (\Exception $e) {
			$shopifyClient = new ShopifyClient($shop, "", SHOPIFY_API_KEY, SHOPIFY_SECRET);
			$scopes = urlencode(SHOPIFY_SCOPE);
			$ruri = 'https://www.digitaldarts.com.au/relatedposts/';
			$install_url = "https://".$shop."/admin/oauth/authorize?client_id=" .SHOPIFY_API_KEY."&scope=".$scopes."&redirect_uri=".urlencode($ruri);
			header("Location: ".$install_url);
			exit;
		}

}

	if (isset($_GET['code'])) {

		$select_sql = "SELECT `id`, `payment_status` FROM `app` WHERE `shop` = '".$_GET['shop']."' ORDER BY `id` DESC LIMIT 1";
		$res = mysqli_query($db_obj, $select_sql);

		if (mysqli_num_rows($res) > 0) {
			$result = mysqli_fetch_assoc($res);

			$shopifyClient = new ShopifyClient($_GET['shop'], "", SHOPIFY_API_KEY, SHOPIFY_SECRET);
			$access_token = $shopifyClient->getAccessToken($_GET['code']);

			session_unset();

			$_SESSION['token'] = $access_token;
			if ($_SESSION['token'] != '') {
				$_SESSION['shop'] = $_GET['shop'];
				$update_sql = "UPDATE `app` SET `code` = '".$_GET['code']."', `token` = '".$_SESSION['token']."', `payment_status` = 'pending', created_date = '".date('Y-m-d H:i:s')."' WHERE `id` = '".$result['id']."'";
				mysqli_query($db_obj, $update_sql);

				$delete_all_other_entries = "DELETE FROM `app` WHERE `id` != '".$result['id']."' AND `shop` = '".$_GET['shop']."'";
				mysqli_query($db_obj, $delete_all_other_entries);
			}
		} else {
			$error_message = "Something went wrong, Please try after sometime.";
		}

		header("Location: index.php");
		exit;

	} elseif (isset($_POST['shop']) || (isset($_GET['shop']) && !isset($_GET['c_id']))) {

		$shop = isset($_POST['shop']) ? $_POST['shop'] : $_GET['shop'];
	     $check_sql = "SELECT `id`, `payment_status`, `app_status` FROM `app` WHERE `shop` = '".$shop."' AND `code` != '' AND `token` != ''";
	     #echo $check_sql; exit;
		$chk_res = mysqli_query($db_obj, $check_sql);
		if (mysqli_num_rows($chk_res) > 0) {
			$result = mysqli_fetch_assoc($chk_res);
			if (($result['payment_status'] == 'accepted' || $result['payment_status'] == 'free')  && $result['app_status'] == 'installed') {
				header("Location: widget.php?shop=".$_GET['shop']);
				exit;
			}/* elseif ($result['payment_status'] == 'declined') {
				header("Location: index.php?p=0");
				exit;
			}*/
		}

		$ins_sql = "INSERT INTO `app` (`shop`, `shop_domain`, `sync_taken`, `snippet_status`, `code`, `token`, `app_status`, `status_change_date`,`created_date`,`store_metafield_id`) VALUES ('".$_REQUEST['shop']."', '', '0', '0', '', '', '','".date('Y-m-d H:i:s')."','".date('Y-m-d H:i:s')."','')";
		$shop_id = mysqli_query($db_obj, $ins_sql);
		$shopifyClient = new ShopifyClient($shop, "", SHOPIFY_API_KEY, SHOPIFY_SECRET);
		$scopes = urlencode(SHOPIFY_SCOPE);
		$ruri = 'https://www.digitaldarts.com.au/relatedposts/';
		$install_url = "https://".$shop."/admin/oauth/authorize?client_id=" .SHOPIFY_API_KEY."&scope=".$scopes."&redirect_uri=".urlencode($ruri);
		header("Location: ".$install_url);
		exit;
	}

	if ((isset($_REQUEST['shop']) && $_REQUEST['shop'] != '') || (isset($_SESSION['shop']) && $_SESSION['shop'] != '')) {

		if(isset($_SESSION['shop'])){
			$shop = $_SESSION['shop'];
		}else{
			$shop = $_REQUEST['shop'];
		}

		$select_sql = "SELECT `id`,`token`,`payment_status`, `app_status` FROM `app` WHERE `shop` = '".$shop."' ORDER BY `id` DESC LIMIT 1";
		$res = mysqli_query($db_obj, $select_sql);
		if (mysqli_num_rows($res) > 0) {
			$res_arr = mysqli_fetch_assoc($res);
			$token = $res_arr['token'];
			$payment_status = $res_arr['payment_status'];
			$app_status = $res_arr['app_status'];
			$id = $res_arr['id'];
		}

		if (($payment_status == 'accepted' || $payment_status == 'free') && $app_status == 'installed') {
			try {
				$sc = new ShopifyClient($shop, $token, SHOPIFY_API_KEY, SHOPIFY_SECRET);
				$shopdetails = $sc->call('GET', "/admin/api/".getYear()."/shop.json");
				header("Location: ".SITE_URL."/admin/home.php?shop=".$shop);
				exit;
			} catch (\Exception $e) {
				$shopifyClient = new ShopifyClient($shop, "", SHOPIFY_API_KEY, SHOPIFY_SECRET);
				$scopes = urlencode(SHOPIFY_SCOPE);
				$ruri = 'https://www.digitaldarts.com.au/relatedposts/';
				$install_url = "https://".$shop."/admin/oauth/authorize?client_id=" .SHOPIFY_API_KEY."&scope=".$scopes."&redirect_uri=".urlencode($ruri);
				header("Location: ".$install_url);
				exit;
			}


			exit;
		}

		if ($_SESSION['token'] != '') {
			$token = $_SESSION['token'];
		}

		$sc = new ShopifyClient($shop, $token, SHOPIFY_API_KEY, SHOPIFY_SECRET);
		$shop_resp = $sc->call('GET', "/admin/api/".getYear()."/shop.json");
		$domain = preg_replace('/^www\./', '', $shop_resp['domain']);
		$app_update_sql = "UPDATE `app` SET `shop_domain` = '".$domain."' WHERE `id` = '".$id."'";
		mysqli_query($db_obj, $app_update_sql);

		$status_update_sql = "UPDATE `app` SET `app_status` = 'installed' WHERE `id` = '".$id."'";
		mysqli_query($db_obj, $status_update_sql);

  	header("location: ".SITE_URL."/install.php?shop=".$shop);
	}
} else {
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
     <head>
          <title>Add Your Shop</title>
          <meta http-equiv="content-type" content="text/html; charset=utf-8" />
          <meta http-equiv="content-language" content="en" />
          <link href="style.css" rel="stylesheet" type="text/css" />
          <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
	</head>
	<body>

<?php
      if(isset($_POST['addShop']) && ($_POST['shop'] == "" && $_GET['shop'] == "")){
            echo "<center style='color:red;'>Please enter shop name.</center>";
      }
      if(isset($_GET['p']) && $_GET['p'] == 0){
            echo "<center style='color:red;'>Please contact us at jaron.smith2006@gmail.com to get this app installed.</center>";
      }
      if(isset($_GET['a']) && $_GET['a'] == 0){
            echo "<center style='color:red;'>You already have installed this app.</center>";
      }
?>
          <div class="span24">
              <div class="error" style='color:red;display: none;text-align: center;'>Please enter shop name.</div>
            <div id="app-install">

                <div class="clearfix" id="visual-install-details">
                    <div class="oauth-app-icon">
                        <img id="app-logo" src="image/shopify-app-logo.png" style="height: 63px;"/>
                        <p><?=APP_NAME;?></p>
                    </div>

                    <div class="connect">
                        <p class="readwrite"></p>
                    </div>

                    <div class="shopify-icon">
                        <img width="60" height="60" id="shopify-logo" src="image/shopify-app-logo.png" />
                        <p>Your Store</p>
                    </div>
                </div>

                <h2>You're about to install <span><?=APP_NAME;?></span></h2>

                <p class="app_status">This application will be able to access and modify your store data.</p>

                <form method="POST">
                    <label for="shop">Enter your shop URL:</label>
                    <input type="text" id="shop" name="shop" value="" placeholder="examplestore.com"/>
                    <input type="submit" id="addShop" name="addShop" value="Install" class="btn primary"/>
                </form>

                <script type="text/javascript">
                    function extractDomain(url) {
                        var domain;
                        //find & remove protocol (http, ftp, etc.) and get domain
                        if (url.indexOf("://") > -1) {
                            domain = url.split('/')[2];
                        }
                        else {
                            domain = url.split('/')[0];
                        }

                        //find & remove port number
                        domain = domain.split(':')[0];

                        return domain;
                    }
                    $("form").submit(function(){
                        $(".error").text("").hide();
                        if($("#shop").val() == ""){
                           $(".error").text("Please enter shop name.").show();
                            return false;
                        }
                        if(extractDomain($("#shop").val())){
                            var domain = extractDomain($("#shop").val());
                            if(domain.indexOf('myshopify.com') > -1){
                                $("#shop").val(domain);
                               // console.log("valid");
                            }else{
                                $(".error").text("Please enter valid shop name.").show();
                                return false;
                            }

                        }
                    });
                </script>
            </div>
        </div>

</body>
</html>
<?php
}
exit;
?>
