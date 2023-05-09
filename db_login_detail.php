<?php
//error_reporting(E_ALL ^ E_NOTICE ^ E_DEPRECATED ^ E_WARNING);

// $db_obj = mysqli_connect("127.0.0.1","easythe_repous", "(d2@oApqdg6#", "easythe_shopifyrp1");
$db_obj = mysqli_connect('localhost','digit@digitaldar', ',e%.IHcO', 'digitald4_shopify');
define('DOMAIN_NAME', 'www.digitaldarts.com.au/relatedposts');
define('SITE_URL', 'https://www.digitaldarts.com.au/relatedposts');
mysqli_set_charset($db_obj,"utf8");

if (!$db_obj) {
	echo "Failed to connect to MySQL: " . mysqli_connect_error();
}

define('SHOPIFY_API_KEY', '9df3546f24fa4568ef0ab74fad97f109');
define('SHOPIFY_SECRET', '5e2ae615f0da0efcc8478bfe0ec83a93');
define('SHOPIFY_SCOPE', 'write_content,write_themes');
define('APP_NAME',"Related Blog Posts");

function loopAndFind($array, $index, $search){
	$returnArray = array();
	if(is_array($array) || is_object($array)){
		foreach($array as $k=>$v){
			if($v[$index] == $search){
				$returnArray[] = $v;
			}
		}
	}
	return $returnArray;
}
function getYear() {
  $curr_date = date('m/d/Y h:i:s a', time());
  $curr_month = date('m');
  $curr_year = date('Y');
  $api_arr = ['-01', '-04', '-07', '-10'];
  $api_end = '';

  if($curr_month == 1) {
    $api_end = ($curr_year - 1) . $api_arr[3];
  } else if($curr_month > 1 && $curr_month <= 4) {
    $api_end = $curr_year . $api_arr[0];
  } else if($curr_month > 4 && $curr_month <= 7) {
    $api_end = $curr_year . $api_arr[1];
  } else if($curr_month > 7 && $curr_month <= 10) {
    $api_end = $curr_year . $api_arr[2];
  } else if($curr_month > 10 && $curr_month <= 12) {
    $api_end = $curr_year . $api_arr[3];
  }
  return $api_end;
}
function base64url_encode($data)
{
		return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
}

function base64url_decode($data)
{
		return base64_decode(str_pad(strtr($data, '-_', '+/'), strlen($data) % 4, '=', STR_PAD_RIGHT));
}

function checkSignature($token)
 {
		 $parts = explode('.', $token);
		 $signature = array_pop($parts);
		 $check = implode('.', $parts);
		 $shop = null;
		 $body = json_decode(base64url_decode($parts[1]));
		 if (isset($body->dest)) {
				 $url = parse_url($body->dest);
				 $shop = isset($url['host']) ? $url['host'] : null;
		 }
		 $secret = SHOPIFY_SECRET;
		 $hmac = hash_hmac('sha256', $check, $secret, true);
		 $encoded = base64url_encode($hmac);
		 return $encoded === $signature;
 }

function IsValidRequest()
{
	$return = ['code'=>200,"msg"=>"Success Request"];
	$apiKey = SHOPIFY_API_KEY;
	$headers = apache_request_headers();
	if(isset($headers['Authorization'])){
			$token = str_replace('Bearer ', '', $headers['Authorization']);
			if (!checkSignature($token)) {
				$return = ['code'=>201,"msg"=>"Unable to verify signature"];
				return $return;
				exit;
			}
			$parts = explode('.', $token);
			$body = json_decode(base64url_decode($parts[1]));
			$now = time();
			// if($now > $body->exp || $body->nbf > $now){
			// 	$return = ['code'=>201,"msg"=>"Token Expired"];
			// 	return $return;
			// 	exit;
			// }

			if(strpos($body->iss,'myshopify.com') == false || strpos($body->dest, 'myshopify.com') == false) {
				$return = ['code'=>201,"msg"=>"Requestor not Verified"];
				return $return;
				exit;
			}
			if ($apiKey != $body->aud) {
				$return = ['code'=>201,"msg"=>"Not Requested from the My App"];
				return $return;
				exit;
			}

			$return = $return;
			return $return;
	}else{
		$return = ['code'=>201,"msg"=>"Not Requested from Shopify admin"];
		return $return;
	}
}


?>
