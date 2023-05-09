<?php

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


?>
